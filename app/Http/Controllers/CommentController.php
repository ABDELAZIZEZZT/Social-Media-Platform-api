<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function index($blog_id): JsonResponse
    {
        $comments = Comment::where('blog_id', $blog_id)
            ->where('parent_id', null)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();
        return response()->json($comments);
    }

    public function store(CommentRequest $request): JsonResponse
    {
        $data=$request->validated();
        $data['user_id'] = auth()->user()->id;
        $comment = Comment::create($data);
        return response()->json($comment);
    }

    public function getReplies($comment_id): JsonResponse{
        $comment=Comment::findOrFail($comment_id);
        $replies = Comment::where('parent_id', $comment_id)
            ->with('user')
            ->get();
        return response()->json($replies);
    }




}
