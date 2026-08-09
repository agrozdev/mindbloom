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
    /**
     * myPOS's own published sandbox test credentials (developers.mypos.com —
     * "Test Data"). These are intentionally public, not secrets, and only
     * work against the checkout-test endpoint — never real money.
     */
    private const SANDBOX_SID = '000000000000010';

    private const SANDBOX_WALLET = '61938166610';

    private const SANDBOX_KEY_INDEX = 1;

    private const SANDBOX_PRIVATE_KEY = <<<'PEM'
        -----BEGIN RSA PRIVATE KEY-----
        MIICXAIBAAKBgQCf0TdcTuphb7X+Zwekt1XKEWZDczSGecfo6vQfqvraf5VPzcnJ
        2Mc5J72HBm0u98EJHan+nle2WOZMVGItTa/2k1FRWwbt7iQ5dzDh5PEeZASg2UWe
        hoR8L8MpNBqH6h7ZITwVTfRS4LsBvlEfT7Pzhm5YJKfM+CdzDM+L9WVEGwIDAQAB
        AoGAYfKxwUtEbq8ulVrD3nnWhF+hk1k6KejdUq0dLYN29w8WjbCMKb9IaokmqWiQ
        5iZGErYxh7G4BDP8AW/+M9HXM4oqm5SEkaxhbTlgks+E1s9dTpdFQvL76TvodqSy
        l2E2BghVgLLgkdhRn9buaFzYta95JKfgyKGonNxsQA39PwECQQDKbG0Kp6KEkNgB
        srCq3Cx2od5OfiPDG8g3RYZKx/O9dMy5CM160DwusVJpuywbpRhcWr3gkz0QgRMd
        IRVwyxNbAkEAyh3sipmcgN7SD8xBG/MtBYPqWP1vxhSVYPfJzuPU3gS5MRJzQHBz
        sVCLhTBY7hHSoqiqlqWYasi81JzBEwEuQQJBAKw9qGcZjyMH8JU5TDSGllr3jybx
        FFMPj8TgJs346AB8ozqLL/ThvWPpxHttJbH8QAdNuyWdg6dIfVAa95h7Y+MCQEZg
        jRDl1Bz7eWGO2c0Fq9OTz3IVLWpnmGwfW+HyaxizxFhV+FOj1GUVir9hylV7V0DU
        QjIajyv/oeDWhFQ9wQECQCydhJ6NaNQOCZh+6QTrH3TC5MeBA1Yeipoe7+BhsLNr
        cFG8s9sTxRnltcZl1dXaBSemvpNvBizn0Kzi8G3ZAgc=
        -----END RSA PRIVATE KEY-----
        PEM;

    private const SANDBOX_PUBLIC_CERT = <<<'PEM'
        -----BEGIN CERTIFICATE-----
        MIIBsTCCARoCCQCCPjNttGNQWDANBgkqhkiG9w0BAQsFADAdMQswCQYDVQQGEwJC
        RzEOMAwGA1UECgwFbXlQT1MwHhcNMTgxMDEyMDcwOTEzWhcNMjgxMDA5MDcwOTEz
        WjAdMQswCQYDVQQGEwJCRzEOMAwGA1UECgwFbXlQT1MwgZ8wDQYJKoZIhvcNAQEB
        BQADgY0AMIGJAoGBAML+VTmiY4yChoOTMZTXAIG/mk+xf/9mjwHxWzxtBJbNncNK
        0OLI0VXYKW2GgVklGHHQjvew1hTFkEGjnCJ7f5CDnbgxevtyASDGst92a6xcAedE
        adP0nFXhUz+cYYIgIcgfDcX3ZWeNEF5kscqy52kpD2O7nFNCV+85vS4duJBNAgMB
        AAEwDQYJKoZIhvcNAQELBQADgYEACj0xb+tNYERJkL+p+zDcBsBK4RvknPlpk+YP
        ephunG2dBGOmg/WKgoD1PLWD2bEfGgJxYBIg9r1wLYpDC1txhxV+2OBQS86KULh0
        NEcr0qEY05mI4FlE+D/BpT/+WFyKkZug92rK0Flz71Xy/9mBXbQfm+YK6l9roRYd
        J4sHeQc=
        -----END CERTIFICATE-----
        PEM;

    public function buildConfig(): Config
    {
        $config = config('services.mypos');

        if ($config['sandbox']) {
            return (new Config)
                ->setSid(self::SANDBOX_SID)
                ->setWallet(self::SANDBOX_WALLET)
                ->setKeyIndex(self::SANDBOX_KEY_INDEX)
                ->setPrivateKey(self::SANDBOX_PRIVATE_KEY)
                ->setAPIPublicKey(self::SANDBOX_PUBLIC_CERT)
                ->setIpcURL('https://www.mypos.com/vmp/checkout-test')
                ->setLang('BG')
                ->setVersion('1.4');
        }

        $cnf = (new Config)
            ->setIpcURL('https://mypos.com/vmp/checkout/')
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
