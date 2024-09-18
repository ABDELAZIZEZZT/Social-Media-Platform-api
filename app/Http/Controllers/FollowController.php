<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FollowUnfollowRequest;
use App\Models\Blog;
use App\Models\User;

class FollowController extends Controller
{
    //
    public function follow(FollowUnfollowRequest $request) {
        $user_id = $request->input('user_id');
        if($user_id == auth()->user()->id) {
            return response()->json(['message' => 'You cannot follow yourself']);
        }

        $userToFollow = User::find($user_id);
        if (!$userToFollow) {
            return response()->json(['message' => 'User not found']);
        }
        if(auth()->user()->isFollowing($userToFollow)) {
            return response()->json(['message' => 'You are already following this user']);
        }
        
        $user = auth()->user();
        $user->follow($userToFollow);

        return response()->json(['message' => 'User followed successfully!'],200);

    }

    public function unfollow(FollowUnfollowRequest $request) {
        $user_id = $request->input('user_id');
        if($user_id == auth()->user()->id) {
            return response()->json(['message' => 'You cannot unfollow yourself']);
        }

        $userToFollow = User::find($user_id);
        if (!$userToFollow) {
            return response()->json(['message' => 'User not found']);
        }
        if(!auth()->user()->isFollowing($userToFollow)) {
            return response()->json(['message' => 'You are not following this user']);
        }
        $user = auth()->user();
        $user->unfollow($userToFollow);

        return response()->json(['message' => 'User unfollowed successfully!'],200);
    }
}
