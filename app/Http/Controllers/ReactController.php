<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\ReactionOnBlogRequest;
use App\Models\Blog;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\ReactionOnBlogNotification;
use App\Services\ReactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactController extends Controller
{
    //

    public function __construct(protected ReactService $reactService)
    {
    }

    public function react(ReactionOnBlogRequest $request,Blog $blog):JsonResponse
    {
        $data = $request->validated();

        $result = $this->reactService->react($data, $blog);

        return response()->json(['message' => $result['message']], $result['status']);

    }
}
