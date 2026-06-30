<?php
$base = BASE_URL . '/admin/reports';
$growthLabels = array_map(static fn($r) => $r['ym'], $growth);
$growthData = array_map(static fn($r) => (int) $r['cnt'], $growth);
$attLabels = array_map(static fn($r) => $r['ym'], $attendanceTrend);
$attData = array_map(static fn($r) => (int) $r['cnt'], $attendanceTrend);
$giveLabels = array_map(static fn($r) => $r['ym'], $givingTrend);
$giveData = array_map(static fn($r) => (float) $r['total'], $givingTrend);
$minLabels = array_map(static fn($r) => $r['name'], $ministries);
$minData = array_map(static fn($r) => (int) $r['cnt'], $ministries);
$genderLabels = array_map(static fn($r) => ucfirst($r['gender']), $gender);
$genderData = array_map(static fn($r) => (int) $r['cnt'], $gender);
?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Reports &amp; Analytics</h1>
            <p class="text-gray-600">Membership, attendance, giving and demographics.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo $base; ?>/export?from=<?php echo Helpers::escape($from); ?>&to=<?php echo Helpers::escape($to); ?>" class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3"><i class="fas fa-file-pdf mr-2"></i>Export PDF</a>
            <a href="<?php echo $base; ?>/export?format=xlsx&from=<?php echo Helpers::escape($from); ?>&to=<?php echo Helpers::escape($to); ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3"><i class="fas fa-file-excel mr-2"></i>Export Excel</a>
        </div>
    </div>

    <div class="card">
        <form method="get" action="<?php echo $base; ?>" class="flex flex-wrap items-end gap-3">
            <div><label class="mb-1 block text-sm font-semibold text-gray-700">From</label><input class="form-input" type="date" name="from" value="<?php echo Helpers::escape($from); ?>"></div>
            <div><label class="mb-1 block text-sm font-semibold text-gray-700">To</label><input class="form-input" type="date" name="to" value="<?php echo Helpers::escape($to); ?>"></div>
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">Apply</button>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card"><p class="text-sm text-gray-500">Total Members</p><p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $totals['members']; ?></p></div>
        <div class="card"><p class="text-sm text-gray-500">Active Members</p><p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $totals['active']; ?></p></div>
        <div class="card"><p class="text-sm text-gray-500">New (period)</p><p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $totals['new']; ?></p></div>
        <div class="card"><p class="text-sm text-gray-500">Giving (period)</p><p class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($totals['giving'])); ?></p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">Membership Growth (12 mo)</h2><canvas id="growthChart" height="160"></canvas></div>
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">Attendance Trend (12 mo)</h2><canvas id="attChart" height="160"></canvas></div>
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">Giving Trend (12 mo)</h2><canvas id="giveChart" height="160"></canvas></div>
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">Ministry Distribution</h2><canvas id="minChart" height="160"></canvas></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">Gender</h2><canvas id="genderChart" height="140"></canvas></div>
        <div class="card overflow-x-auto">
            <h2 class="mb-4 text-lg font-bold text-rccg-navy">Marital Status</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500"><th class="py-2 pr-4">Status</th><th class="py-2">Members</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($marital)): ?><tr><td colspan="2" class="py-6 text-center text-gray-500">No data.</td></tr><?php endif; ?>
                    <?php foreach ($marital as $r): ?>
                        <tr><td class="py-3 pr-4 text-sm text-gray-700"><?php echo Helpers::escape(ucfirst($r['marital_status'])); ?></td><td class="py-3 text-sm font-semibold text-rccg-navy"><?php echo (int) $r['cnt']; ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
(function () {
    const palette = ['#C41E3A','#D4AF37','#0A1628','#8B0000','#16A34A','#D97706','#6B7280','#2563EB','#7C3AED'];
    const money = v => '<?php echo CURRENCY_SYMBOL; ?>' + Number(v).toLocaleString();
    const bar = (id, labels, data, label) => new Chart(document.getElementById(id), {
        type: 'bar',
        data: { labels, datasets: [{ label, data, backgroundColor: '#C41E3A', borderRadius: 4 }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
    bar('growthChart', <?php echo json_encode($growthLabels); ?>, <?php echo json_encode($growthData); ?>, 'New members');
    bar('attChart', <?php echo json_encode($attLabels); ?>, <?php echo json_encode($attData); ?>, 'Attendance');
    new Chart(document.getElementById('giveChart'), {
        type: 'line',
        data: { labels: <?php echo json_encode($giveLabels); ?>, datasets: [{ label: 'Giving', data: <?php echo json_encode($giveData); ?>, borderColor: '#C41E3A', backgroundColor: 'rgba(196,30,58,0.1)', fill: true, tension: 0.3 }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: money } } } }
    });
    new Chart(document.getElementById('minChart'), {
        type: 'doughnut',
        data: { labels: <?php echo json_encode($minLabels); ?>, datasets: [{ data: <?php echo json_encode($minData); ?>, backgroundColor: palette }] }
    });
    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: { labels: <?php echo json_encode($genderLabels); ?>, datasets: [{ data: <?php echo json_encode($genderData); ?>, backgroundColor: palette }] }
    });
})();
</script>
