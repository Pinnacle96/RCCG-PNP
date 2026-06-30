<?php

namespace App\Controllers\Member;

class AttendanceController extends \Controller {
    use LinksMember;

    private \App\Models\AttendanceModel $attendance;

    public function __construct() {
        parent::__construct();
        $this->attendance = new \App\Models\AttendanceModel();
    }

    /** GET /portal/attendance — history table, monthly chart, attendance rate. */
    public function index(): void {
        $this->requireAuth();
        $member = $this->requireLinkedMember('My Attendance');
        $memberId = (int) $member['id'];

        $yearStart = date('Y-01-01');
        $today = date('Y-m-d');

        $this->view('member.attendance', [
            'title' => 'My Attendance',
            'page_title' => 'My Attendance',
            'rows' => $this->attendance->memberHistory($memberId, 100),
            'monthly' => $this->attendance->memberMonthly($memberId, 12),
            'rate' => $this->attendance->memberRate($memberId, $yearStart, $today),
            'currentYear' => date('Y'),
        ], 'member');
    }
}
