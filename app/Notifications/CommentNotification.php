<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentNotification extends Notification
{
    use Queueable;
    protected $blog , $comment, $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($blog, $comment, $user)
    {
        //
        $this->blog = $blog;
        $this->comment = $comment;
        $this->user = $user;
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
            'message' =>$this->user->first_name.' '.$this->user->last_name.' commented on your '.$this->blog->title.' blog',
        ];
    }
}
