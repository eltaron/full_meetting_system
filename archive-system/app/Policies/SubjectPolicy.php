<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

/**
 * صلاحيات المستندات المؤرشفة:
 * - الحذف والاسترجاع والحذف النهائي: مدير المنظومة فقط
 * - إلغاء التفعيل: أي موظف على مستندات إدارته (مع ذكر السبب)
 * - إعادة التفعيل: مدير الإدارة أو مدير المنظومة
 * - التعديل: على المستندات السارية فقط داخل نطاق المستخدم
 */
class SubjectPolicy
{
    public function before(User $user): ?bool
    {
        if (! $user->is_active) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->department_id !== null;
    }

    public function view(User $user, Subject $subject): bool
    {
        return $subject->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->department_id !== null;
    }

    public function update(User $user, Subject $subject): bool
    {
        if ($subject->trashed()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $subject->is_active && $subject->isVisibleTo($user);
    }

    public function deactivate(User $user, Subject $subject): bool
    {
        return $subject->is_active && ! $subject->trashed() && $subject->isVisibleTo($user);
    }

    public function activate(User $user, Subject $subject): bool
    {
        return ! $subject->is_active
            && ! $subject->trashed()
            && ($user->isAdmin() || ($user->isManager() && $subject->isVisibleTo($user)));
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Subject $subject): bool
    {
        return $user->isAdmin();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
        return $user->isAdmin();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
