<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return view('events.index', [
            'events' => Event::active()->paginate(9),
        ]);
    }

    public function show(Event $event)
    {
        return view('events.show', [
            'event' => $event,
        ]);
    }
}
