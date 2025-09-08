<?php
namespace App\Repositories;

use App\Models\Reaction;
use App\Models\User;
use App\Notifications\ReactionOnBlogNotification;

class ReactRepository
{
    public function handleReaction($data,$blog): array
    {
        $reaction=$this->getExitingReactions($blog->id);
        if ($reaction) {
            return  $this->updateOrDeleteReaction($reaction,$data['type']);
        }
        $user=User::findOrFail(auth()->user()->id);

        $this->createReaction($data,$blog);

        $this->sendNotification($user,$blog,$data['type']);

        return  ['message' => 'Blog '.$data['type'].' successfully','status' => 200];
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

    public function getExitingReactions(int $blogId)
    {
        $reaction = Reaction::where('reactionable_type', 'App\Models\Blog')
            ->where('reactionable_id', $blogId)
            ->where('user_id', auth()->user()->id)
            ->first();
        return $reaction;
    }
    public function createReaction(array $data,$blog)
    {
        $data['user_id'] = auth()->user()->id;
        $data['reactionable_id'] = $blog->id;
        $data['reactionable_type'] = 'App\Models\Blog';
        Reaction::updateOrCreate($data);
    }
    public function sendNotification(User $user,$blog,$reactionType)
    {
        //send notification to the blog's user who liked the blog
        $blog->user->notify(new ReactionOnBlogNotification($user,$blog,$reactionType));
    }


}
