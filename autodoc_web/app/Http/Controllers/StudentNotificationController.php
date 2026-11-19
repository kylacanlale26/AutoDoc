<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::user()->notifications()->latest()->get();

        $missingDocumentTypes = $notifications
            ->filter(fn ($notification): bool => blank(data_get($notification->data, 'document_type')))
            ->pluck('data.document_id')
            ->filter()
            ->unique();

        if ($missingDocumentTypes->isNotEmpty()) {
            $documentTypes = Document::query()
                ->where('user_id', Auth::id())
                ->whereIn('id', $missingDocumentTypes)
                ->pluck('document_type', 'id');

            $notifications->each(function ($notification) use ($documentTypes): void {
                $documentId = data_get($notification->data, 'document_id');
                if (blank(data_get($notification->data, 'document_type')) && $documentTypes->has($documentId)) {
                    $notification->data = array_merge($notification->data, [
                        'document_type' => $documentTypes->get($documentId),
                    ]);
                }
            });
        }

        return view('student.notifications', compact('notifications'));
    }

    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('student.notifications');
    }

    public function open(string $notificationId): RedirectResponse
    {
        $notification = Auth::user()->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->markAsRead();

        $documentId = data_get($notification->data, 'document_id');
        $document = $documentId
            ? Document::query()->where('user_id', Auth::id())->find($documentId)
            : null;

        if (!$document) {
            return redirect()->route('student.notifications');
        }

        return redirect()->route('student.documents.show', $document)->with([
            'notificationMessage' => data_get($notification->data, 'message'),
            'notificationReviewNotes' => data_get($notification->data, 'review_notes'),
        ]);
    }
}
