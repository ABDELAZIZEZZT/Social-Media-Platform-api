<?php
namespace App\Services;


use App\Http\Requests\Posts\ReactionOnBlogRequest;
use App\Models\Blog;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\ReactionOnBlogNotification;
use App\Repositories\ReactRepository;
use Illuminate\Http\JsonResponse;

class ReactService
{


    public function __construct(protected ReactRepository $reactRepository)
    {
    }

    public function react($data,Blog $blog):array
    {
        $result = $this->reactRepository->handleReaction($data, $blog);
        return ['message' => $result['message'], 'status'=>$result['status']];
    }

}
