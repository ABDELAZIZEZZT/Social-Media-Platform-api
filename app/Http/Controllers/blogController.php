<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        // dd('222');
        $blogs = Blog::with('user')->get();
    //     $blogs = Blog::paginate(5);
    //    foreach ($blogs as $blog) {
    //        $blog += User::find($blog->user_id)->first_name;
    //    }
        return response()->json($blogs, 200);
    }

    public function oneBlog($id)
    {
        // dd($id);
        $blog = Blog::with('user')->find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        return response()->json([$blog], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $blog = new Blog();
        $blog->user_id = $request->user()->id;
        $blog->title = $request->title;
        $blog->content = $request->content;

        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $blog->image_path = '/storage/' . $filePath;
        }

        $blog->save();

        return response()->json($blog, 201);
    }


    public function show()
    {
        $id=auth()->user()->id;
        $blogs = Blog::with('user')->where('user_id',$id)->get();
        if ($blogs->count() == 0) {
            return response()->json(['error' => 'no crated blogs for this user'], 404);
        }
        return response()->json($blogs, 200);
    }

    public function update(Request $request,$id)
    {
        // return response()->json([$request->all()]);


        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
        if($blog->user_id != $request->user()->id){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blog->title = $request->title;
        $blog->content = $request->content;

        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $blog->image_path = '/storage/' . $filePath;
        }

        $blog->save();

        return response()->json($blog, 200);
    }

    public function destroy(Request $request,$id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
        if($blog->user_id != $request->user()->id){
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully'], 200);
    }
}



