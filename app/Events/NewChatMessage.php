<?php

namespace App\Events;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Queue\SerializesModels;

class NewChatMessage
{
    use SerializesModels;

    public $comment;
    public $receiver;

    public function __construct(Comment $comment, User $receiver)
    {
        $this->comment = $comment;
        $this->receiver = $receiver;
    }
}