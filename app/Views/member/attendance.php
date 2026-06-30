<?php
$monthLabels = [];
$monthData = [];
foreach ($monthly as $m) {
    $monthLabels[] = date('M Y', strtotime($m['ym'] . '-01'));
    $monthData[] = (int) $m['cnt'];
}
?>

<section class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="card">
            <p class="text-sm text-gray-500">Attendance Rate (<?php echo (int) $currentYear; ?>)</p>
            <p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $rate['rate']; ?>%</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Services Attended</p>
            <p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $rate['attended']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Services Held</p>
            <p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $rate['total']; ?></p>
        </div>
    </div>

    <div class="card">
        <h2 class="text-lg font-bold text-rccg-navy mb-4">Monthly Attendance</h2>
        <?php if (empty($monthData)): ?>
            <p class="text-gray-500">No attendance recorded in the last 12 months.</p>
        <?php else: ?>
            <canvas id="attendanceChart" height="100"></canvas>
        <?php endif; ?>
    </div>

    <div class="card overflow-x-auto">
        <h2 class="text-lg font-bold text-rccg-navy mb-4">Attendance History</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Date</th>
                    <th class="py-3 pr-4">Service</th>
                    <th class="py-3 pr-4">Theme</th>
                    <th class="py-3 pr-4">Checked In</th>
                    <th class="py-3 pr-4">Method</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">No attendance records yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(Helpers::formatDate($row['service_date'])); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-700"><?php echo Helpers::escape(ucfirst(str_replace('_', ' ', $row['service_type']))); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape($row['theme'] ?: '—'); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo $row['check_in_time'] ? Helpers::escape(Helpers::formatDateTime($row['check_in_time'])) : '—'; ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(ucfirst($row['method'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if (!empty($monthData)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('attendanceChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($monthLabels); ?>,
            datasets: [{
                label: 'Services attended',
                data: <?php echo json_encode($monthData); ?>,
                backgroundColor: '#C41E3A',
                borderRadius: 4,
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
<?php endif; ?>
