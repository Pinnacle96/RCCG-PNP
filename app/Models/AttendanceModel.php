<?php

namespace App\Models;

class AttendanceModel extends \Model {
    protected string $table = 'attendance';

    public function presentForService(int $serviceId): array {
        return \Database::fetchAll(
            "SELECT attendance.*, members.member_code, members.first_name, members.last_name, members.email, members.phone
             FROM attendance
             INNER JOIN members ON members.id = attendance.member_id
             WHERE attendance.service_id = ?
             ORDER BY attendance.check_in_time DESC, attendance.id DESC",
            [$serviceId]
        );
    }

    public function searchableMembers(int $serviceId, string $search = '', int $limit = 25): array {
        $params = [':service_id' => $serviceId];
        $where = "members.membership_status = 'active'";
        if ($search !== '') {
            $where .= " AND (members.member_code LIKE :search OR members.first_name LIKE :search OR members.last_name LIKE :search OR members.email LIKE :search OR members.phone LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        return \Database::fetchAll(
            "SELECT members.*
             FROM members
             WHERE {$where}
             AND NOT EXISTS (
                SELECT 1 FROM attendance
                WHERE attendance.service_id = :service_id
                AND attendance.member_id = members.id
             )
             ORDER BY members.first_name ASC, members.last_name ASC
             LIMIT " . (int) $limit,
            $params
        );
    }

    public function mark(int $serviceId, int $memberId, int $markedBy, string $method = 'manual'): void {
        $existing = \Database::fetchOne(
            'SELECT id FROM attendance WHERE service_id = ? AND member_id = ? LIMIT 1',
            [$serviceId, $memberId]
        );

        if ($existing) {
            return;
        }

        \Database::insert('attendance', [
            'service_id' => $serviceId,
            'member_id' => $memberId,
            'check_in_time' => date('Y-m-d H:i:s'),
            'method' => $method,
            'marked_by' => $markedBy,
        ]);
    }

    public function remove(int $serviceId, int $memberId): void {
        \Database::delete('attendance', 'service_id = :service_id AND member_id = :member_id', [
            ':service_id' => $serviceId,
            ':member_id' => $memberId,
        ]);
    }

    public function totalsByMethod(int $serviceId): array {
        $rows = \Database::fetchAll(
            'SELECT method, COUNT(*) total FROM attendance WHERE service_id = ? GROUP BY method',
            [$serviceId]
        );
        $totals = ['manual' => 0, 'qr' => 0, 'self' => 0];
        foreach ($rows as $row) {
            $totals[$row['method']] = (int) $row['total'];
        }
        return $totals;
    }

    /* -------------------------------------------------------------- */
    /* Member portal (SSOT §8 — My Attendance)                       */
    /* -------------------------------------------------------------- */

    public function memberHistory(int $memberId, int $limit = 50): array {
        return \Database::fetchAll(
            "SELECT a.check_in_time, a.method,
                    s.service_type, s.service_date, s.theme
             FROM attendance a
             INNER JOIN services s ON s.id = a.service_id
             WHERE a.member_id = ?
             ORDER BY s.service_date DESC, a.id DESC
             LIMIT " . (int) $limit,
            [$memberId]
        );
    }

    public function memberMonthly(int $memberId, int $months = 12): array {
        return \Database::fetchAll(
            "SELECT DATE_FORMAT(s.service_date, '%Y-%m') ym, COUNT(*) cnt
             FROM attendance a
             INNER JOIN services s ON s.id = a.service_id
             WHERE a.member_id = ? AND s.service_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY ym ORDER BY ym ASC",
            [$memberId, $months]
        );
    }

    /** Attendance rate for a member across services held in the given period. */
    public function memberRate(int $memberId, string $from, string $to): array {
        $totalServices = (int) \Database::fetchColumn(
            'SELECT COUNT(*) FROM services WHERE service_date BETWEEN ? AND ?',
            [$from, $to]
        );
        $attended = (int) \Database::fetchColumn(
            "SELECT COUNT(*) FROM attendance a
             INNER JOIN services s ON s.id = a.service_id
             WHERE a.member_id = ? AND s.service_date BETWEEN ? AND ?",
            [$memberId, $from, $to]
        );
        $rate = $totalServices > 0 ? round(($attended / $totalServices) * 100) : 0;
        return ['attended' => $attended, 'total' => $totalServices, 'rate' => (int) $rate];
    }
}
