<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    /**
     * Create a new policy instance.
     */

    public function update(User $user, Comment $comment):bool
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment):bool
    {
        return $user->id === $comment->user_id;
    }
}
