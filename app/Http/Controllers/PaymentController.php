<?php

namespace App\Http\Controllers;

use App\Mail\OrderPaidAdmin;
use App\Mail\OrderPaidCustomer;
use App\Models\Order;
use App\Services\MyPos\MyPosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mypos\IPC\Defines;
use Mypos\IPC\IPC_Exception;

class PaymentController extends Controller
{
    public function thankYou(Order $order)
    {
        return view('payments.thank-you', [
            'order' => $order->load('orderable'),
        ]);
    }

    public function cancelled(Order $order)
    {
        if ($order->status === Order::STATUS_PENDING) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
        }

        return view('payments.cancelled', [
            'order' => $order->load('orderable'),
        ]);
    }

    public function notify(Request $request, MyPosService $myPos)
    {
        $this->rawLog($request->all());

        try {
            $response = $myPos->parseNotify($request->all());
        } catch (IPC_Exception $e) {
            Log::warning('myPOS IPN rejected: '.$e->getMessage());

            return response('Invalid request', 400);
        }

        $data = $response->getData(CASE_LOWER);
        $order = Order::where('uuid', $data['orderid'] ?? null)->first();

        if (! $order) {
            Log::warning('myPOS IPN for unknown order', ['orderid' => $data['orderid'] ?? null]);

            return response('Unknown order', 400);
        }

        $order->notify_payload = $data;

        if ($response->getStatus() === Defines::STATUS_SUCCESS) {
            if ($order->status !== Order::STATUS_PAID) {
                $order->status = Order::STATUS_PAID;
                $order->paid_at = now();
                $order->transaction_id = $data['ipn_trnref'] ?? $data['trnref'] ?? $data['transactionid'] ?? null;
                $order->save();

                Mail::to($order->email)->send(new OrderPaidCustomer($order));
                Mail::to(config('mail.admin_address'))->send(new OrderPaidAdmin($order));
            } else {
                $order->save();
            }
        } else {
            $order->status = Order::STATUS_FAILED;
            $order->save();
        }

        return response('OK', 200);
    }

    /**
     * Writes the raw IPN payload straight to a dedicated file, bypassing the
     * Log facade entirely — kept as a fallback in case the app's normal
     * logging isn't writing (as observed once on production).
     *
     * @param  array<string, mixed>  $payload
     */
    private function rawLog(array $payload): void
    {
        $line = '['.now()->toDateTimeString().'] '.json_encode($payload, JSON_UNESCAPED_UNICODE).PHP_EOL;

        @file_put_contents(storage_path('logs/mypos-raw.log'), $line, FILE_APPEND | LOCK_EX);
    }
}
