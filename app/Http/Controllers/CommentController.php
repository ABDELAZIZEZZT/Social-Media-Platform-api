<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ReactionOnCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\DeleteCommentRequest;

class CommentController extends Controller
{
    protected CommentService $commentService;
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function getAllCommentsInBlog($blog_id): JsonResponse
    {
        return response()->json($this->commentService->getComments($blog_id),200);
    }

    public function store(CommentRequest $request): JsonResponse
    {
        $data=$request->validated();

        $comment = $this->commentService->createComment($data);

        return response()->json($comment,201);
    }

    public function getReplies(Comment $comment): JsonResponse{
        $replies = Comment::where('parent_id', $comment->id)
            ->with('user')
            ->get();
        return response()->json($replies);
    }

    public function destroy(DeleteCommentRequest $request,Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function update(UpdateCommentRequest $request,Comment $comment): JsonResponse
    {
        $data = $request->validated();
        $comment->update($data);
        return response()->json($comment,200);
    }

 public function react(ReactionOnCommentRequest $request,Comment $comment):JsonResponse
    {
        $data=$request->validated();

        $result = $this->commentService->handleReaction($data,$comment);

        return response()->json(['message' => $result['message']],$result['status']);
    }
}
