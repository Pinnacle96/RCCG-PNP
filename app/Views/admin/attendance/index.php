<?php
/** @var array<int, array<string, mixed>> $services */
/** @var string $search */
/** @var int $page */
/** @var int $total */
/** @var int $totalPages */
$services = $services ?? [];
$search = $search ?? '';
$page = isset($page) ? (int) $page : 1;
$total = isset($total) ? (int) $total : count($services);
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
?>
<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Attendance</h1>
            <p class="text-gray-600">Create services and track member attendance.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/attendance/add"
            class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3">
            <i class="fas fa-plus mr-2"></i>Create Service
        </a>
    </div>

    <div class="card">
        <form method="get" action="<?php echo BASE_URL; ?>/admin/attendance" class="flex flex-col gap-3 md:flex-row">
            <input class="form-input" type="search" name="q" value="<?php echo Helpers::escape($search); ?>"
                placeholder="Search by theme, preacher, or service type">
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            <?php if ($search !== ''): ?>
            <a href="<?php echo BASE_URL; ?>/admin/attendance"
                class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Service</th>
                    <th class="py-3 pr-4">Date</th>
                    <th class="py-3 pr-4">Theme</th>
                    <th class="py-3 pr-4">Preacher</th>
                    <th class="py-3 pr-4">Attendance</th>
                    <th class="py-3 pr-4">Closed</th>
                    <th class="py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($services)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-500">No services created yet.</td>
                </tr>
                <?php endif; ?>

                <?php foreach ($services as $service): ?>
                <tr>
                    <td class="py-4 pr-4 font-semibold text-rccg-navy capitalize">
                        <?php echo Helpers::escape(str_replace('_', ' ', $service['service_type'])); ?></td>
                    <td class="py-4 pr-4 text-sm text-gray-600">
                        <?php echo Helpers::escape(Helpers::formatDate($service['service_date'], 'M d, Y')); ?></td>
                    <td class="py-4 pr-4"><?php echo Helpers::escape($service['theme'] ?: 'N/A'); ?></td>
                    <td class="py-4 pr-4"><?php echo Helpers::escape($service['preacher'] ?: 'N/A'); ?></td>
                    <td class="py-4 pr-4 font-semibold"><?php echo (int) $service['marked_count']; ?></td>
                    <td class="py-4 pr-4"><?php echo !empty($service['is_closed']) ? 'Yes' : 'No'; ?></td>
                    <td class="py-4 text-right whitespace-nowrap">
                        <a href="<?php echo BASE_URL; ?>/admin/attendance/view/<?php echo (int) $service['id']; ?>"
                            class="text-rccg-red font-semibold mr-4">Open</a>
                        <a href="<?php echo BASE_URL; ?>/admin/attendance/edit/<?php echo (int) $service['id']; ?>"
                            class="text-rccg-navy font-semibold">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">Page <?php echo (int) $page; ?> of <?php echo (int) $totalPages; ?>,
            <?php echo (int) $total; ?> total</p>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2"
                href="<?php echo BASE_URL; ?>/admin/attendance?page=<?php echo $page - 1; ?>&q=<?php echo urlencode($search); ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2"
                href="<?php echo BASE_URL; ?>/admin/attendance?page=<?php echo $page + 1; ?>&q=<?php echo urlencode($search); ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</section>
