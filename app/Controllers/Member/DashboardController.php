<?php

namespace App\Controllers\Member;

class DashboardController extends \Controller {
    use LinksMember;

    public function index(): void {
        $this->requireAuth();

        $member = $this->linkedMember();
        $stats = null;

        if ($member) {
            $memberId = (int) $member['id'];
            $yearStart = date('Y-01-01');
            $today = date('Y-m-d');
            $giving = new \App\Models\GivingModel();
            $attendance = new \App\Models\AttendanceModel();

            $stats = [
                'giving_year' => $giving->memberTotalBetween($memberId, $yearStart, $today),
                'attendance' => $attendance->memberRate($memberId, $yearStart, $today),
            ];
        }

        $this->view('member.dashboard', [
            'title' => 'Member Portal',
            'page_title' => 'Member Dashboard',
            'member' => $member,
            'stats' => $stats,
            'currentYear' => date('Y'),
        ], 'member');
    }
}
