<?php
namespace App\Services;

use App\Models\User;

class FollowService{


    public function follow(User $userToFollow)
    {
//        dd(auth()->user()->id);
        if($userToFollow->id == auth()->user()->id) {
            return response()->json(['message' => 'You cannot follow yourself'], 400);
        }
        $user = auth()->user();
        if($user->isFollowing($userToFollow)) {
            return response()->json(['message' => 'You are already following this user'], 400);
        }
        $user->follow($userToFollow);
        return response()->json(['message' => 'User followed successfully!'],200);
    }

    public function unfollow(User $userToUnfollow)
    {
        if($userToUnfollow->id == auth()->user()->id) {
            return response()->json(['message' => 'You cannot unfollow yourself'], 400);
        }

        $user=auth()->user();

        if(!$user->isFollowing($userToUnfollow)) {
            return response()->json(['message' => 'You are not following this user'],400);
        }
        $user->unfollow($userToUnfollow);

        return response()->json(['message' => 'User unfollowed successfully!'],200);
    }

}
