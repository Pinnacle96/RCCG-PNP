<?php
$base = BASE_URL . '/admin/prayer';
$statusBadge = function (string $status): string {
    $map = [
        'new' => 'bg-blue-100 text-blue-700',
        'praying' => 'bg-amber-100 text-amber-700',
        'answered' => 'bg-green-100 text-green-700',
        'archived' => 'bg-gray-100 text-gray-600',
    ];
    return $map[$status] ?? 'bg-gray-100 text-gray-600';
};
?>

<section class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-rccg-navy">Prayer Requests</h1>
        <p class="text-gray-600">Review, assign, and respond to prayer requests.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-4">
        <?php foreach (['new' => 'New', 'praying' => 'Praying', 'answered' => 'Answered', 'archived' => 'Archived'] as $key => $label): ?>
            <a href="<?php echo $base; ?>?status=<?php echo $key; ?>" class="card hover:border-rccg-red border border-transparent transition">
                <p class="text-sm text-gray-500"><?php echo $label; ?></p>
                <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) ($counts[$key] ?? 0); ?></p>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <form method="get" action="<?php echo $base; ?>" class="grid gap-3 md:grid-cols-4">
            <input class="form-input" type="search" name="q" value="<?php echo Helpers::escape($filters['q']); ?>" placeholder="Search name, subject, text">
            <select class="form-input" name="status">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $filters['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
            </select>
            <select class="form-input" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c; ?>" <?php echo $filters['category'] === $c ? 'selected' : ''; ?>><?php echo ucfirst($c); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit"><i class="fas fa-filter mr-2"></i>Filter</button>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Requester</th>
                    <th class="py-3 pr-4">Subject</th>
                    <th class="py-3 pr-4">Category</th>
                    <th class="py-3 pr-4">Privacy</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Received</th>
                    <th class="py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($requests)): ?>
                    <tr><td colspan="7" class="py-8 text-center text-gray-500">No prayer requests found.</td></tr>
                <?php endif; ?>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td class="py-4 pr-4 text-sm font-semibold text-gray-900"><?php echo Helpers::escape($row['requester_name']); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-700"><?php echo Helpers::escape($row['subject']); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape(ucfirst($row['category'])); ?></td>
                        <td class="py-4 pr-4 text-sm"><?php echo $row['is_private'] ? '<span class="text-rccg-red">Private</span>' : 'Public'; ?></td>
                        <td class="py-4 pr-4 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $statusBadge($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span>
                        </td>
                        <td class="py-4 pr-4 text-sm text-gray-500"><?php echo Helpers::escape(Helpers::formatDate($row['created_at'])); ?></td>
                        <td class="py-4 text-right">
                            <a href="<?php echo $base; ?>/view/<?php echo (int) $row['id']; ?>" class="text-rccg-navy font-semibold">Manage</a>
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
                <?php $qs = http_build_query(array_filter(['status' => $filters['status'], 'category' => $filters['category'], 'q' => $filters['q']])); ?>
                <?php if ($page > 1): ?>
                    <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo $base; ?>?page=<?php echo $page - 1; ?>&<?php echo $qs; ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo $base; ?>?page=<?php echo $page + 1; ?>&<?php echo $qs; ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
