<?php

namespace App\Notifications;

use App\Models\CommentModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentOnDiscussion extends Notification
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(CommentModel $comment)
    {
        //
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database notifaction.
     * @param mixed $notifiable
     * @return array{by: mixed, comment_id: mixed, content: mixed, discussion_id: mixed, message: string}
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Someone replied to your discussion.',
            'comment_id' => $this->comment->id,
            'content' => $this->comment->content,
            'discussion_content' => $this->comment->discussion->content,
            'board_id' => $this->comment->discussion->board->id,
            'discussion_id' => $this->comment->discussion_id,
        ];
    }

}
