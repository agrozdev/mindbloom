<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'first_name.required' => 'Моля, въведете вашето име.',
            'last_name.required' => 'Моля, въведете вашата фамилия.',
            'email.required' => 'Моля, въведете имейл адрес.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'message.required' => 'Моля, въведете съобщение.',
            '*.max' => 'Полето е твърде дълго.',
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactFormSubmitted($data));

        return back()->with('status', 'Благодарим ви! Ще се свържем с вас скоро.');
    }
}
