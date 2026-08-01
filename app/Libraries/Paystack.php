<?php

namespace App\Libraries;

use Exception;
use InvalidArgumentException;
use Throwable;

class Paystack
{
    private string $publicKey;
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    /**
     * Initializes the Paystack API client.
     */
    public function __construct(object $config)
    {
        if (empty($config->public_key) || empty($config->secret_key)) {
            throw new InvalidArgumentException('Paystack Public Key and Secret Key are required.');
        }

        $this->publicKey = $config->public_key;
        $this->secretKey = $config->secret_key;
    }

    /**
     * Internal method to handle API requests to Paystack.
     */
    private function request(string $method, string $endpoint, array $data = []): ?object
    {
        $client = \Config\Services::curlrequest([
            'baseURI' => $this->baseUrl,
        ]);

        $options = [
            'headers'     => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'http_errors' => false
        ];

        if ($method === 'POST' && !empty($data)) {
            $options['json'] = $data;
        }

        try {
            $response = $client->request($method, $endpoint, $options);
            return json_decode($response->getBody());
        } catch (Throwable $e) {
            log_message('error', '[Paystack] API Request failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initializes a transaction on Paystack to generate a checkout URL
     */
    public function initializeTransaction(object $order, string $userEmail, string $callbackUrl): object
    {
        // Paystack processes amounts in subunits (e.g., Kobo/Cents)
        $amountInSubunits = (int)round((float)$order->total_amount * 100);

        $data = [
            'email'        => $userEmail,
            'amount'       => $amountInSubunits,
            'currency'     => strtoupper($order->currency ?? 'NGN'),
            'reference'    => $order->order_token,
            'callback_url' => $callbackUrl,
            'metadata'     => [
                'order_id'    => $order->id,
                'order_token' => $order->order_token
            ]
        ];

        $response = $this->request('POST', '/transaction/initialize', $data);

        if (empty($response) || $response->status !== true) {
            $errorMsg = $response->message ?? 'Unknown API error';
            throw new Exception('Failed to initialize Paystack transaction: ' . $errorMsg);
        }

        return $response->data; // Returns authorization_url, access_code, and reference
    }

    /**
     * Verifies a transaction via the Paystack API using the order reference.
     */
    public function verifyTransaction(string $reference): ?object
    {
        $response = $this->request('GET', '/transaction/verify/' . urlencode($reference));

        if ($response && $response->status === true && isset($response->data)) {
            return $response->data;
        }

        log_message('warning', '[Paystack] Payment verification failed or incomplete for reference: ' . $reference);
        return null;
    }

    /**
     * Returns the Paystack Public Key for potential frontend usage.
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Validates and parses the incoming Webhook payload from Paystack
     *
     * @param string $payload The raw JSON body of the request
     * @param string $signature The x-paystack-signature header
     * @return array ['isValid' => bool, 'isIgnored' => bool, 'reference' => string, 'error' => string]
     */
    public function parseWebhookPayload(string $payload, string $signature): array
    {
        if (empty($payload) || empty($signature)) {
            return ['isValid' => false, 'isIgnored' => false, 'reference' => '', 'error' => 'Missing payload or signature'];
        }

        $calculatedSignature = hash_hmac('sha512', $payload, $this->secretKey);

        if (!hash_equals($calculatedSignature, $signature)) {
            return ['isValid' => false, 'isIgnored' => false, 'reference' => '', 'error' => 'Signature mismatch. Unauthorized.'];
        }

        $postData = json_decode($payload, true);

        // Safety check for invalid JSON
        if (!is_array($postData)) {
            return ['isValid' => false, 'isIgnored' => false, 'reference' => '', 'error' => 'Invalid JSON payload.'];
        }

        $event = $postData['event'] ?? '';

        if ($event !== 'charge.success') {
            return ['isValid' => true, 'isIgnored' => true, 'reference' => '', 'error' => ''];
        }

        $reference = $postData['data']['reference'] ?? '';

        if (empty($reference)) {
            return ['isValid' => false, 'isIgnored' => false, 'reference' => '', 'error' => 'Missing reference in payload'];
        }

        return ['isValid' => true, 'isIgnored' => false, 'reference' => $reference, 'error' => ''];
    }
}