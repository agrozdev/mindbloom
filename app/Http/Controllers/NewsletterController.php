<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsAgainstBots;
use App\Mail\NewsletterSubscribed;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    use GuardsAgainstBots;

    public function store(Request $request)
    {
        if ($this->isLikelyBot($request)) {
            return back()->with('newsletter_status', 'Благодарим ви за абонамента!');
        }

        $data = $request->validate([
            'newsletter_email' => ['required', 'email', 'max:255'],
        ], [
            'newsletter_email.required' => 'Моля, въведете имейл адрес.',
            'newsletter_email.email' => 'Моля, въведете валиден имейл адрес.',
        ]);

        $subscriber = Subscriber::firstOrCreate(['email' => $data['newsletter_email']]);

        if ($subscriber->wasRecentlyCreated) {
            Mail::to(config('mail.admin_address'))->send(new NewsletterSubscribed($data['newsletter_email']));
        }

        return back()->with('newsletter_status', 'Благодарим ви за абонамента!');
    }
}
