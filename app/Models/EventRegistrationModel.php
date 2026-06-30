<?php

namespace App\Models;

class EventRegistrationModel extends \Model {
    protected string $table = 'event_registrations';

    public function forEvent(int $eventId): array {
        return \Database::fetchAll('SELECT * FROM event_registrations WHERE event_id = ? ORDER BY created_at DESC', [$eventId]);
    }

    public function countForEvent(int $eventId): int {
        return (int) \Database::fetchColumn('SELECT COUNT(*) FROM event_registrations WHERE event_id = ?', [$eventId]);
    }
}
