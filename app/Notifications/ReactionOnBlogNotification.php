<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionOnBlogNotification extends Notification
{
    use Queueable;
    protected $user, $blog, $reaction;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $blog, $reaction)
    {
        //
        $this->user = $user;
        $this->blog = $blog;
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
            'user_id'=>$this->user->id,
            'blog_id'=>$this->blog->id,
            'reaction'=>$this->reaction,
            'message'=>$this->user->first_name.' '.$this->user->last_name.' reacted on your '.$this->blog->title.' blog with '.$this->reaction
        ];
    }
}
