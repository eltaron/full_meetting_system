<?php

namespace Modules\Archive\Http\Controllers;

use Modules\Archive\Models\ActivityLog;
use Modules\Archive\Models\Subject;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// فتح/تحميل المرفقات بعد التحقق من صلاحية المستخدم على المستند
class AttachmentController extends Controller
{
    public function __invoke(Request $request, Media $media)
    {
        $user = $request->user();

        abort_unless($media->model_type === Subject::class, 404);

        $subject = Subject::withTrashed()->find($media->model_id);

        abort_unless($subject && $user->can('view', $subject), 403);
        abort_if($subject->trashed() && ! $user->isAdmin(), 404);
        abort_unless(is_file($media->getPath()), 404);

        // العرض داخل المتصفح لملفات PDF والصور فقط، أي نوع آخر يُحمّل كملف
        $inline = in_array($media->mime_type, self::INLINE_TYPES, true) && ! $request->boolean('download');

        ActivityLog::record('viewed_attachment', $subject, [
            'file' => $media->name,
            'download' => ! $inline,
        ]);

        $headers = [
            'Content-Type' => $inline ? $media->mime_type : 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ];

        // متصفح كروم لا يعرض PDF داخل sandbox، باقي الأنواع تُعزل تماماً
        if ($media->mime_type !== 'application/pdf') {
            $headers['Content-Security-Policy'] = 'sandbox';
        }

        return $inline
            ? response()->file($media->getPath(), $headers)
            : response()->download($media->getPath(), $media->file_name, $headers);
    }

    private const INLINE_TYPES = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/tiff'];
}
