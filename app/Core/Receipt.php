<?php

class Receipt {
    public static function generate(array $gift): string {
        $dir = UPLOAD_PATH . 'documents/receipts/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $baseName = 'receipt-' . preg_replace('/[^A-Za-z0-9-]/', '-', $gift['reference_no']);
        $html = self::renderHtml($gift);

        if (class_exists(\Dompdf\Dompdf::class)) {
            $fileName = $baseName . '.pdf';
            $path = $dir . $fileName;

            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();

            file_put_contents($path, $dompdf->output());
            return 'uploads/documents/receipts/' . $fileName;
        }

        $fileName = $baseName . '.html';
        $path = $dir . $fileName;
        file_put_contents($path, $html);
        return 'uploads/documents/receipts/' . $fileName;
    }

    private static function renderHtml(array $gift): string {
        $html = '<!doctype html><html><head><meta charset="utf-8"><title>Receipt ' . Helpers::escape($gift['reference_no']) . '</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;color:#111827;padding:40px} .brand{color:#C41E3A} table{width:100%;border-collapse:collapse;margin-top:24px}td{border:1px solid #e5e7eb;padding:12px}.muted{color:#6b7280}</style></head><body>';
        $html .= '<h1 class="brand">' . Helpers::escape(Settings::get('site_name', SITE_NAME)) . '</h1>';
        $html .= '<h2>Giving Receipt</h2><p class="muted">This is an official receipt from RCCG Prince and Princess Parish.</p>';
        $html .= '<table>';
        $rows = [
            'Receipt Number' => $gift['reference_no'],
            'Date' => Helpers::formatDate($gift['giving_date']),
            'Giver' => $gift['giver_name'] ?: 'Anonymous',
            'Giving Type' => ucfirst($gift['giving_type']),
            'Amount' => Helpers::currency((float) $gift['amount']),
            'Status' => ucfirst($gift['payment_status']),
        ];
        foreach ($rows as $label => $value) {
            $html .= '<tr><td><strong>' . Helpers::escape($label) . '</strong></td><td>' . Helpers::escape((string) $value) . '</td></tr>';
        }
        $html .= '</table></body></html>';

        return $html;
    }

    public static function queueEmail(array $gift, string $receiptPath): void {
        if (empty($gift['giver_email'])) {
            return;
        }

        $receiptUrl = BASE_URL . '/' . ltrim($receiptPath, '/');
        Database::insert('sms_emails_log', [
            'type' => 'email',
            'recipient' => $gift['giver_email'],
            'subject' => 'Giving receipt - ' . $gift['reference_no'],
            'body' => 'Thank you for giving. Your receipt is available here: ' . $receiptUrl,
            'status' => 'pending',
        ]);
    }
}
