<?php
namespace App\Repositories;


use App\Models\Blog;

class BlogRepository{

    public function create($data):Blog
    {
        $blog = Blog::create($data);
        return $this->createImage($data,$blog);
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

    public function update($blog, $data):array
    {
        $blog->update($data);
        return $blog->toArray();
    }

    public function delete($blog):bool
    {
        return $blog->delete();
    }

    public function getById($id)
    {
        return Blog::with('user')->findOrFail($id);
    }

    public function createImage($data,Blog $blog)
    {
        if (isset($data['image'])) {
            $file = $data['image'];
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $blog->image_path = '/storage/' . $filePath;
            $blog->save();
        }
        return $blog;

    }
}
