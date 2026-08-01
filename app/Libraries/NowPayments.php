<?php

namespace App\Libraries;

/**
 * NOWPayments API Library for CodeIgniter 4
 * * Provides methods for creating invoices, checking payment status,
 * and verifying IPN signatures using native CI4 CURLRequest.
 */
class NowPayments
{
    private string $apiKey;
    private string $ipnSecret;
    private string $apiUrl = 'https://api.nowpayments.io/v1/';

    /**
     * Initialize the library with configuration
     * * @param object $config Configuration from the database
     */
    public function __construct(object $config)
    {
        $this->apiKey = $config->secret_key ?? '';
        $this->ipnSecret = $config->webhook_secret ?? '';
    }

    /**
     * Create an invoice for a specific order
     * * @param object $order Order details from the database
     * @param string $successUrl Redirect URL after successful payment
     * @param string $cancelUrl Redirect URL if payment is cancelled
     * @param string $ipnUrl Webhook URL for payment notifications
     * @return object|null API response or null on failure
     */
    public function createInvoice(object $order, string $successUrl, string $cancelUrl, string $ipnUrl): ?object
    {
        $data = [
            'price_amount'      => (float)$order->total_amount,
            'price_currency'    => strtoupper($order->currency),
            'order_id'          => $order->order_token,
            'order_description' => 'Subscription Plan: ' . ($order->item_title ?? 'Premium'),
            'ipn_callback_url'  => $ipnUrl,
            'success_url'       => $successUrl,
            'cancel_url'        => $cancelUrl
        ];

        return $this->sendRequest('invoice', 'POST', $data);
    }

    /**
     * Retrieve payment details from the API
     * * @param string $paymentId The unique NOWPayments payment ID
     * @return object|null API response or null on failure
     */
    public function getPaymentStatus(string $paymentId): ?object
    {
        return $this->sendRequest('payment/' . $paymentId, 'GET');
    }

    /**
     * Verify the authenticity of the IPN request signature
     * * @param string $requestBody The raw JSON body received from the webhook
     * @param string $receivedSignature The signature from 'x-nowpayments-sig' header
     * @return bool True if signature is valid
     */
    public function verifyIpnSignature(string $requestBody, string $receivedSignature): bool
    {
        if (empty($this->ipnSecret) || empty($requestBody) || empty($receivedSignature)) {
            return false;
        }

        $data = json_decode($requestBody, true);
        if (empty($data)) {
            return false;
        }

        // Sort keys alphabetically as required by NOWPayments IPN verification
        ksort($data);
        $sortedJson = json_encode($data, JSON_UNESCAPED_SLASHES);
        $calculatedSignature = hash_hmac('sha512', $sortedJson, $this->ipnSecret);

        return hash_equals($calculatedSignature, $receivedSignature);
    }

    /**
     * Internal method to execute API requests via CURL
     * * @param string $endpoint API endpoint relative to base URL
     * @param string $method HTTP method (GET, POST)
     * @param array $data Data for POST requests
     * @return object|null Decoded JSON response
     */
    private function sendRequest(string $endpoint, string $method, array $data = []): ?object
    {
        $client = \Config\Services::curlrequest();
        $url = $this->apiUrl . ltrim($endpoint, '/');

        $options = [
            'headers' => [
                'x-api-key'    => $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'http_errors' => false,
            'timeout'     => 30
        ];

        if ($method === 'POST') {
            $options['json'] = $data;
        }

        try {
            $response = $client->request($method, $url, $options);
            $body = json_decode($response->getBody());

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return $body;
            }

            log_message('error', '[NOWPayments] API Error Response: ' . $response->getBody());
            return null;
        } catch (\Exception $e) {
            log_message('error', '[NOWPayments] CURL Request failed: ' . $e->getMessage());
            return null;
        }
    }
}