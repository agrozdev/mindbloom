<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;

class BlogController extends Controller
{
    public function index()
    {
        return view('blog.categories', [
            'categories' => PostCategory::withCount(['posts' => fn ($query) => $query->published()])->orderBy('name')->get(),
        ]);
    }

    public function category(PostCategory $category)
    {
        return view('blog.category', [
            'posts' => $category->posts()->published()->paginate(9),
            'categories' => PostCategory::orderBy('name')->get(),
            'activeCategory' => $category,
        ]);
    }

    public function show(PostCategory $category, Post $post)
    {
        return view('blog.show', [
            'post' => $post,
        ]);
    }
}
