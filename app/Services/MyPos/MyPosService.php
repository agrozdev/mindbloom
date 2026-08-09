<?php

namespace App\Services\MyPos;

use App\Models\Order;
use Mypos\IPC\Cart;
use Mypos\IPC\Config;
use Mypos\IPC\Customer;
use Mypos\IPC\Defines;
use Mypos\IPC\Purchase;
use Mypos\IPC\Response;

class MyPosService
{
    public function buildConfig(): Config
    {
        $config = config('services.mypos');

        $cnf = (new Config)
            ->setIpcURL($config['sandbox']
                ? 'https://www.mypos.com/vmp/checkout-test'
                : 'https://mypos.com/vmp/checkout/')
            ->setLang('BG')
            ->setVersion('1.4');

        if (! empty($config['config_package'])) {
            return $cnf->loadConfigurationPackage($config['config_package']);
        }

        return $cnf
            ->setSid($config['sid'])
            ->setWallet($config['wallet'])
            ->setKeyIndex($config['key_index'])
            ->setPrivateKeyPath($config['private_key_path'])
            ->setAPIPublicKeyPath($config['public_key_path']);
    }

    /**
     * Build the signed form data for redirecting the customer to the myPOS
     * hosted checkout page.
     *
     * @return array{ActionUrl: string, FormData: array<string, string>}
     */
    public function buildPurchaseRedirect(Order $order): array
    {
        [$firstName, $lastName] = $this->splitName($order->name);

        $cart = (new Cart)->add($order->orderable->title, 1, (float) $order->amount);

        $customer = (new Customer)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($order->email)
            ->setPhone($order->phone);

        $purchase = (new Purchase($this->buildConfig()))
            ->setOrderID($order->uuid)
            ->setCurrency($order->currency)
            ->setUrlOk(route('payments.thank-you', $order))
            ->setUrlCancel(route('payments.cancelled', $order))
            ->setUrlNotify(route('payments.mypos.notify'))
            ->setCart($cart)
            ->setCustomer($customer);

        // setCardTokenRequest() and setPaymentParametersRequired() don't return
        // $this in the SDK, so they can't be chained like the other setters.
        $purchase->setCardTokenRequest(Purchase::CARD_TOKEN_REQUEST_NONE);
        $purchase->setPaymentParametersRequired(Purchase::PURCHASE_TYPE_FULL);

        $purchase->process(false);

        return $purchase->getFormParameters();
    }

    /**
     * Parse and signature-verify an IPN payload posted by myPOS.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws \Mypos\IPC\IPC_Exception
     */
    public function parseNotify(array $payload): Response
    {
        return Response::getInstance($this->buildConfig(), $payload, Defines::COMMUNICATION_FORMAT_POST);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0], $parts[1] ?? $parts[0]];
    }
}
