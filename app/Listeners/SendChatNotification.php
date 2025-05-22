<?php

namespace App\Listeners;

use App\Events\NewChatMessage;
use App\Models\Notification;

class SendChatNotification
{

public function handle(NewChatMessage $event)
{
    \Log::info('SendChatNotification listener triggered', [
        'receiver_id' => $event->receiver->id,
        'comment_id' => $event->comment->id,
    ]);
    Notification::create([
        'user_id' => $event->receiver->id,
        'title' => 'New Chat Message',
        'message' => 'You have a new message from ' . $event->comment->sender->username,
        'url' => route('records.show', ['id' => $event->comment->document_id]),
        'is_read' => false,
    ]);
}
}
