<?php
$base = BASE_URL . '/portal/giving';
$qs = http_build_query(array_filter([
    'type' => $filters['type'], 'from' => $filters['from'], 'to' => $filters['to'],
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
    <div class="grid gap-4 md:grid-cols-3">
        <div class="card">
            <p class="text-sm text-gray-500">Total Given (<?php echo (int) $currentYear; ?>)</p>
            <p class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($yearTotal)); ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/give" class="card block hover:border-rccg-red transition">
            <p class="text-sm text-gray-500">Make a Donation</p>
            <p class="text-xl font-bold text-rccg-red"><i class="fas fa-hand-holding-heart mr-2"></i>Give Now</p>
        </a>
        <a href="<?php echo $base; ?>/statement?<?php echo $qs; ?>" class="card block hover:border-rccg-red transition">
            <p class="text-sm text-gray-500">Annual Statement</p>
            <p class="text-xl font-bold text-rccg-navy"><i class="fas fa-file-pdf mr-2"></i>Download PDF</p>
        </a>
    </div>

    <div class="card">
        <form method="get" action="<?php echo $base; ?>" class="grid gap-3 md:grid-cols-4">
            <select class="form-input" name="type">
                <option value="">All types</option>
                <?php foreach ($types as $t): ?><option value="<?php echo $t; ?>" <?php echo $filters['type'] === $t ? 'selected' : ''; ?>><?php echo ucfirst($t); ?></option><?php endforeach; ?>
            </select>
            <input class="form-input" type="date" name="from" value="<?php echo Helpers::escape($filters['from']); ?>">
            <input class="form-input" type="date" name="to" value="<?php echo Helpers::escape($filters['to']); ?>">
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit"><i class="fas fa-filter mr-2"></i>Filter</button>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Date</th>
                    <th class="py-3 pr-4">Reference</th>
                    <th class="py-3 pr-4">Type</th>
                    <th class="py-3 pr-4">Method</th>
                    <th class="py-3 pr-4">Amount</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 text-right">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">No giving records yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(Helpers::formatDate($row['giving_date'])); ?></td>
                        <td class="py-4 pr-4 text-sm font-mono text-gray-700"><?php echo Helpers::escape($row['reference_no']); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(ucfirst($row['giving_type'])); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(ucfirst(str_replace('_', ' ', $row['giving_method']))); ?></td>
                        <td class="py-4 pr-4 text-sm font-semibold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency((float) $row['amount'])); ?></td>
                        <td class="py-4 pr-4 text-sm"><span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $statusBadge($row['payment_status']); ?>"><?php echo ucfirst($row['payment_status']); ?></span></td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <?php if (!empty($row['receipt_path'])): ?>
                                <a href="<?php echo BASE_URL . '/' . ltrim($row['receipt_path'], '/'); ?>" target="_blank" class="text-rccg-navy font-semibold">Download</a>
                            <?php else: ?>
                                <span class="text-gray-400">&mdash;</span>
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
