<?php

namespace Modules\Archive\Observers;

use Modules\Archive\Models\ActivityLog;
use Modules\Archive\Models\Subject;

// تسجيل كل حركة على المستندات في سجل الحركات
class SubjectObserver
{
    public function created(Subject $subject): void
    {
        ActivityLog::record('created', $subject);
    }

    public function updated(Subject $subject): void
    {
        if ($subject->wasChanged('is_active')) {
            $subject->is_active
                ? ActivityLog::record('activated', $subject)
                : ActivityLog::record('deactivated', $subject, ['reason' => $subject->deactivation_reason]);

            return;
        }

        $changes = [];

        foreach (array_keys($subject->getChanges()) as $field) {
            if (in_array($field, ['updated_at', 'deleted_at'], true)) {
                continue;
            }

            $changes[$field] = [
                'old' => self::plain($subject->getRawOriginal($field)),
                'new' => self::plain($subject->getAttributes()[$field] ?? null),
            ];
        }

        if ($changes) {
            ActivityLog::record('updated', $subject, ['changes' => $changes]);
        }
    }

    private static function plain(mixed $value): mixed
    {
        return is_string($value) ? mb_substr($value, 0, 500) : $value;
    }

    public function deleted(Subject $subject): void
    {
        ActivityLog::record($subject->isForceDeleting() ? 'force_deleted' : 'deleted', $subject);
    }

    public function restored(Subject $subject): void
    {
        ActivityLog::record('restored', $subject);
    }
}
