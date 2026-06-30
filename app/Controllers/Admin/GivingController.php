<?php

namespace App\Controllers\Admin;

class GivingController extends \Controller {
    private \App\Models\GivingModel $giving;

    private const TYPES = ['tithe', 'offering', 'seed', 'project', 'welfare', 'mission', 'thanksgiving', 'vow', 'other'];
    private const METHODS = ['cash', 'bank_transfer', 'pos', 'online', 'cheque'];
    private const STATUSES = ['pending', 'success', 'failed', 'reversed'];

    public function __construct() {
        parent::__construct();
        $this->giving = new \App\Models\GivingModel();
    }

    /** GET /admin/giving — transactions table with filters. */
    public function index(): void {
        $this->requireAdmin();
        $filters = $this->filters();
        $page = max(1, (int) $this->input('page', 1));
        $limit = ADMIN_ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->giving->totalFiltered($filters);

        $this->view('admin.giving.index', [
            'title' => 'Giving Transactions',
            'page_title' => 'Giving Transactions',
            'rows' => $this->giving->paginate($filters, $limit, $offset),
            'filters' => $filters,
            'filteredSum' => $this->giving->sumFiltered($filters),
            'types' => self::TYPES,
            'methods' => self::METHODS,
            'statuses' => self::STATUSES,
            'page' => $page,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'admin');
    }

    /** GET /admin/giving/record — manual giving entry form. */
    public function create(): void {
        $this->requireAdmin();
        $this->view('admin.giving.form', [
            'title' => 'Record Giving',
            'page_title' => 'Record Giving',
            'types' => self::TYPES,
            'methods' => self::METHODS,
        ], 'admin');
    }

    /** POST /admin/giving/record — store a manual gift. */
    public function store(): void {
        $this->requireAdmin();
        $this->verifyCsrf();

        $amount = (float) $this->input('amount', 0);
        $type = (string) $this->input('giving_type', '');
        $method = (string) $this->input('giving_method', 'cash');
        if ($amount <= 0 || !in_array($type, self::TYPES, true) || !in_array($method, self::METHODS, true)) {
            $this->setFlash('error', 'Enter a valid amount, giving type, and method.');
            $this->redirect(BASE_URL . '/admin/giving/record');
        }

        $givingDate = (string) $this->input('giving_date', date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $givingDate)) {
            $givingDate = date('Y-m-d');
        }
        $memberId = (int) $this->input('member_id', 0);
        $reference = $this->giving->reference();

        $this->giving->create([
            'reference_no' => $reference,
            'member_id' => $memberId > 0 ? $memberId : null,
            'giver_name' => ($this->input('giver_name') ?: null),
            'giver_email' => ($this->input('giver_email') ?: null),
            'giver_phone' => ($this->input('giver_phone') ?: null),
            'amount' => $amount,
            'currency' => CURRENCY,
            'giving_type' => $type,
            'giving_method' => $method,
            'payment_status' => 'success',
            'description' => ($this->input('description') ?: null),
            'giving_date' => $givingDate,
            'recorded_by' => $this->userId,
        ]);

        \Audit::log('create', 'giving', 'Recorded manual giving ' . $reference);
        $this->setFlash('success', 'Giving recorded: ' . $reference);
        $this->redirect(BASE_URL . '/admin/giving');
    }

    /** POST /admin/giving/verify/{id} — re-verify a pending online payment via Paystack. */
    public function verify(): void {
        $this->requireAdmin();
        $this->verifyCsrf();
        $gift = $this->findOr404();

        if ($gift['payment_status'] === 'success') {
            $this->setFlash('success', 'This transaction is already verified.');
            $this->redirect(BASE_URL . '/admin/giving');
        }
        if (empty($gift['gateway_ref']) && empty($gift['reference_no'])) {
            $this->setFlash('error', 'No gateway reference available to verify.');
            $this->redirect(BASE_URL . '/admin/giving');
        }

        try {
            $response = \Paystack::verify($gift['gateway_ref'] ?: $gift['reference_no']);
            if (($response['data']['status'] ?? '') === 'success') {
                $updated = $this->giving->markSuccess($gift['reference_no'], $response['data']['reference'] ?? null);
                if ($updated) {
                    $this->giving->queueReceipt($updated);
                }
                \Audit::log('verify', 'giving', 'Verified payment ' . $gift['reference_no']);
                $this->setFlash('success', 'Payment verified and marked successful.');
            } else {
                $this->setFlash('error', 'Gateway reports this payment is not successful yet.');
            }
        } catch (\Throwable $e) {
            $this->setFlash('error', 'Verification failed: ' . $e->getMessage());
        }
        $this->redirect(BASE_URL . '/admin/giving');
    }

    /** GET /admin/giving/reports — summary, charts data, top givers. */
    public function reports(): void {
        $this->requireAdmin();
        $from = $this->validDate((string) $this->input('from', ''), date('Y-01-01'));
        $to = $this->validDate((string) $this->input('to', ''), date('Y-m-d'));
        $anonymize = (bool) $this->input('anonymize', 0);

        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');
        $yearStart = date('Y-01-01');
        $today = date('Y-m-d');

        $this->view('admin.giving.reports', [
            'title' => 'Giving Reports',
            'page_title' => 'Giving Reports',
            'from' => $from,
            'to' => $to,
            'anonymize' => $anonymize,
            'summary' => [
                'week' => $this->giving->totalBetween($weekStart, $today),
                'month' => $this->giving->totalBetween($monthStart, $today),
                'year' => $this->giving->totalBetween($yearStart, $today),
            ],
            'byType' => $this->giving->byType($from, $to),
            'trend' => $this->giving->monthlyTrend(12),
            'topGivers' => $this->giving->topGivers($from, $to),
            'members' => \Database::fetchAll("SELECT id, CONCAT(first_name,' ',last_name) name FROM members ORDER BY first_name, last_name"),
        ], 'admin');
    }

