<?php
namespace App\Http\Controllers;

use App\Http\Requests\Posts\DeleteRequest;
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Http\Requests\Posts\ReactionOnBlogRequest;
use App\Services\BlogService;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;


class BlogController extends Controller
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }
    public function index():JsonResponse
    {
        $blogs = Blog::with('user')->get();
        return response()->json($blogs,  200);
    }

    public function store(StoreRequest $request):JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image');
        }
        $blog = $this->blogService->create($validated);

        return response()->json($blog, status: 201);

    }

    public function show(Blog $blog):JsonResponse
    {
        $data = $blog->load('user');
        return response()->json($data, 200);
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

        $blogs = $this->blogService->search($searchTerm);

        return response()->json($blogs, 200);
    }

}
