<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.index', [
            'posts' => Post::published()->paginate(9),
        ]);
    }

    public function show(Post $post)
    {
        return view('blog.show', [
            'post' => $post,
        ]);
    }
}
