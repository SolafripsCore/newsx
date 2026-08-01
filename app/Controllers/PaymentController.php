<?php

namespace App\Controllers;

use App\Services\PaymentProcessorService;

class PaymentController extends BaseController
{
    protected PaymentProcessorService $paymentProcessorService;
    protected object $orderModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->paymentProcessorService = new PaymentProcessorService();
        $this->orderModel = model('OrderModel');
    }

    /**
     * Process PayPal payment callback
     *
     * @method POST
     */
    public function paypal()
    {
        if (!authCheck()) {
            return $this->response->setJSON(['status' => 0, 'message' => trans("msg_error")]);
        }

        $orderToken = $this->request->getPost('order_token');
        $paymentId = $this->request->getPost('payment_id');
        $subscriptionId = $this->request->getPost('subscription_id');

        if (empty($orderToken)) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Invalid request token.']);
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();
        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Order not found.']);
        }

        // Validate order ownership
        if ($order->user_id != user()->id) {
            log_message('warning', '[PayPal Frontend] User ID mismatch for order token: ' . $orderToken);
            return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized action on this order.']);
        }

        // Prevent duplicate processing if webhook fired first
        if ($order->status === 'completed') {
            return $this->response->setJSON([
                'status'      => 1,
                'redirectUrl' => generateURL('checkout', 'success') . '?order_token=' . $order->order_token
            ]);
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processPaypalPayment($order, $paymentId, $subscriptionId);

        if ($result['success']) {
            return $this->response->setJSON([
                'status'      => 1,
                'redirectUrl' => generateURL('checkout', 'success') . "?order_token=" . $order->order_token,
            ]);
        }

        return $this->response->setJSON(['status' => 0, 'message' => $result['message']]);
    }

    /**
     * Process Stripe payment callback
     *
     * @method GET
     */
    public function stripe()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $sessionId = $this->request->getGet('session_id');
        $orderToken = $this->request->getGet('order_token');

        if (empty($sessionId) || empty($orderToken)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();

        // Verify if order exists and belongs to the current user
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        // Prevent duplicate processing if webhook fired first
        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processStripePayment($order, $sessionId, $this->settings->application_name);

        if ($result['success']) {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        setErrorMessage($result['message']);
        return redirect()->to(generateURL('checkout', 'payment'));
    }

    /**
     * Process Razorpay payment callback
     *
     * @method POST
     */
    public function razorpay()
    {
        if (!authCheck()) {
            return $this->response->setJSON(['status' => 0, 'message' => trans("msg_error")]);
        }

        $orderToken = $this->request->getPost('order_token');
        $razorpayPaymentId = $this->request->getPost('razorpay_payment_id');
        $razorpayOrderId = $this->request->getPost('razorpay_order_id');
        $razorpaySignature = $this->request->getPost('razorpay_signature');

        if (empty($orderToken) || empty($razorpayPaymentId) || empty($razorpaySignature)) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Invalid payment parameters.']);
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();

        if (empty($order)) {
            return $this->response->setJSON(['status' => 0, 'message' => 'Order not found.']);
        }

        // Validate order ownership
        if ($order->user_id != user()->id) {
            log_message('warning', '[Razorpay Frontend] User ID mismatch for order token: ' . $orderToken);
            return $this->response->setJSON(['status' => 0, 'message' => 'Unauthorized action on this order.']);
        }

        // Prevent duplicate processing if webhook fired first
        if ($order->status === 'completed') {
            return $this->response->setJSON([
                'status'      => 1,
                'redirectUrl' => generateURL('checkout', 'success') . '?order_token=' . $order->order_token
            ]);
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processRazorpayPayment($order, $razorpayPaymentId, $razorpayOrderId, $razorpaySignature, false);

        if ($result['success']) {
            return $this->response->setJSON([
                'status'      => 1,
                'redirectUrl' => generateURL('checkout', 'success') . '?order_token=' . $order->order_token
            ]);
        }

        return $this->response->setJSON(['status' => 0, 'message' => $result['message']]);
    }

    /**
     * Process Paystack payment callback
     *
     * @method GET
     */
    public function paystack()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $reference = $this->request->getGet('reference');
        if (empty($reference)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $reference)->first();

        // Verify if order exists and belongs to the current user
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        // Prevent duplicate processing if webhook fired first
        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processPaystackPayment($order, $reference);
        if ($result['success']) {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        setErrorMessage($result['message']);
        return redirect()->to(generateURL('checkout', 'payment'));
    }

    /**
     * Process Iyzico payment callback
     *
     * @method GET
     */
    public function iyzico()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $token = $this->request->getGet('token');
        $orderToken = $this->request->getGet('order_token');

        if (empty($token) || empty($orderToken)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();

        // Verify if order exists and belongs to the current user
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        // Prevent duplicate processing if the user refreshes the page
        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processIyzicoPayment($order, $token, $this->settings->application_name);

        if ($result['success']) {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        setErrorMessage($result['message']);
        return redirect()->to(generateURL('checkout', 'payment'));
    }

    /**
     * Process Mercado Pago payment callback
     *
     * @method GET
     */
    public function mercadopago()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $paymentId = $this->request->getGet('payment_id');
        $orderToken = $this->request->getGet('order_token');

        if (empty($paymentId) || empty($orderToken)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();

        // Verify if order exists and belongs to the current user
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        // Prevent duplicate processing if the user refreshes the page
        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processMercadoPagoPayment($order, $paymentId);

        if ($result['success']) {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        setErrorMessage($result['message']);
        return redirect()->to(generateURL('checkout', 'payment'));
    }

    /**
     * Process PayTabs payment callback
     *
     * @method GET
     */
    public function paytabs()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $orderToken = $this->request->getGet('order_token');
        $postData = $this->request->getGet('post_data');

        if (empty($orderToken) || empty($postData)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();

        // Verify if order exists and belongs to the current user
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        // Prevent duplicate processing if the user refreshes the page
        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        $decodedData = json_decode(base64_decode($postData), true);
        $tranRef = $decodedData['tranRef'] ?? ($decodedData['tran_ref'] ?? null);

        if (empty($tranRef)) {
            setErrorMessage("payment_option_load_error");
            return redirect()->to(generateURL('checkout', 'payment'));
        }

        // Delegate processing to the PaymentProcessorService
        $result = $this->paymentProcessorService->processPaytabsPayment($order, $tranRef, $this->settings->application_name);

        if ($result['success']) {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        setErrorMessage($result['message']);
        return redirect()->to(generateURL('checkout', 'payment'));
    }

    /**
     * Process NOWPayments payment callback
     *
     * @method GET
     */
    public function nowpayments()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $orderToken = $this->request->getGet('order_token');
        $paymentId = $this->request->getGet('NP_id');

        if (empty($orderToken)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();

        // Verify if order exists and belongs to the current user
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        if (!empty($paymentId)) {
            $result = $this->paymentProcessorService->processNowPaymentsPayment($order, $paymentId);

            // If Completed
            if ($result['status'] === 'completed') {
                return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
            }

            // If Pending (Waiting for blockchain confirmation)
            if ($result['status'] === 'pending') {
                // Using the specific crypto pending message
                setSuccessMessage("msg_payment_processing_crypto");
                return redirect()->to(generateURL('checkout', 'pending') . '?order_token=' . $order->order_token);
            }

            // If Failed
            setErrorMessage($result['message']);
            return redirect()->to(langBaseUrl());
        }

        // If user returns without NP_id but a pending transaction already exists
        $existingTx = model('TransactionModel')->where('order_id', $order->id)->first();

        if (!empty($existingTx) && $existingTx->status === 'pending') {
            setSuccessMessage("msg_payment_processing_crypto");
            return redirect()->to(generateURL('checkout', 'pending') . '?order_token=' . $order->order_token);
        }

        setErrorMessage("msg_error");
        return redirect()->to(langBaseUrl());
    }

    /**
     * Process Bank Transfer request
     *
     * @method POST
     */
    public function bankTransfer()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $orderToken = $this->request->getPost('order_token');
        if (empty($orderToken)) {
            return redirect()->to(langBaseUrl());
        }

        $order = $this->orderModel->where('order_token', $orderToken)->first();
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return redirect()->to(langBaseUrl());
        }

        if ($order->status === 'completed') {
            return redirect()->to(generateURL('checkout', 'success') . '?order_token=' . $order->order_token);
        }

        // Generate the exact same 8-character Transaction ID that was shown to the user on the frontend
        $txId = strtoupper(substr($order->order_token, 0, 8));

        $paymentService = new \App\Services\PaymentService();
        $paymentService->createPendingTransaction($order, 'bank_transfer', $txId);

        setSuccessMessage("msg_bank_transfer_order_created");
        return redirect()->to(generateURL('checkout', 'pending') . '?order_token=' . $order->order_token);
    }

    /**
     * Report Bank Transfer payment
     *
     * @method POST
     */
    public function reportBankTransferPayment()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $transactionId = (int)inputPost('transaction_id');
        if (empty($transactionId)) {
            return redirect()->to(langBaseUrl());
        }

        $transactionModel = model('TransactionModel');
        $transaction = $transactionModel->find($transactionId);

        if (empty($transaction) || $transaction->user_id != user()->id || $transaction->status !== 'pending' || $transaction->payment_method !== 'bank_transfer') {
            setErrorMessage("msg_error");
            return redirect()->to(getBackUrl());
        }

        $transferDetails = [
            'sender_name' => inputPost('sender_name') ?? '',
            'note'        => inputPost('transfer_note') ?? '',
        ];

        $transferNoteJson = safeEncode($transferDetails);

        if ($transactionModel->update($transactionId, ['transfer_note' => $transferNoteJson])) {
            setSuccessMessage("msg_request_sent");
        } else {
            setErrorMessage("msg_error");
        }

        return redirect()->to(getBackUrl());
    }

    /**
     * Cancel Order
     *
     * @method POST
     */
    public function cancelOrder()
    {
        if (!authCheck()) {
            return jsonResponse(false);
        }

        $transactionId = (int)inputPost('transaction_id');
        if (empty($transactionId)) {
            setErrorMessage("msg_error");
            return jsonResponse(false);
        }

        $transactionModel = model('TransactionModel');
        $transaction = $transactionModel->find($transactionId);
        if (empty($transaction) || $transaction->user_id != user()->id || $transaction->status !== 'pending' || !empty($transaction->transfer_note)) {
            setErrorMessage("msg_error");
            return jsonResponse(false);
        }

        $order = $this->orderModel->find((int)$transaction->order_id);
        if (empty($order) || $order->user_id != user()->id) {
            setErrorMessage("msg_error");
            return jsonResponse(false);
        }

        if ($transactionModel->cancelTransaction($transaction)) {
            setSuccessMessage("msg_order_cancelled");
            return jsonResponse(true);
        }

        setErrorMessage("msg_error");
        return jsonResponse(false);
    }

    /**
     * Invoice for a specific transaction
     */
    public function invoice($id)
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $transactionModel = model('TransactionModel');
        $orderModel = model('OrderModel');

        $transaction = $transactionModel->find($id);
        if (empty($transaction)) {
            return redirect()->to(langBaseUrl());
        }

        if (!isSuperAdmin() && (int)$transaction->user_id !== (int)user()->id) {
            return redirect()->to(langBaseUrl());
        }

        if ($transaction->status !== 'succeeded') {
            return redirect()->to(langBaseUrl());
        }

        $order = $orderModel->find($transaction->order_id);
        if (empty($order)) {
            return redirect()->to(langBaseUrl());
        }
        return view('common/invoice', [
            'transaction' => $transaction,
            'order'       => $order
        ]);
    }
}