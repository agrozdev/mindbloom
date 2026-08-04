<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsAgainstBots;
use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use GuardsAgainstBots;

    public function create()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        if ($this->isLikelyBot($request)) {
            return back()->with('status', 'Благодарим ви! Ще се свържем с вас скоро.');
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'service' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'agree_terms' => ['accepted'],
        ], [
            'first_name.required' => 'Моля, въведете вашето име.',
            'last_name.required' => 'Моля, въведете вашата фамилия.',
            'email.required' => 'Моля, въведете имейл адрес.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'message.required' => 'Моля, въведете съобщение.',
            'agree_terms.accepted' => 'Моля, потвърдете съгласието си с Политиката на поверителност и Общите условия.',
            '*.max' => 'Полето е твърде дълго.',
        ]);

        unset($data['agree_terms']);

        Mail::to(config('mail.admin_address'))->send(new ContactFormSubmitted($data));

        return back()->with('status', 'Благодарим ви! Ще се свържем с вас скоро.');
    }
}
