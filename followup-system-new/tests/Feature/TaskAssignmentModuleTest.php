<?php

namespace Tests\Feature;

use App\Models\SignatureOfficer;
use App\Models\TaskAssignment;
use App\Models\TaskEntity;
use App\Models\TaskSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TaskAssignmentModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete'] as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);

        $permissions = match ($role) {
            'admin', 'assignment_manager' => ['tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete'],
            default => ['tasks.view'],
        };

        $user->syncPermissions($permissions);

        return $user;
    }

    private function seedLookups(): array
    {
        $source = TaskSource::create(['name' => 'مكتب المتابعة', 'is_active' => true]);
        $entity = TaskEntity::create(['name' => 'مركز كفر الشيخ', 'type' => 'مركز', 'is_active' => true]);

        return [$source, $entity];
    }

    public function test_guest_is_redirected_away_from_tasks(): void
    {
        $this->get('/tasks')->assertRedirect('/login');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_dashboard_renders_for_authenticated_user(): void
    {
        $user = $this->makeUser('staff');

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_tasks_index_renders(): void
    {
        $user = $this->makeUser('staff');

        $this->actingAs($user)->get(route('tasks.index'))->assertOk()->assertViewIs('tasks.index');
    }

    public function test_tasks_create_form_renders_for_assignment_manager(): void
    {
        $user = $this->makeUser('assignment_manager');

        $this->actingAs($user)->get(route('tasks.create'))->assertOk();
    }

    public function test_any_authenticated_user_can_create_task(): void
    {
        $user = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        // ملاحظة: الـ Policy الحالي يسمح لـ staff بالإنشاء (لازم نتأكد من المقصود)
        $this->assertTrue($user->can('create', TaskAssignment::class));
    }

    public function test_assignment_manager_can_create_task_and_notify_assignee(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $response = $this->actingAs($manager)->post(route('tasks.store'), [
            'received_date' => '2026-09-01',
            'document_type' => 'وارد',
            'source_id' => $source->id,
            'entity_id' => $entity->id,
            'assignee_id' => $staff->id,
            'description' => 'متابعة حالة البلاغ',
            'priority' => 'عاجل',
            'deadline' => '2026-09-15',
        ]);

        $response->assertRedirect(route('tasks.index'));

        $task = TaskAssignment::firstOrFail();

        $this->assertSame('لم يبدأ', $task->status);
        $this->assertStringStartsWith('TA-'.now()->year.'-', $task->task_number);
        $this->assertDatabaseHas('task_status_logs', ['task_assignment_id' => $task->id, 'field_name' => 'status']);
        $this->assertCount(1, $staff->notifications);
    }

    public function test_staff_cannot_delete_task(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $task = TaskAssignment::create([
            'task_number' => 'TA-2026-00005', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'حذف', 'priority' => 'عادي', 'deadline' => '2026-09-10',
        ]);

        $this->actingAs($staff)->delete(route('tasks.destroy', $task))->assertForbidden();
        $this->actingAs($manager)->delete(route('tasks.destroy', $task))->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('task_assignments', ['id' => $task->id]);
    }

    public function test_store_validation_rejects_deadline_before_received_date(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $this->actingAs($manager)->post(route('tasks.store'), [
            'received_date' => '2026-09-10',
            'document_type' => 'وارد',
            'source_id' => $source->id,
            'entity_id' => $entity->id,
            'assignee_id' => $staff->id,
            'description' => 'وصف',
            'priority' => 'عاجل',
            'deadline' => '2026-09-01',
        ])->assertSessionHasErrors('deadline');
    }

    public function test_staff_sees_only_own_tasks_and_manager_sees_all(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staffA = $this->makeUser('staff');
        $staffB = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        TaskAssignment::create([
            'task_number' => 'TA-2026-00001', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staffA->id, 'created_by' => $manager->id,
            'description' => 'أ', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        TaskAssignment::create([
            'task_number' => 'TA-2026-00002', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staffB->id, 'created_by' => $manager->id,
            'description' => 'ب', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        $this->assertSame(1, $staffA->visibleTasksQuery()->count());
        $this->assertSame(2, $manager->visibleTasksQuery()->count());
    }

    public function test_delay_days_and_overdue_flags(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $late = TaskAssignment::create([
            'task_number' => 'TA-2026-00003', 'received_date' => '2026-08-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'تأخر', 'priority' => 'عاجل', 'deadline' => '2026-08-10',
            'status' => 'جاري التنفيذ',
        ]);

        $this->assertGreaterThan(0, $late->delay_days);
        $this->assertTrue($late->is_overdue);

        $done = TaskAssignment::create([
            'task_number' => 'TA-2026-00004', 'received_date' => '2026-08-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'خلص', 'priority' => 'عاجل', 'deadline' => '2026-08-10',
            'status' => 'تم التنفيذ', 'response_date' => '2026-08-09',
        ]);

        $this->assertSame(0, $done->delay_days);
        $this->assertFalse($done->is_overdue);
    }

    public function test_task_sources_and_entities_pages_render(): void
    {
        $user = $this->makeUser('admin');
        $this->seedLookups();

        $this->actingAs($user)->get(route('task-sources.index'))->assertOk();
        $this->actingAs($user)->get(route('task-sources.create'))->assertOk();
        $this->actingAs($user)->get(route('task-entities.index'))->assertOk();
        $this->actingAs($user)->get(route('task-entities.create'))->assertOk();
    }

    public function test_profile_page_renders_with_real_user_data(): void
    {
        $user = $this->makeUser('supervisor');
        $user->update(['phone_number' => '01012345678', 'job_title' => 'مشرف مركز']);

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('01012345678')
            ->assertSee('مشرف مركز')
            ->assertSee($user->name);
    }

    public function test_tasks_index_shows_user_tasks_data(): void
    {
        $user = $this->makeUser('staff');
        $other = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $mine = TaskAssignment::create([
            'task_number' => 'TA-2026-00010', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $user->id, 'created_by' => $other->id,
            'description' => 'تكليف خاص بي', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        TaskAssignment::create([
            'task_number' => 'TA-2026-00011', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $other->id, 'created_by' => $other->id,
            'description' => 'تكليف حد تاني', 'priority' => 'عادي', 'deadline' => '2026-09-10',
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee($mine->task_number)
            ->assertSee($entity->name)
            ->assertDontSee('TA-2026-00011');
    }

    public function test_task_show_page_renders_with_log(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $task = TaskAssignment::create([
            'task_number' => 'TA-2026-00020', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'تفاصيل التكليف', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        $this->actingAs($manager)->get(route('tasks.show', $task))->assertOk()->assertSee('تفاصيل التكليف');
        $this->actingAs($staff)->get(route('tasks.show', $task))->assertOk();
    }

    public function test_task_edit_page_renders_for_assignee(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $task = TaskAssignment::create([
            'task_number' => 'TA-2026-00030', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'تعديل', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        $this->actingAs($staff)->get(route('tasks.edit', $task))->assertOk();
        $this->actingAs($manager)->get(route('tasks.edit', $task))->assertOk();
    }

    public function test_staff_can_only_update_status_fields(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();

        $task = TaskAssignment::create([
            'task_number' => 'TA-2026-00040', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'تحديث', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        // الموظف يحدّث الحالة بس
        $this->actingAs($staff)->put(route('tasks.update', $task), [
            'status' => 'تم التنفيذ',
            'completion_percentage' => 100,
        ])->assertRedirect(route('tasks.index'));

        $task->refresh();
        $this->assertSame('تم التنفيذ', $task->status);
        $this->assertSame(100, $task->completion_percentage);
        $this->assertDatabaseHas('task_status_logs', [
            'task_assignment_id' => $task->id,
            'field_name' => 'status',
            'new_value' => 'تم التنفيذ',
        ]);

        // الموظف لا يقدر يغيّر الأولوية: الحقل مش موجود في قواعد التحقق
        // فـ validated() بيتجاهله تمامًا (مش error، مجرد تجاهل)
        $this->actingAs($staff)->put(route('tasks.update', $task), [
            'priority' => 'عادي',
            'status' => 'تم التنفيذ',
        ])->assertRedirect(route('tasks.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('عاجل', $task->fresh()->priority, 'priority should be unchanged for staff');
        $this->assertDatabaseMissing('task_status_logs', [
            'task_assignment_id' => $task->id,
            'field_name' => 'priority',
        ]);

        // الموظف كمان مش بيقدر يغيّر المسؤول المكلف
        $newStaff = $this->makeUser('staff');
        $this->actingAs($staff)->put(route('tasks.update', $task), [
            'status' => 'تم التنفيذ',
            'assignee_id' => $newStaff->id,
        ])->assertRedirect(route('tasks.index'));

        $this->assertSame($staff->id, $task->fresh()->assignee_id, 'assignee should be unchanged for staff');
    }

    public function test_notification_endpoints(): void
    {
        $user = $this->makeUser('staff');

        $this->actingAs($user)->get(route('task-notifications.recent'))
            ->assertOk()
            ->assertJsonStructure(['unread_count', 'notifications']);

        $this->actingAs($user)->post(route('task-notifications.mark-all-read'))
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_manager_can_add_signature_to_task(): void
    {
        $manager = $this->makeUser('assignment_manager');
        $staff = $this->makeUser('staff');
        [$source, $entity] = $this->seedLookups();
        $officer = SignatureOfficer::create(['name' => 'مسئول التأشيرة', 'is_active' => true]);

        $task = TaskAssignment::create([
            'task_number' => 'TA-2026-00050', 'received_date' => '2026-09-01',
            'source_id' => $source->id, 'entity_id' => $entity->id,
            'assignee_id' => $staff->id, 'created_by' => $manager->id,
            'description' => 'تأشيرة', 'priority' => 'عاجل', 'deadline' => '2026-09-10',
        ]);

        $this->actingAs($manager)->post(route('tasks.signatures.store', $task), [
            'signature_officer_id' => $officer->id,
            'signature_text' => 'تم الاطلاع والموافقة',
            'signature_date' => '2026-09-05',
        ])->assertRedirect(route('tasks.edit', $task));

        $this->assertDatabaseHas('task_signatures', [
            'task_assignment_id' => $task->id,
            'signature_officer_id' => $officer->id,
            'signature_text' => 'تم الاطلاع والموافقة',
        ]);

        $this->actingAs($manager)->get(route('tasks.edit', $task))
            ->assertOk()
            ->assertSee('إضافة تأشيرة')
            ->assertSee('تم الاطلاع والموافقة');
    }
}
