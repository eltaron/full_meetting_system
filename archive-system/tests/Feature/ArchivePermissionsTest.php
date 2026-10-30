<?php

namespace Tests\Feature;

use App\Enums\Confidentiality;
use App\Enums\UserRole;
use App\Filament\Admin\Pages\DocumentLifecycle;
use App\Filament\Admin\Resources\SubjectResource as AdminSubjectResource;
use App\Filament\Staff\Resources\ActivityLogResource as StaffActivityLogResource;
use App\Filament\Staff\Resources\SubjectResource as StaffSubjectResource;
use App\Filament\Staff\Resources\SubjectResource\Pages\CreateSubject;
use App\Filament\Staff\Resources\SubjectResource\Pages\ListSubjects;
use App\Filament\Staff\Resources\TeamResource;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Subject;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * اختبارات الصلاحيات: تُشغّل على قاعدة بيانات اختبار منفصلة
 * DB_CONNECTION=mysql DB_DATABASE=archive_system_test php artisan test
 */
class ArchivePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private Department $parent;
    private Department $child;
    private Department $other;
    private User $admin;
    private User $manager;
    private User $employee;
    private User $childEmployee;
    private User $otherEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parent = Department::create(['name' => 'الشئون المالية']);
        $this->child = Department::create(['name' => 'الموازنة', 'parent_id' => $this->parent->id]);
        $this->other = Department::create(['name' => 'الشئون القانونية']);

        $this->admin = $this->makeUser(UserRole::Admin, null);
        $this->manager = $this->makeUser(UserRole::Manager, $this->parent);
        $this->employee = $this->makeUser(UserRole::Employee, $this->parent);
        $this->childEmployee = $this->makeUser(UserRole::Employee, $this->child);
        $this->otherEmployee = $this->makeUser(UserRole::Employee, $this->other);
    }

    private function makeUser(UserRole $role, ?Department $department): User
    {
        return User::factory()->create([
            'username' => fake()->unique()->userName(),
            'role' => $role,
            'department_id' => $department?->id,
        ]);
    }

    private function makeSubject(User $by, array $attributes = []): Subject
    {
        $this->actingAs($by);

        return Subject::create([
            'title' => 'مستند ' . fake()->word(),
            'document_date' => now()->toDateString(),
            'direction' => 'incoming',
            'document_type' => 'letter',
            'external_party' => 'وزارة التنمية المحلية',
            'user_id' => $by->id,
            'department_id' => $by->department_id,
            ...$attributes,
        ]);
    }

    private function staffPanel(User $user): void
    {
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('staff'));
    }

    // ===== الدخول للوحات =====

    public function test_panel_access_is_restricted_by_role(): void
    {
        $this->actingAs($this->employee)->get('/admin')->assertForbidden();
        $this->actingAs($this->manager)->get('/admin')->assertForbidden();
        $this->actingAs($this->employee)->get('/staff')->assertOk();
        $this->actingAs($this->manager)->get('/staff')->assertOk();
        $this->actingAs($this->admin)->get('/admin')->assertOk();
        $this->actingAs($this->admin)->get('/staff')->assertForbidden();
    }

    public function test_deactivated_account_cannot_access_any_panel(): void
    {
        $this->employee->update(['is_active' => false]);

        $this->actingAs($this->employee)->get('/staff')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/staff')->assertRedirect('/staff/login');
        $this->get('/print-user/' . $this->employee->id)->assertRedirect('/staff/login');
    }

    public function test_only_admin_can_print_user_card(): void
    {
        $this->actingAs($this->employee)->get('/print-user/' . $this->employee->id)->assertForbidden();
        $this->actingAs($this->manager)->get('/print-user/' . $this->employee->id)->assertForbidden();
        $this->actingAs($this->admin)->get('/print-user/' . $this->employee->id)->assertOk();
    }

    // ===== الحذف وإلغاء التفعيل =====

    public function test_staff_cannot_delete_but_can_deactivate(): void
    {
        $subject = $this->makeSubject($this->employee);
        $this->staffPanel($this->employee);

        $this->assertFalse($this->employee->can('delete', $subject));
        $this->assertFalse($this->manager->can('delete', $subject));
        $this->assertFalse($this->manager->can('forceDelete', $subject));

        Livewire::test(ListSubjects::class)
            ->assertTableActionDoesNotExist('delete')
            ->assertTableBulkActionDoesNotExist('delete')
            ->callTableAction('deactivate', $subject, ['reason' => 'تم إدخاله بالخطأ'])
            ->assertHasNoTableActionErrors();

        $subject->refresh();
        $this->assertFalse($subject->is_active);
        $this->assertSame($this->employee->id, $subject->deactivated_by);
        $this->assertNotNull(Subject::find($subject->id), 'المستند لم يُحذف');
        $this->assertDatabaseHas('activity_logs', ['subject_id' => $subject->id, 'action' => 'deactivated']);
    }

    public function test_deactivation_requires_reason(): void
    {
        $subject = $this->makeSubject($this->employee);
        $this->staffPanel($this->employee);

        Livewire::test(ListSubjects::class)
            ->callTableAction('deactivate', $subject, ['reason' => ''])
            ->assertHasTableActionErrors(['reason' => 'required']);

        $this->assertTrue($subject->refresh()->is_active);
    }

    public function test_only_manager_or_admin_can_reactivate(): void
    {
        $subject = $this->makeSubject($this->employee);
        $subject->deactivate($this->employee, 'خطأ');

        $this->assertFalse($this->employee->can('activate', $subject));
        $this->assertFalse($this->otherEmployee->can('activate', $subject));
        $this->assertTrue($this->manager->can('activate', $subject));
        $this->assertTrue($this->admin->can('activate', $subject));

        $this->staffPanel($this->manager);
        Livewire::test(ListSubjects::class)
            ->filterTable('is_active', false)
            ->callTableAction('activate', $subject);

        $this->assertTrue($subject->refresh()->is_active);
    }

    public function test_deactivated_document_cannot_be_edited_by_staff(): void
    {
        $subject = $this->makeSubject($this->employee);
        $subject->deactivate($this->employee, 'خطأ');

        $this->assertFalse($this->employee->can('update', $subject));
        $this->staffPanel($this->employee);
        $this->get(StaffSubjectResource::getUrl('edit', ['record' => $subject]))->assertForbidden();
    }

    public function test_admin_can_soft_delete_restore_and_force_delete(): void
    {
        $subject = $this->makeSubject($this->employee);
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(AdminSubjectResource\Pages\ListSubjects::class)
            ->callTableAction('delete', $subject);
        $this->assertSoftDeleted($subject);

        Livewire::test(AdminSubjectResource\Pages\ListSubjects::class)
            ->filterTable('trashed', true)
            ->callTableAction('restore', $subject->getKey());
        $this->assertNotSoftDeleted($subject);

        // الحذف النهائي متاح فقط للمستندات الموجودة في المحذوفات (خطوتين)
        Livewire::test(AdminSubjectResource\Pages\ListSubjects::class)
            ->assertTableActionHidden('forceDelete', $subject->getKey())
            ->callTableAction('delete', $subject->getKey());

        Livewire::test(AdminSubjectResource\Pages\ListSubjects::class)
            ->filterTable('trashed', true)
            ->callTableAction('forceDelete', $subject->getKey());
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);

        // أثر الحذف يبقى في سجل الحركات
        $this->assertDatabaseHas('activity_logs', ['subject_id' => $subject->id, 'action' => 'force_deleted']);
    }

    // ===== نطاق الرؤية =====

    public function test_employee_sees_only_own_department(): void
    {
        $mine = $this->makeSubject($this->employee);
        $childDoc = $this->makeSubject($this->childEmployee);
        $otherDoc = $this->makeSubject($this->otherEmployee);

        $this->staffPanel($this->employee);
        Livewire::test(ListSubjects::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$childDoc, $otherDoc]);

        $this->get(StaffSubjectResource::getUrl('view', ['record' => $otherDoc]))->assertNotFound();
        $this->get(StaffSubjectResource::getUrl('edit', ['record' => $otherDoc]))->assertNotFound();
    }

    public function test_manager_sees_department_and_sub_departments(): void
    {
        $mine = $this->makeSubject($this->employee);
        $childDoc = $this->makeSubject($this->childEmployee);
        $otherDoc = $this->makeSubject($this->otherEmployee);

        $this->staffPanel($this->manager);
        Livewire::test(ListSubjects::class)
            ->assertCanSeeTableRecords([$mine, $childDoc])
            ->assertCanNotSeeTableRecords([$otherDoc]);
    }

    public function test_top_secret_hidden_from_other_employees(): void
    {
        $secret = $this->makeSubject($this->employee, ['confidentiality' => Confidentiality::TopSecret->value]);
        $colleague = $this->makeUser(UserRole::Employee, $this->parent);

        $this->assertTrue($this->employee->can('view', $secret));
        $this->assertTrue($this->manager->can('view', $secret));
        $this->assertTrue($this->admin->can('view', $secret));
        $this->assertFalse($colleague->can('view', $secret));
    }

    // ===== صفحات مدير الإدارة =====

    public function test_team_and_activity_pages_are_for_managers_only(): void
    {
        $this->staffPanel($this->employee);
        $this->get(TeamResource::getUrl('index'))->assertForbidden();
        $this->get(StaffActivityLogResource::getUrl('index'))->assertForbidden();

        $this->staffPanel($this->manager);
        $this->get(TeamResource::getUrl('index'))->assertOk();
        $this->get(StaffActivityLogResource::getUrl('index'))->assertOk();
    }

    public function test_manager_activity_log_excludes_other_departments(): void
    {
        $mine = $this->makeSubject($this->childEmployee);
        $other = $this->makeSubject($this->otherEmployee);

        $this->staffPanel($this->manager);
        Livewire::test(StaffActivityLogResource\Pages\ListActivityLogs::class)
            ->assertCanSeeTableRecords(ActivityLog::where('subject_id', $mine->id)->get())
            ->assertCanNotSeeTableRecords(ActivityLog::where('subject_id', $other->id)->get());
    }

    // ===== المرفقات =====

    public function test_attachments_are_private_and_permission_checked(): void
    {
        $subject = $this->makeSubject($this->employee);
        $media = $subject->addMedia(UploadedFile::fake()->create('letter.pdf', 10, 'application/pdf'))
            ->toMediaCollection('archive_attachments');

        $this->assertSame('local', $media->disk);

        auth()->logout();
        $this->get(route('attachments.show', $media))->assertRedirect('/staff/login');
        $this->actingAs($this->otherEmployee)->get(route('attachments.show', $media))->assertForbidden();
        $this->actingAs($this->employee)->get(route('attachments.show', $media))->assertOk();
        $this->actingAs($this->manager)->get(route('attachments.show', $media))->assertOk();

        $this->assertDatabaseHas('activity_logs', ['subject_id' => $subject->id, 'action' => 'viewed_attachment', 'user_id' => $this->manager->id]);

        $media->delete();
    }

    // ===== الأرشفة ودورة العمل =====

    public function test_staff_create_assigns_own_department_and_archive_number(): void
    {
        $this->staffPanel($this->employee);

        Livewire::test(CreateSubject::class)
            ->fillForm([
                'title' => 'طلب ترخيص',
                'direction' => 'outgoing',
                'document_type' => 'letter',
                'document_date' => now()->toDateString(),
                'confidentiality' => 'normal',
                'source_id' => $this->other->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $subject = Subject::latest('id')->first();
        $this->assertSame($this->parent->id, $subject->department_id);
        $this->assertSame($this->employee->id, $subject->user_id);
        $this->assertSame('outgoing', $subject->direction->value);
        $this->assertMatchesRegularExpression('/^KFS-\d{4}-000001$/', $subject->archive_number);
    }

    public function test_party_is_required(): void
    {
        $this->staffPanel($this->employee);

        Livewire::test(CreateSubject::class)
            ->fillForm([
                'title' => 'بدون جهة',
                'direction' => 'incoming',
                'document_type' => 'letter',
                'document_date' => now()->toDateString(),
                'confidentiality' => 'normal',
            ])
            ->call('create')
            ->assertHasFormErrors(['source_id']);
    }

    public function test_lifecycle_tracks_create_edit_and_view(): void
    {
        $subject = $this->makeSubject($this->employee);
        $subject->update(['title' => 'عنوان معدل']);

        $this->staffPanel($this->manager);
        $this->get(StaffSubjectResource::getUrl('view', ['record' => $subject]))->assertOk();

        $actions = ActivityLog::where('subject_id', $subject->id)->pluck('action')->all();
        $this->assertEqualsCanonicalizing(['created', 'updated', 'viewed'], $actions);

        $update = ActivityLog::where('subject_id', $subject->id)->where('action', 'updated')->first();
        $this->assertSame('عنوان معدل', $update->properties['changes']['title']['new']);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->get(DocumentLifecycle::getUrl(['subject' => $subject->id]))
            ->assertOk()
            ->assertSee('عنوان معدل')
            ->assertSee($this->manager->name);
    }

    public function test_all_pages_render(): void
    {
        $subject = $this->makeSubject($this->employee);
        $subject->tags()->create(['name' => 'تراخيص']);

        $this->actingAs($this->admin);
        foreach (['/admin', '/admin/subjects', '/admin/subjects/create', "/admin/subjects/{$subject->id}",
            "/admin/subjects/{$subject->id}/edit", '/admin/document-lifecycle', "/admin/document-lifecycle?subject={$subject->id}",
            '/admin/activity-logs', '/admin/tags', '/admin/users', '/admin/users/create', "/admin/users/{$this->manager->id}/edit",
            '/admin/departments', "/admin/departments/{$this->parent->id}/edit", '/admin/general-reports'] as $url) {
            $this->get($url)->assertOk();
        }

        foreach ([$this->employee, $this->manager] as $user) {
            $this->actingAs($user);
            foreach (['/staff', '/staff/subjects', '/staff/subjects/create', "/staff/subjects/{$subject->id}",
                "/staff/subjects/{$subject->id}/edit"] as $url) {
                $this->get($url)->assertOk();
            }
        }

        $this->get('/staff/team')->assertOk();
        $this->get('/staff/activity-logs')->assertOk();
    }

    public function test_dashboard_widgets_render(): void
    {
        $this->makeSubject($this->employee);

        $this->staffPanel($this->manager);
        foreach ([\App\Filament\Staff\Widgets\SubjectStats::class, \App\Filament\Staff\Widgets\ArchivesChart::class,
            \App\Filament\Staff\Widgets\ArchiveBySourceChart::class] as $widget) {
            Livewire::test($widget)->assertOk();
        }
    }

    public function test_department_with_documents_cannot_be_deleted(): void
    {
        $this->makeSubject($this->otherEmployee);

        $this->assertFalse($this->other->isDeletable());
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->other->delete();
    }
}
