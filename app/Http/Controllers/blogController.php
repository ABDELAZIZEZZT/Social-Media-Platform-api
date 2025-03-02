<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\DeleteRequest;
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
//use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Json;

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
        $blog->user_id=auth()->user()->id;
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

}



