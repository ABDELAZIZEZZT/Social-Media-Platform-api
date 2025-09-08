<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    //
    public function index(Request $request):JsonResponse
    {
        $user = $request->user();
        $followingIds = $user->following()->pluck('following_id');
        $blogs = Blog::whereIn('user_id', $followingIds)
            ->orWhere('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($blogs, 200);
    }
}
