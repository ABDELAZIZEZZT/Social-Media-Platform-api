<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionOnCommentNotification extends Notification
{
    use Queueable;
    protected $blog, $comment, $user, $reaction;

    /**
     * Create a new notification instance.
     */
    public function __construct($blog, $comment, $user, $reaction)
    {
        //
        $this->blog = $blog;
        $this->comment = $comment;
        $this->user = $user;
        $this->reaction = $reaction;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
            'blog_id' => $this->blog->id,
            'comment_id' => $this->comment->id,
            'comment_content'=>$this->comment->content,
            'user_name'=>$this->user->name,
            'user_id' => $this->user->id,
            'reaction' => $this->reaction,
            'message'=>$this->user->first_name.' reacted on your comment with '.$this->reaction
        ];
    }
}
