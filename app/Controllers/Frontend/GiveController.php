<?php

namespace App\Controllers\Frontend;

class GiveController extends \Controller {
    private \App\Models\GivingModel $giving;

    public function __construct() {
        parent::__construct();
        $this->giving = new \App\Models\GivingModel();
    }

    public function index(): void {
        $this->view('frontend.give', ['title' => 'Give'], 'public');
    }

    public function initiate(): void {
        $this->verifyCsrf();
        $amount = (float) $this->input('amount', 0);
        $type = $this->givingType((string) $this->input('giving_type', 'offering'));
        $method = (string) $this->input('giving_method', 'online');
        $name = trim((string) $this->input('giver_name', ''));
        $email = strtolower(trim((string) $this->input('giver_email', '')));

        if ($amount <= 0 || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Please provide a valid name, email, and amount.');
            $this->redirect(BASE_URL . '/give');
        }

        $reference = $this->giving->reference();
        $this->giving->create([
            'reference_no' => $reference,
            'giver_name' => $name,
            'giver_email' => $email,
            'giver_phone' => $this->input('giver_phone') ?: null,
            'amount' => $amount,
            'currency' => CURRENCY,
            'giving_type' => $type,
            'giving_method' => $method === 'bank_transfer' ? 'bank_transfer' : 'online',
            'payment_gateway' => $method === 'bank_transfer' ? null : 'paystack',
            'payment_status' => 'pending',
            'description' => $this->input('description') ?: null,
            'giving_date' => date('Y-m-d'),
        ]);

        if ($method === 'bank_transfer') {
            $this->setFlash('success', 'Your bank transfer pledge has been recorded. Please complete the transfer using the church account details.');
            $this->redirect(BASE_URL . '/give/success?reference=' . urlencode($reference));
        }

        if (!\Paystack::configured()) {
            $gift = $this->giving->markSuccess($reference, 'local-dev');
            if ($gift) {
                $this->giving->queueReceipt($gift);
            }
            $this->redirect(BASE_URL . '/give/success?reference=' . urlencode($reference));
        }

        try {
            $response = \Paystack::initialize([
                'email' => $email,
                'amount' => (int) round($amount * 100),
                'reference' => $reference,
                'callback_url' => BASE_URL . '/give/verify?reference=' . rawurlencode($reference),
                'metadata' => [
                    'giver_name' => $name,
                    'giving_type' => $type,
                    'reference_no' => $reference,
                ],
            ]);

            $data = $response['data'] ?? [];
            \Database::update('giving', [
                'gateway_ref' => $data['access_code'] ?? null,
            ], 'reference_no = :reference', [':reference' => $reference]);

            $this->redirect($data['authorization_url'] ?? (BASE_URL . '/give/success?reference=' . urlencode($reference)));
        } catch (\Throwable $e) {
            $this->setFlash('error', 'Unable to start Paystack payment: ' . $e->getMessage());
            $this->redirect(BASE_URL . '/give');
        }
    }

    public function success(): void {
        $reference = (string) $this->input('reference', '');
        $gift = $reference !== '' ? $this->giving->findByReference($reference) : null;
        $receiptPath = $gift['receipt_path'] ?? null;
        $this->view('frontend.give-success', ['title' => 'Giving Received', 'gift' => $gift, 'receiptPath' => $receiptPath], 'public');
    }

    public function verify(): void {
        $reference = (string) $this->input('reference', '');
        if ($reference === '') {
            $this->setFlash('error', 'Missing payment reference.');
            $this->redirect(BASE_URL . '/give');
        }

        try {
            $response = \Paystack::verify($reference);
            $data = $response['data'] ?? [];
            if (($data['status'] ?? '') === 'success') {
                $gift = $this->giving->markSuccess($reference, $data['id'] ?? null);
                if ($gift) {
                    $this->giving->queueReceipt($gift);
                }
                $this->setFlash('success', 'Payment verified successfully.');
            } else {
                $this->setFlash('error', 'Payment was not successful.');
            }
        } catch (\Throwable $e) {
            $this->setFlash('error', 'Payment verification failed: ' . $e->getMessage());
        }

        $this->redirect(BASE_URL . '/give/success?reference=' . urlencode($reference));
    }

    public function webhook(): void {
        $raw = file_get_contents('php://input') ?: '';
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? null;

        if (!\Paystack::validWebhookSignature($raw, $signature)) {
            $this->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            $this->json(['success' => false, 'message' => 'Invalid payload'], 400);
        }

        if (($payload['event'] ?? '') === 'charge.success') {
            $data = $payload['data'] ?? [];
            $reference = (string) ($data['reference'] ?? '');
            if ($reference !== '') {
                $gift = $this->giving->markSuccess($reference, isset($data['id']) ? (string) $data['id'] : null);
                if ($gift) {
                    $this->giving->queueReceipt($gift);
                }
            }
        }

        $this->json(['success' => true, 'message' => 'Webhook received']);
    }

    private function givingType(string $type): string {
        $allowed = ['tithe', 'offering', 'seed', 'project', 'welfare', 'mission', 'thanksgiving', 'vow', 'other'];
        return in_array($type, $allowed, true) ? $type : 'offering';
    }
}
