<?php

namespace App\Models;

class GivingModel extends \Model {
    protected string $table = 'giving';

    public function reference(): string {
        return 'RPP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function findByReference(string $reference): ?array {
        return $this->findBy('reference_no', $reference);
    }

    public function markSuccess(string $reference, ?string $gatewayRef = null): ?array {
        $gift = $this->findByReference($reference);
        if (!$gift) {
            return null;
        }

        if ($gift['payment_status'] !== 'success') {
            $this->update((int) $gift['id'], [
                'payment_status' => 'success',
                'gateway_ref' => $gatewayRef ?: $gift['gateway_ref'],
            ]);
        }

        return $this->findByReference($reference);
    }

    public function queueReceipt(array $gift): string {
        // The payment has already succeeded and the gift row is persisted, so a
        // receipt/email failure must never bubble up and 500 the giver. Generate
        // best-effort and log on failure; the receipt can be regenerated later.
        try {
            $receiptPath = \Receipt::generate($gift);
            $this->update((int) $gift['id'], ['receipt_path' => $receiptPath]);
            $gift['receipt_path'] = $receiptPath;
            \Receipt::queueEmail($gift, $receiptPath);
            return $receiptPath;
        } catch (\Throwable $e) {
            error_log('Receipt generation failed for gift ' . ($gift['reference_no'] ?? $gift['id'] ?? '?') . ': ' . $e->getMessage());
            return '';
        }
    }

    /* -------------------------------------------------------------- */
    /* Admin transaction list                                         */
    /* -------------------------------------------------------------- */

    public function paginate(array $filters, int $limit, int $offset): array {
        [$where, $params] = $this->filterClause($filters);
        return \Database::fetchAll(
            "SELECT g.*, CONCAT(m.first_name, ' ', m.last_name) AS member_name
             FROM giving g
             LEFT JOIN members m ON m.id = g.member_id
             {$where}
             ORDER BY g.giving_date DESC, g.id DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    public function totalFiltered(array $filters): int {
        [$where, $params] = $this->filterClause($filters);
        return (int) \Database::fetchColumn("SELECT COUNT(*) FROM giving g {$where}", $params);
    }

    public function sumFiltered(array $filters): float {
        [$where, $params] = $this->filterClause($filters);
        $base = $where === '' ? "WHERE g.payment_status = 'success'" : $where . " AND g.payment_status = 'success'";
        return (float) \Database::fetchColumn("SELECT COALESCE(SUM(g.amount),0) FROM giving g {$base}", $params);
    }

    private function filterClause(array $filters): array {
        $clauses = [];
        $params = [];

        $type = $filters['type'] ?? '';
        if ($type !== '') { $clauses[] = 'g.giving_type = ?'; $params[] = $type; }

        $method = $filters['method'] ?? '';
        if ($method !== '') { $clauses[] = 'g.giving_method = ?'; $params[] = $method; }

        $status = $filters['status'] ?? '';
        if ($status !== '') { $clauses[] = 'g.payment_status = ?'; $params[] = $status; }

        $from = $filters['from'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $clauses[] = 'g.giving_date >= ?'; $params[] = $from; }
        $to = $filters['to'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $clauses[] = 'g.giving_date <= ?'; $params[] = $to; }

        $search = $filters['q'] ?? '';
        if ($search !== '') {
            $clauses[] = '(g.reference_no LIKE ? OR g.giver_name LIKE ? OR g.giver_email LIKE ? OR g.giver_phone LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term, $term);
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }

    /* -------------------------------------------------------------- */
    /* Reports                                                        */
    /* -------------------------------------------------------------- */

    public function totalBetween(string $from, string $to): float {
        return (float) \Database::fetchColumn(
            "SELECT COALESCE(SUM(amount),0) FROM giving WHERE payment_status = 'success' AND giving_date BETWEEN ? AND ?",
            [$from, $to]
        );
    }

    public function byType(string $from, string $to): array {
        return \Database::fetchAll(
            "SELECT giving_type, COALESCE(SUM(amount),0) total, COUNT(*) cnt
             FROM giving WHERE payment_status = 'success' AND giving_date BETWEEN ? AND ?
             GROUP BY giving_type ORDER BY total DESC",
            [$from, $to]
        );
    }

    public function monthlyTrend(int $months = 12): array {
        return \Database::fetchAll(
            "SELECT DATE_FORMAT(giving_date, '%Y-%m') ym, COALESCE(SUM(amount),0) total
             FROM giving
             WHERE payment_status = 'success' AND giving_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY ym ORDER BY ym ASC",
            [$months]
        );
    }

    public function topGivers(string $from, string $to, int $limit = 10): array {
        return \Database::fetchAll(
            "SELECT g.member_id, COALESCE(CONCAT(m.first_name,' ',m.last_name), g.giver_name, 'Anonymous') name,
                    COALESCE(SUM(g.amount),0) total, COUNT(*) cnt
             FROM giving g
             LEFT JOIN members m ON m.id = g.member_id
             WHERE g.payment_status = 'success' AND g.giving_date BETWEEN ? AND ?
             GROUP BY g.member_id, name
             ORDER BY total DESC LIMIT " . (int) $limit,
            [$from, $to]
        );
    }

    public function memberStatement(int $memberId, string $from, string $to): array {
        return \Database::fetchAll(
            "SELECT reference_no, amount, giving_type, giving_method, giving_date
             FROM giving
             WHERE member_id = ? AND payment_status = 'success' AND giving_date BETWEEN ? AND ?
             ORDER BY giving_date ASC",
            [$memberId, $from, $to]
        );
    }

    /* -------------------------------------------------------------- */
    /* Member portal (SSOT §8 — My Giving)                            */
    /* -------------------------------------------------------------- */

    public function memberPaginate(int $memberId, array $filters, int $limit, int $offset): array {
        [$where, $params] = $this->memberClause($memberId, $filters);
        return \Database::fetchAll(
            "SELECT reference_no, amount, giving_type, giving_method, payment_status, giving_date, receipt_path
             FROM giving
             {$where}
             ORDER BY giving_date DESC, id DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    public function memberCount(int $memberId, array $filters): int {
        [$where, $params] = $this->memberClause($memberId, $filters);
        return (int) \Database::fetchColumn("SELECT COUNT(*) FROM giving {$where}", $params);
    }

    public function memberTotalBetween(int $memberId, string $from, string $to): float {
        return (float) \Database::fetchColumn(
            "SELECT COALESCE(SUM(amount),0) FROM giving
             WHERE member_id = ? AND payment_status = 'success' AND giving_date BETWEEN ? AND ?",
            [$memberId, $from, $to]
        );
    }

    private function memberClause(int $memberId, array $filters): array {
        $clauses = ['member_id = ?'];
        $params = [$memberId];

        $type = $filters['type'] ?? '';
        if ($type !== '') { $clauses[] = 'giving_type = ?'; $params[] = $type; }

        $from = $filters['from'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $clauses[] = 'giving_date >= ?'; $params[] = $from; }
        $to = $filters['to'] ?? '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $clauses[] = 'giving_date <= ?'; $params[] = $to; }

        return ['WHERE ' . implode(' AND ', $clauses), $params];
    }
}
