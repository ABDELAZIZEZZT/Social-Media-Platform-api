<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\DeleteRequest;
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Http\Requests\Posts\ReactionOnBlogRequest;
use App\Notifications\ReactionOnBlogNotification;
use Illuminate\Http\Request;
use App\Models\Reaction;
use App\Models\Blog;
use App\Models\User;
use \Illuminate\Http\JsonResponse;


class BlogController extends Controller
{
    public function index():JsonResponse
    {
        // dd('index');
        $blogs = Blog::with('user')->get();
        return response()->json($blogs, 200);
    }

    public function oneBlog($id):JsonResponse
    {
        // dd($id);
        $blog = Blog::with('user')->find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        return response()->json([$blog], 200);
    }

    public function store(StoreRequest $request):JsonResponse
    {
        $data=$request->validated();
        $data['user_id']=auth()->user()->id;

       $blog = Blog::create($data);

        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $blog->image_path = '/storage/' . $filePath;
        }
        $blog->save();

        return response()->json($blog, 201);
    }


    public function show():JsonResponse
    {
        $id=auth()->user()->id;
        $blogs = Blog::with('user')->where('user_id',$id)->get();
        if ($blogs->count() == 0) {
            return response()->json(['error' => 'no crated blogs for this user'], 404);
        }
        return response()->json($blogs, 200);
    }

    public function update(UpdateRequest $request,Blog $blog):JsonResponse
    {
        $data=$request->validated();
        $blog->update($data);

        return response()->json($blog, 200);
    }

    public function destroy(DeleteRequest $request,Blog $blog):JsonResponse
    {
        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully'], 200);
    }

    public function search(Request $request):JsonResponse
    {
        $searchTerm = $request->input('search');
        $blogs = Blog::query()
        ->when($searchTerm, function ($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('content', 'LIKE', "%{$searchTerm}%");
        })
        ->latest()
        ->with('user')
        ->paginate(10);
        return response()->json($blogs, 200);
    }

    public function react(ReactionOnBlogRequest $request,$blog_id):JsonResponse
    {
        $data=$request->validated();
        $blog = Blog::findOrFail($blog_id);
        $reaction = Reaction::where('reactionable_type', 'App\Models\Blog')
        ->where('reactionable_id', $blog->id)
        ->where('user_id', auth()->user()->id)
        ->first();

        if ($reaction) {
            if($data['type'] == $reaction->type){
                $reaction->delete();
                return response()->json(['message' => 'Reaction deleted successfully'], 200);
            }
            $reaction->type = $data['type'];
            $reaction->save();
            return response()->json(['message' => 'Reaction updated successfully'], 200);
        }
        $data['user_id'] = auth()->user()->id;
        $data['reactionable_id'] = $blog->id;
        $data['reactionable_type'] = 'App\Models\Blog';
        Reaction::updateOrCreate($data);

        //send notification to the blog's user who liked the blog
        $user=User::findOrFail($data['user_id']);
        $blog->user->notify(new ReactionOnBlogNotification($user,$blog,$data['type']));
        return response()->json(['message' => 'Blog liked successfully'], 200);
    }

}