    /** GET /admin/giving/statement/{id} — per-member annual statement PDF. */
    public function statement(): void {
        $this->requireAdmin();
        $memberId = (int) ($_GET['id'] ?? 0);
        $member = $memberId > 0 ? \Database::fetchOne('SELECT * FROM members WHERE id = ?', [$memberId]) : null;
        if (!$member) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Member Not Found'], 'public');
            exit;
        }
        $from = $this->validDate((string) $this->input('from', ''), date('Y-01-01'));
        $to = $this->validDate((string) $this->input('to', ''), date('Y-m-d'));
        $rows = $this->giving->memberStatement($memberId, $from, $to);
        $total = array_sum(array_map(static fn($r) => (float) $r['amount'], $rows));

        $name = $member['first_name'] . ' ' . $member['last_name'];
        $html = '<!doctype html><html><head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;color:#111827;padding:32px}.brand{color:#C41E3A}table{width:100%;border-collapse:collapse;margin-top:20px}td,th{border:1px solid #e5e7eb;padding:10px;text-align:left;font-size:13px}.muted{color:#6b7280}</style></head><body>';
        $html .= '<h1 class="brand">' . \Helpers::escape(\Settings::get('site_name', SITE_NAME)) . '</h1>';
        $html .= '<h2>Annual Giving Statement</h2>';
        $html .= '<p class="muted">Member: <strong>' . \Helpers::escape($name) . '</strong> (' . \Helpers::escape($member['member_code']) . ')<br>Period: ' . \Helpers::escape($from) . ' to ' . \Helpers::escape($to) . '</p>';
        $html .= '<table><thead><tr><th>Date</th><th>Reference</th><th>Type</th><th>Method</th><th>Amount</th></tr></thead><tbody>';
        foreach ($rows as $r) {
            $html .= '<tr><td>' . \Helpers::escape(\Helpers::formatDate($r['giving_date'])) . '</td><td>' . \Helpers::escape($r['reference_no']) . '</td><td>' . \Helpers::escape(ucfirst($r['giving_type'])) . '</td><td>' . \Helpers::escape(ucfirst(str_replace('_', ' ', $r['giving_method']))) . '</td><td>' . \Helpers::escape(\Helpers::currency((float) $r['amount'])) . '</td></tr>';
        }
        $html .= '</tbody><tfoot><tr><th colspan="4">Total</th><th>' . \Helpers::escape(\Helpers::currency($total)) . '</th></tr></tfoot></table>';
        $html .= '<p class="muted" style="margin-top:24px">This is an official giving statement from RCCG Prince and Princess Parish.</p>';
        $html .= '</body></html>';

        \Audit::log('export', 'giving', 'Generated giving statement for member #' . $memberId);
        $this->streamPdf($html, 'statement-' . $member['member_code'] . '.pdf');
    }

    /** GET /admin/giving/export?format=csv|xlsx — export the filtered transactions. */
    public function export(): void {
        $this->requireAdmin();
        $filters = $this->filters();
        $rows = $this->giving->paginate($filters, 100000, 0);

        $headers = ['Reference', 'Date', 'Giver', 'Member', 'Amount', 'Type', 'Method', 'Status'];
        $data = array_map(static function (array $r): array {
            return [
                $r['reference_no'],
                $r['giving_date'],
                $r['giver_name'] ?: '',
                $r['member_name'] ?: '',
                $r['amount'],
                $r['giving_type'],
                $r['giving_method'],
                $r['payment_status'],
            ];
        }, $rows);

        $isXlsx = strtolower((string) $this->input('format', 'csv')) === 'xlsx';
        \Audit::log('export', 'giving', 'Exported giving transactions ' . ($isXlsx ? 'XLSX' : 'CSV'));

        if ($isXlsx) {
            \Spreadsheet::stream($headers, $data, 'giving-' . date('Ymd-His') . '.xlsx', 'Giving');
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="giving-' . date('Ymd-His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, $headers);
        foreach ($data as $r) {
            fputcsv($out, $r);
        }
        fclose($out);
        exit;
    }

    /* ------------------------------------------------------------------ */

    private function streamPdf(string $html, string $filename): void {
        if (class_exists(\Dompdf\Dompdf::class)) {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    private function filters(): array {
        return [
            'type' => $this->enumOrEmpty('type', self::TYPES),
            'method' => $this->enumOrEmpty('method', self::METHODS),
            'status' => $this->enumOrEmpty('status', self::STATUSES),
            'from' => (string) $this->input('from', ''),
            'to' => (string) $this->input('to', ''),
            'q' => trim((string) $this->input('q', '')),
        ];
    }

    private function enumOrEmpty(string $key, array $allowed): string {
        $value = (string) $this->input($key, '');
        return in_array($value, $allowed, true) ? $value : '';
    }

    private function validDate(string $value, string $fallback): string {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $fallback;
    }

    private function requireAdmin(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
    }

    private function findOr404(): array {
        $id = (int) ($_GET['id'] ?? 0);
        $gift = $id > 0 ? $this->giving->find($id) : null;
        if (!$gift) {
            http_response_code(404);
            $this->view('frontend.404', ['title' => 'Transaction Not Found'], 'public');
            exit;
        }
        return $gift;
    }
}
