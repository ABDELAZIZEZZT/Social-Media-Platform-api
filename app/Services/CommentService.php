<?php
namespace App\Services;



use App\Models\Blog;
use App\Models\Comment;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\CommentNotification;
use App\Notifications\ReactionOnCommentNotification;

class CommentService
{
    public function getComments($blogId)
    {
        return Comment::where('blog_id', $blogId)
            ->where('parent_id', null)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get()->toArray();
    }

    public function createComment($data):array
    {
        $data['user_id'] = auth()->user()->id;
        $comment = Comment::create($data);
        $this->sendNotification($data,$comment);
        return $comment->toArray();
    }

    public function sendNotification($data,Comment $comment):void
    {
        $blog=Blog::findOrFail($data['blog_id']);
        $user=User::findOrFail($data['user_id']);
        $blog->user->notify(new CommentNotification($blog,$comment,$user));
    }

    public function handleReaction($data,Comment $comment): array
    {
        $reaction=$this->getExitingReactions($comment->id);
        if ($reaction) {
            return  $this->updateOrDeleteReaction($reaction,$data['type']);
        }

        $this->createReaction($data,$comment);

        $user=User::findOrFail(auth()->user()->id);
        $blog=$comment->blog;
        $this->sendNotificationWithReaction($user,$blog,$comment,$data['type']);

        return  ['message' => 'Comment '.$data['type'].' successfully','status' => 200];
    }

    protected function updateOrDeleteReaction(Reaction $reaction, string $newType): array
    {
        if ($reaction->type === $newType) {
            $reaction->delete();
            return ['message' => 'Reaction deleted successfully', 'status' => 200];
        }
        $reaction->update(['type' => $newType]);
        return ['message' => 'Reaction updated successfully', 'status' => 200];
    }

    public function getExitingReactions(int $commentId)
    {
        $reaction = Reaction::where('reactionable_type', 'App\Models\Comment')
            ->where('reactionable_id', $commentId)
            ->where('user_id', auth()->user()->id)
            ->first();
        return $reaction;
    }
    public function createReaction(array $data,$comment)
    {
        $data['user_id'] = auth()->user()->id;
        $data['reactionable_id'] = $comment->id;
        $data['reactionable_type'] = 'App\Models\Comment';
        Reaction::updateOrCreate($data);
    }
    public function sendNotificationWithReaction(User $user,$blog,$comment,$reactionType):void
    {
        //send notification to the blog's user who react on comment the blog
        $blog->user->notify(new ReactionOnCommentNotification($blog,$comment,$user,$reactionType));
    }

}
