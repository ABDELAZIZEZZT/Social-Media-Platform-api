<?php
namespace App\Services;

use App\Http\Requests\Posts\ReactionOnBlogRequest;
use App\Models\Blog;
use App\Models\Reaction;
use App\Models\User;
use App\Notifications\ReactionOnBlogNotification;
use Illuminate\Http\JsonResponse;

class BlogService
{
    public function create(array $data):Blog
    {
        $data['user_id'] = auth()->user()->id;

        $blog = Blog::create($data);

        if (isset($data['image'])) {
            $file = $data['image'];
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $blog->image_path = '/storage/' . $filePath;
            $blog->save();
        }

        return $blog;
    }

    public function search($searchTerm):array
    {
        $blogs = Blog::query()
            ->when($searchTerm, function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('content', 'LIKE', "%{$searchTerm}%");
            })
            ->latest()
            ->with('user')
            ->get()->toArray();
        return $blogs;
    }

    public function handleReaction($data,$blog): array
    {
        $reaction=$this->getExitingReactions($blog->id);
        if ($reaction) {
          return  $this->updateOrDeleteReaction($reaction,$data['type']);
        }
        $user=User::findOrFail(auth()->user()->id);

        $this->createReaction($data,$blog);

        $this->sendNotification($user,$blog,$data['type']);

        return  ['message' => 'Blog '.$data['type'].' successfully','status' => 200];
    }

    protected function updateOrDeleteReaction(Reaction $reaction, string $newType): array
    {
        if ($reaction->type === $newType) {
            $reaction->delete();
            return ['message' => 'Reaction deleted successfully', 'status' => 200];
        }
        $reaction->update(['type' => $newType]);
        return ['message' => 'Reaction updated successfully', 'status' => 200];
    }

    public function getExitingReactions(int $blogId)
    {
        $reaction = Reaction::where('reactionable_type', 'App\Models\Blog')
            ->where('reactionable_id', $blogId)
            ->where('user_id', auth()->user()->id)
            ->first();
        return $reaction;
    }
    public function createReaction(array $data,$blog)
    {
        $data['user_id'] = auth()->user()->id;
        $data['reactionable_id'] = $blog->id;
        $data['reactionable_type'] = 'App\Models\Blog';
        Reaction::updateOrCreate($data);
    }
    public function sendNotification(User $user,$blog,$reactionType)
    {
        //send notification to the blog's user who liked the blog
        $blog->user->notify(new ReactionOnBlogNotification($user,$blog,$reactionType));
    }
}
