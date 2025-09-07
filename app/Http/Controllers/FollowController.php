<?php

namespace App\Http\Controllers;

use App\Services\FollowService;
use App\Http\Requests\FollowUnfollowRequest;
use App\Models\User;
use App\Notifications\NewFollowerNotification;
use Illuminate\Http\JsonResponse;

class FollowController extends Controller
{
    //
    protected FollowService $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }
    public function follow(FollowUnfollowRequest $request): JsonResponse
     {
        $user_id = $request->input('user_id');
        $userToFollow = User::find($user_id);

        $response = $this->followService->follow($userToFollow);

        //send notification to user who is followed
        $userToFollow->notify(new NewFollowerNotification(Auth()->user()));

        return $response;

    }

    public function unfollow(FollowUnfollowRequest $request): JsonResponse
    {
        $user_id = $request->input('user_id');

        $userToUnfollow = User::find($user_id);

        return  $this->followService->unfollow($userToUnfollow);
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
