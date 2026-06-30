<?php

namespace App\Controllers\Member;

class GivingController extends \Controller {
    use LinksMember;

    private const TYPES = ['tithe', 'offering', 'seed', 'project', 'welfare', 'mission', 'thanksgiving', 'vow', 'other'];

    private \App\Models\GivingModel $giving;

    public function __construct() {
        parent::__construct();
        $this->giving = new \App\Models\GivingModel();
    }

    /** GET /portal/giving — giving history with filters and yearly total. */
    public function index(): void {
        $this->requireAuth();
        $member = $this->requireLinkedMember('My Giving');
        $memberId = (int) $member['id'];

        $filters = [
            'type' => $this->enumOrEmpty('type', self::TYPES),
            'from' => $this->validDate((string) $this->input('from', ''), ''),
            'to' => $this->validDate((string) $this->input('to', ''), ''),
        ];

        $page = max(1, (int) $this->input('page', 1));
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $total = $this->giving->memberCount($memberId, $filters);

        $yearStart = date('Y-01-01');
        $today = date('Y-m-d');

        $this->view('member.giving', [
            'title' => 'My Giving',
            'page_title' => 'My Giving',
            'rows' => $this->giving->memberPaginate($memberId, $filters, $limit, $offset),
            'filters' => $filters,
            'types' => self::TYPES,
            'yearTotal' => $this->giving->memberTotalBetween($memberId, $yearStart, $today),
            'currentYear' => date('Y'),
            'page' => $page,
            'total' => $total,
            'totalPages' => max(1, (int) ceil($total / $limit)),
        ], 'member');
    }

    /** GET /portal/giving/statement — annual statement PDF for the logged-in member. */
    public function statement(): void {
        $this->requireAuth();
        $member = $this->requireLinkedMember('My Giving');
        $memberId = (int) $member['id'];

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

        \Audit::log('export', 'giving', 'Member downloaded own giving statement #' . $memberId);
        $this->streamPdf($html, 'my-statement-' . $member['member_code'] . '.pdf');
    }

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

    private function enumOrEmpty(string $key, array $allowed): string {
        $value = (string) $this->input($key, '');
        return in_array($value, $allowed, true) ? $value : '';
    }

    private function validDate(string $value, string $fallback): string {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $fallback;
    }
}
