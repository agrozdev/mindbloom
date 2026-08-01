<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Post;
use App\Models\Service;

class PageController extends Controller
{
    public function home()
    {
        return view('home', [
            'services' => Service::active()->limit(4)->get(),
            'events' => Event::active()->where('starts_at', '>=', now())->limit(3)->get(),
            'posts' => Post::published()->limit(2)->get(),
        ]);
    }

    public function about()
    {
        return view('about');
    }
}
