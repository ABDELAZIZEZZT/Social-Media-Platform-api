<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FollowUnfollowRequest;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    //
    public function follow(FollowUnfollowRequest $request): JsonResponse
     {
        $user_id = $request->input('user_id');
        if($user_id == auth()->user()->id) {
            return response()->json(['message' => 'You cannot follow yourself']);
        }
        $userToFollow = User::find($user_id);
        $user = auth()->user();
        if($user->isFollowing($userToFollow)) {
            return response()->json(['message' => 'You are already following this user']);
        }
        $user->follow($userToFollow);

        return response()->json(['message' => 'User followed successfully!'],200);

    }

    public function unfollow(FollowUnfollowRequest $request): JsonResponse
    {
        $user_id = $request->input('user_id');
        if($user_id == auth()->user()->id) {
            return response()->json(['message' => 'You cannot unfollow yourself']);
        }

        $userToUnfollow = User::find($user_id);
        $user=auth()->user();

        if(!$user->isFollowing($userToUnfollow)) {
            return response()->json(['message' => 'You are not following this user']);
        }
        $user->unfollow($userToUnfollow);

        return response()->json(['message' => 'User unfollowed successfully!'],200);
    }

    public function followers(): JsonResponse {
        $followers = auth()->user()->followers()->paginate(5);
        return response()->json(['followers' => $followers],200);
    }

    public function following(): JsonResponse {
        $following = auth()->user()->following()->paginate(5);
        return response()->json(['following' => $following],200);
    }
}
