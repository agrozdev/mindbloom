<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index', [
            'services' => Service::active()->get(),
        ]);
    }

    public function show(Service $service)
    {
        return view('services.show', [
            'service' => $service,
        ]);
    }
}
