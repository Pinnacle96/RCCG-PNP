<?php
$base = BASE_URL . '/admin/giving';
$trendLabels = array_map(static fn($r) => $r['ym'], $trend);
$trendData = array_map(static fn($r) => (float) $r['total'], $trend);
$typeLabels = array_map(static fn($r) => ucfirst($r['giving_type']), $byType);
$typeData = array_map(static fn($r) => (float) $r['total'], $byType);
?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Giving Reports</h1>
            <p class="text-gray-600">Trends, breakdowns, and per-member statements.</p>
        </div>
        <a href="<?php echo $base; ?>" class="btn-primary bg-rccg-navy text-white px-5 py-3">Transactions</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card"><p class="text-sm text-gray-500">This Week</p><p class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($summary['week'])); ?></p></div>
        <div class="card"><p class="text-sm text-gray-500">This Month</p><p class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($summary['month'])); ?></p></div>
        <div class="card"><p class="text-sm text-gray-500">This Year</p><p class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($summary['year'])); ?></p></div>
    </div>

    <div class="card">
        <form method="get" action="<?php echo $base; ?>/reports" class="flex flex-wrap items-end gap-3">
            <div><label class="mb-1 block text-sm font-semibold text-gray-700">From</label><input class="form-input" type="date" name="from" value="<?php echo Helpers::escape($from); ?>"></div>
            <div><label class="mb-1 block text-sm font-semibold text-gray-700">To</label><input class="form-input" type="date" name="to" value="<?php echo Helpers::escape($to); ?>"></div>
            <label class="flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" name="anonymize" value="1" <?php echo $anonymize ? 'checked' : ''; ?>>Anonymize givers</label>
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">Apply</button>
        </form>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">Monthly Trend (12 mo)</h2><canvas id="trendChart" height="160"></canvas></div>
        <div class="card"><h2 class="mb-4 text-lg font-bold text-rccg-navy">By Type</h2><canvas id="typeChart" height="160"></canvas></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card overflow-x-auto">
            <h2 class="mb-4 text-lg font-bold text-rccg-navy">Top Givers</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500"><th class="py-2 pr-4">Giver</th><th class="py-2 pr-4">Gifts</th><th class="py-2">Total</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($topGivers)): ?><tr><td colspan="3" class="py-6 text-center text-gray-500">No data.</td></tr><?php endif; ?>
                    <?php foreach ($topGivers as $i => $g): ?>
                        <tr>
                            <td class="py-3 pr-4 text-sm text-gray-700"><?php echo $anonymize ? 'Giver #' . ($i + 1) : Helpers::escape($g['name']); ?></td>
                            <td class="py-3 pr-4 text-sm text-gray-600"><?php echo (int) $g['cnt']; ?></td>
                            <td class="py-3 text-sm font-semibold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency((float) $g['total'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 class="mb-4 text-lg font-bold text-rccg-navy">Member Statement (PDF)</h2>
            <form method="get" action="#" id="statementForm" class="space-y-3">
                <select class="form-input" id="statementMember">
                    <option value="">Select a member</option>
                    <?php foreach ($members as $m): ?><option value="<?php echo (int) $m['id']; ?>"><?php echo Helpers::escape($m['name']); ?></option><?php endforeach; ?>
                </select>
                <div class="flex gap-3">
                    <input class="form-input" type="date" id="stFrom" value="<?php echo Helpers::escape($from); ?>">
                    <input class="form-input" type="date" id="stTo" value="<?php echo Helpers::escape($to); ?>">
                </div>
                <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3" type="submit"><i class="fas fa-file-pdf mr-2"></i>Download Statement</button>
            </form>
        </div>
    </div>
</section>

<script>
(function () {
    const fmt = v => '<?php echo CURRENCY_SYMBOL; ?>' + Number(v).toLocaleString();
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: { labels: <?php echo json_encode($trendLabels); ?>, datasets: [{ label: 'Giving', data: <?php echo json_encode($trendData); ?>, borderColor: '#C41E3A', backgroundColor: 'rgba(196,30,58,0.1)', fill: true, tension: 0.3 }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: fmt } } } }
    });
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: { labels: <?php echo json_encode($typeLabels); ?>, datasets: [{ data: <?php echo json_encode($typeData); ?>, backgroundColor: ['#C41E3A','#D4AF37','#0A1628','#8B0000','#16A34A','#D97706','#6B7280','#2563EB','#7C3AED'] }] }
    });

    document.getElementById('statementForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('statementMember').value;
        if (!id) return;
        const from = document.getElementById('stFrom').value, to = document.getElementById('stTo').value;
        window.open('<?php echo $base; ?>/statement/' + id + '?from=' + from + '&to=' + to, '_blank');
    });
})();
</script>
