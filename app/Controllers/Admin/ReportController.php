<?php

namespace App\Controllers\Admin;

class ReportController extends \Controller {
    public function index(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
        $data = $this->buildReport();
        $data['title'] = 'Reports & Analytics';
        $data['page_title'] = 'Reports & Analytics';
        $this->view('admin.reports.index', $data, 'admin');
    }

    /** GET /admin/reports/export?format=pdf|xlsx — export the same figures. */
    public function export(): void {
        $this->requireRole([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PASTOR, ROLE_DEACON]);
        $d = $this->buildReport();

        if (strtolower((string) $this->input('format', 'pdf')) === 'xlsx') {
            $this->exportXlsx($d);
            return;
        }

        $html = '<!doctype html><html><head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;color:#111827;padding:28px}.brand{color:#C41E3A}h2{margin-top:24px}table{width:100%;border-collapse:collapse;margin-top:8px}td,th{border:1px solid #e5e7eb;padding:8px;text-align:left;font-size:12px}.muted{color:#6b7280}</style></head><body>';
        $html .= '<h1 class="brand">' . \Helpers::escape(\Settings::get('site_name', SITE_NAME)) . '</h1>';
        $html .= '<h2>Reports &amp; Analytics</h2><p class="muted">Period: ' . \Helpers::escape($d['from']) . ' to ' . \Helpers::escape($d['to']) . '</p>';

        $html .= '<table><tr><th>Total Members</th><td>' . (int) $d['totals']['members'] . '</td><th>Active Members</th><td>' . (int) $d['totals']['active'] . '</td></tr>';
        $html .= '<tr><th>New (period)</th><td>' . (int) $d['totals']['new'] . '</td><th>Giving (period)</th><td>' . \Helpers::escape(\Helpers::currency($d['totals']['giving'])) . '</td></tr></table>';

        $html .= '<h2>Membership Growth</h2><table><tr><th>Month</th><th>New Members</th></tr>';
        foreach ($d['growth'] as $r) { $html .= '<tr><td>' . \Helpers::escape($r['ym']) . '</td><td>' . (int) $r['cnt'] . '</td></tr>'; }
        $html .= '</table>';

        $html .= '<h2>Ministry Distribution</h2><table><tr><th>Ministry</th><th>Members</th></tr>';
        foreach ($d['ministries'] as $r) { $html .= '<tr><td>' . \Helpers::escape($r['name']) . '</td><td>' . (int) $r['cnt'] . '</td></tr>'; }
        $html .= '</table>';

        $html .= '<h2>Demographics</h2><table><tr><th>Gender</th><th>Count</th></tr>';
        foreach ($d['gender'] as $r) { $html .= '<tr><td>' . \Helpers::escape(ucfirst($r['gender'])) . '</td><td>' . (int) $r['cnt'] . '</td></tr>'; }
        $html .= '</table>';

        $html .= '</body></html>';

        \Audit::log('export', 'reports', 'Exported analytics report PDF');

        if (class_exists(\Dompdf\Dompdf::class)) {
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', false);
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();
            $dompdf->stream('report-' . date('Ymd') . '.pdf', ['Attachment' => true]);
            exit;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    /** Flatten the report figures into a single labelled .xlsx sheet. */
    private function exportXlsx(array $d): void {
        $rows = [];
        $rows[] = ['Period', $d['from'] . ' to ' . $d['to']];
        $rows[] = ['', ''];
        $rows[] = ['Summary', ''];
        $rows[] = ['Total Members', (int) $d['totals']['members']];
        $rows[] = ['Active Members', (int) $d['totals']['active']];
        $rows[] = ['New (period)', (int) $d['totals']['new']];
        $rows[] = ['Giving (period)', (float) $d['totals']['giving']];
        $rows[] = ['', ''];
        $rows[] = ['Membership Growth', 'New Members'];
        foreach ($d['growth'] as $r) {
            $rows[] = [$r['ym'], (int) $r['cnt']];
        }
        $rows[] = ['', ''];
        $rows[] = ['Ministry Distribution', 'Members'];
        foreach ($d['ministries'] as $r) {
            $rows[] = [$r['name'], (int) $r['cnt']];
        }
        $rows[] = ['', ''];
        $rows[] = ['Gender', 'Count'];
        foreach ($d['gender'] as $r) {
            $rows[] = [ucfirst((string) $r['gender']), (int) $r['cnt']];
        }

        \Audit::log('export', 'reports', 'Exported analytics report XLSX');
        \Spreadsheet::stream(['Metric', 'Value'], $rows, 'report-' . date('Ymd') . '.xlsx', 'Report');
    }

    /* ------------------------------------------------------------------ */

    private function buildReport(): array {
        $from = $this->validDate((string) $this->input('from', ''), date('Y-01-01'));
        $to = $this->validDate((string) $this->input('to', ''), date('Y-m-d'));

        return [
            'from' => $from,
            'to' => $to,
            'totals' => [
                'members' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM members'),
                'active' => (int) \Database::fetchColumn("SELECT COUNT(*) FROM members WHERE membership_status = 'active'"),
                'new' => (int) \Database::fetchColumn('SELECT COUNT(*) FROM members WHERE created_at BETWEEN ? AND ?', [$from . ' 00:00:00', $to . ' 23:59:59']),
                'giving' => (float) \Database::fetchColumn("SELECT COALESCE(SUM(amount),0) FROM giving WHERE payment_status = 'success' AND giving_date BETWEEN ? AND ?", [$from, $to]),
            ],
            'growth' => \Database::fetchAll(
                "SELECT DATE_FORMAT(created_at, '%Y-%m') ym, COUNT(*) cnt
                 FROM members WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                 GROUP BY ym ORDER BY ym ASC"
            ),
            'attendanceTrend' => \Database::fetchAll(
                "SELECT DATE_FORMAT(s.service_date, '%Y-%m') ym, COUNT(a.id) cnt
                 FROM services s LEFT JOIN attendance a ON a.service_id = s.id
                 WHERE s.service_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                 GROUP BY ym ORDER BY ym ASC"
            ),
            'givingTrend' => \Database::fetchAll(
                "SELECT DATE_FORMAT(giving_date, '%Y-%m') ym, COALESCE(SUM(amount),0) total
                 FROM giving WHERE payment_status = 'success' AND giving_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                 GROUP BY ym ORDER BY ym ASC"
            ),
            'ministries' => \Database::fetchAll(
                "SELECT m.name, COUNT(mm.id) cnt
                 FROM ministries m LEFT JOIN ministry_members mm ON mm.ministry_id = m.id AND mm.is_active = 1
                 WHERE m.is_active = 1
                 GROUP BY m.id, m.name ORDER BY cnt DESC"
            ),
            'gender' => \Database::fetchAll('SELECT gender, COUNT(*) cnt FROM members GROUP BY gender'),
            'marital' => \Database::fetchAll("SELECT COALESCE(marital_status,'unknown') marital_status, COUNT(*) cnt FROM members GROUP BY marital_status"),
        ];
    }

    private function validDate(string $value, string $fallback): string {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $fallback;
    }
}
