<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GuardsAgainstBots;
use App\Models\Order;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\MyPos\MyPosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mypos\IPC\IPC_Exception;

class PostUnlockController extends Controller
{
    use GuardsAgainstBots;

    public function checkout(PostCategory $category, Post $post)
    {
        abort_if($post->price === null, 404);

        return view('blog.checkout', [
            'post' => $post,
        ]);
    }

    public function store(PostCategory $category, Post $post, Request $request, MyPosService $myPos)
    {
        abort_if($post->price === null, 404);

        if ($this->isLikelyBot($request)) {
            return back()->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
        ], [
            'name.required' => 'Моля, въведете вашето име.',
            'email.required' => 'Моля, въведете имейл адрес.',
            'email.email' => 'Моля, въведете валиден имейл адрес.',
            'phone.required' => 'Моля, въведете телефонен номер.',
        ]);

        $order = Order::create([
            ...$data,
            'orderable_type' => Post::class,
            'orderable_id' => $post->id,
            'amount' => $post->price,
            'currency' => 'EUR',
            'status' => Order::STATUS_PENDING,
        ]);

        try {
            $redirect = $myPos->buildPurchaseRedirect($order);
        } catch (IPC_Exception $e) {
            Log::error('myPOS purchase build failed: '.$e->getMessage(), ['order_id' => $order->id]);

            return back()->withInput()->with('error', 'В момента плащанията са временно недостъпни. Моля, опитайте отново по-късно.');
        }

        return view('payments.redirect', [
            'actionUrl' => $redirect['ActionUrl'],
            'formData' => $redirect['FormData'],
        ]);
    }
}
