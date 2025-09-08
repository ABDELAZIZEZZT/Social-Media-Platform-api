<?php
namespace App\Services;

use App\Http\Requests\Posts\ReactionOnBlogRequest;
use App\Models\Blog;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\ReactionOnBlogNotification;
use App\Repositories\BlogRepository;
use Illuminate\Http\JsonResponse;

class BlogService
{

    public function __construct(protected BlogRepository $blogRepository){}

    public function create(array $data): Blog
    {
        $data['user_id'] = auth()->user()->id;

        return $this->blogRepository->create($data);
    }

    public function search($searchTerm):array
    {
        return $this->blogRepository->search($searchTerm);
    }

}
