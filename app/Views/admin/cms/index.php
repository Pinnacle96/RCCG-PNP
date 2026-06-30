<?php
$base = BASE_URL . '/admin/' . $module;
$formatValue = function (string $column, mixed $value): string {
    if ($value === null || $value === '') {
        return 'N/A';
    }
    if (str_starts_with($column, 'is_') || in_array($column, ['requires_registration', 'show_on_website'], true)) {
        return $value ? 'Yes' : 'No';
    }
    if ($column === 'amount') {
        return Helpers::currency((float) $value);
    }
    if (str_contains($column, 'date') || str_contains($column, '_at')) {
        return Helpers::escape(Helpers::formatDateTime((string) $value));
    }
    return Helpers::escape((string) $value);
};
?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape($config['title']); ?></h1>
            <p class="text-gray-600">Manage <?php echo Helpers::escape(strtolower($config['title'])); ?>.</p>
        </div>
        <?php if (empty($config['readonly'])): ?>
            <a href="<?php echo $base; ?>/add" class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3">
                <i class="fas fa-plus mr-2"></i>Add <?php echo Helpers::escape($config['singular']); ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <form method="get" action="<?php echo $base; ?>" class="flex flex-col gap-3 md:flex-row">
            <input class="form-input" type="search" name="q" value="<?php echo Helpers::escape($search); ?>" placeholder="Search <?php echo Helpers::escape(strtolower($config['title'])); ?>">
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            <?php if ($search !== ''): ?>
                <a href="<?php echo $base; ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <?php foreach ($config['columns'] as $label): ?>
                        <th class="py-3 pr-4"><?php echo Helpers::escape($label); ?></th>
                    <?php endforeach; ?>
                    <th class="py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?php echo count($config['columns']) + 1; ?>" class="py-8 text-center text-gray-500">No records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($config['columns'] as $column => $label): ?>
                            <td class="py-4 pr-4 text-sm text-gray-700"><?php echo $formatValue($column, $row[$column] ?? null); ?></td>
                        <?php endforeach; ?>
                        <td class="py-4 text-right whitespace-nowrap">
                            <?php if (empty($config['readonly'])): ?>
                                <a href="<?php echo $base; ?>/edit/<?php echo (int) $row['id']; ?>" class="text-rccg-navy font-semibold mr-4">Edit</a>
                                <form method="post" action="<?php echo $base; ?>/delete/<?php echo (int) $row['id']; ?>" class="inline" onsubmit="return confirm('Delete this record?');">
                                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                                    <button type="submit" class="text-rccg-red font-semibold">Delete</button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-400">Read only</span>
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
                <?php if ($page > 1): ?>
                    <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo $base; ?>?page=<?php echo $page - 1; ?>&q=<?php echo urlencode($search); ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo $base; ?>?page=<?php echo $page + 1; ?>&q=<?php echo urlencode($search); ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
