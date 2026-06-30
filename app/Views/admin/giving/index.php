<?php
$base = BASE_URL . '/admin/giving';
$qs = http_build_query(array_filter([
    'type' => $filters['type'], 'method' => $filters['method'], 'status' => $filters['status'],
    'from' => $filters['from'], 'to' => $filters['to'], 'q' => $filters['q'],
]));
$statusBadge = function (string $s): string {
    return [
        'success' => 'bg-green-100 text-green-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'failed' => 'bg-red-100 text-red-700',
        'reversed' => 'bg-gray-100 text-gray-600',
    ][$s] ?? 'bg-gray-100 text-gray-600';
};
?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Giving Transactions</h1>
            <p class="text-gray-600">Filtered successful total: <strong class="text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($filteredSum)); ?></strong></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo $base; ?>/reports" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3"><i class="fas fa-chart-pie mr-2"></i>Reports</a>
            <a href="<?php echo $base; ?>/export?<?php echo $qs; ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3"><i class="fas fa-file-csv mr-2"></i>Export CSV</a>
            <a href="<?php echo $base; ?>/export?<?php echo $qs; ?>&format=xlsx" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3"><i class="fas fa-file-excel mr-2"></i>Export Excel</a>
            <a href="<?php echo $base; ?>/record" class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3"><i class="fas fa-plus mr-2"></i>Record Giving</a>
        </div>
    </div>

    <div class="card">
        <form method="get" action="<?php echo $base; ?>" class="grid gap-3 md:grid-cols-6">
            <input class="form-input md:col-span-2" type="search" name="q" value="<?php echo Helpers::escape($filters['q']); ?>" placeholder="Reference, giver, email, phone">
            <select class="form-input" name="type">
                <option value="">All types</option>
                <?php foreach ($types as $t): ?><option value="<?php echo $t; ?>" <?php echo $filters['type'] === $t ? 'selected' : ''; ?>><?php echo ucfirst($t); ?></option><?php endforeach; ?>
            </select>
            <select class="form-input" name="method">
                <option value="">All methods</option>
                <?php foreach ($methods as $m): ?><option value="<?php echo $m; ?>" <?php echo $filters['method'] === $m ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $m)); ?></option><?php endforeach; ?>
            </select>
            <select class="form-input" name="status">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $s): ?><option value="<?php echo $s; ?>" <?php echo $filters['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option><?php endforeach; ?>
            </select>
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit"><i class="fas fa-filter mr-2"></i>Filter</button>
            <input class="form-input" type="date" name="from" value="<?php echo Helpers::escape($filters['from']); ?>">
            <input class="form-input" type="date" name="to" value="<?php echo Helpers::escape($filters['to']); ?>">
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Reference</th>
                    <th class="py-3 pr-4">Date</th>
                    <th class="py-3 pr-4">Giver</th>
                    <th class="py-3 pr-4">Amount</th>
                    <th class="py-3 pr-4">Type</th>
                    <th class="py-3 pr-4">Method</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                    <tr><td colspan="8" class="py-8 text-center text-gray-500">No transactions found.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="py-4 pr-4 text-sm font-mono text-gray-700"><?php echo Helpers::escape($row['reference_no']); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(Helpers::formatDate($row['giving_date'])); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-700"><?php echo Helpers::escape($row['member_name'] ?: ($row['giver_name'] ?: 'Anonymous')); ?></td>
                        <td class="py-4 pr-4 text-sm font-semibold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency((float) $row['amount'])); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(ucfirst($row['giving_type'])); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(ucfirst(str_replace('_', ' ', $row['giving_method']))); ?></td>
                        <td class="py-4 pr-4 text-sm"><span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $statusBadge($row['payment_status']); ?>"><?php echo ucfirst($row['payment_status']); ?></span></td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <?php if (!empty($row['receipt_path'])): ?>
                                <a href="<?php echo BASE_URL . '/' . ltrim($row['receipt_path'], '/'); ?>" target="_blank" class="text-rccg-navy font-semibold mr-3">Receipt</a>
                            <?php endif; ?>
                            <?php if ($row['payment_status'] === 'pending'): ?>
                                <form method="post" action="<?php echo $base; ?>/verify/<?php echo (int) $row['id']; ?>" class="inline">
                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                                    <button type="submit" class="text-rccg-red font-semibold">Verify</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">Page <?php echo (int) $page; ?> of <?php echo (int) $totalPages; ?>, <?php echo (int) $total; ?> total</p>
            <div class="flex gap-2">
                <?php if ($page > 1): ?><a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo $base; ?>?page=<?php echo $page - 1; ?>&<?php echo $qs; ?>">Previous</a><?php endif; ?>
                <?php if ($page < $totalPages): ?><a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo $base; ?>?page=<?php echo $page + 1; ?>&<?php echo $qs; ?>">Next</a><?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
