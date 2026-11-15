<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public Document $document
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $status = str_replace('_', ' ', $this->document->status);

        return [
            'document_id' => $this->document->id,
            'document_name' => $this->document->document_name,
            'status' => $this->document->status,
            'review_notes' => $this->document->review_notes,
            'message' => "Your document status changed to {$status}.",
        ];
    }
}
