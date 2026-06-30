<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Members</h1>
            <p class="text-gray-600">Manage parish member records.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo BASE_URL; ?>/admin/members/export?q=<?php echo urlencode($search); ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3"><i class="fas fa-file-csv mr-2"></i>Export CSV</a>
            <a href="<?php echo BASE_URL; ?>/admin/members/export?format=xlsx&q=<?php echo urlencode($search); ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3"><i class="fas fa-file-excel mr-2"></i>Export Excel</a>
            <a href="<?php echo BASE_URL; ?>/admin/members/add" class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3">
                <i class="fas fa-user-plus mr-2"></i>Add Member
            </a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <?php foreach ($statusCounts as $status => $count): ?>
            <div class="card">
                <p class="text-sm text-gray-500 capitalize"><?php echo Helpers::escape(str_replace('_', ' ', $status)); ?></p>
                <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $count; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <form method="get" action="<?php echo BASE_URL; ?>/admin/members" class="flex flex-col gap-3 md:flex-row">
            <input class="form-input" type="search" name="q" value="<?php echo Helpers::escape($search); ?>" placeholder="Search by name, code, email, or phone">
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            <?php if ($search !== ''): ?>
                <a href="<?php echo BASE_URL; ?>/admin/members" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Code</th>
                    <th class="py-3 pr-4">Name</th>
                    <th class="py-3 pr-4">Contact</th>
                    <th class="py-3 pr-4">Type</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Joined</th>
                    <th class="py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($members)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500">No members found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($members as $member): ?>
                    <tr>
                        <td class="py-4 pr-4 font-semibold text-rccg-navy"><?php echo Helpers::escape($member['member_code']); ?></td>
                        <td class="py-4 pr-4">
                            <p class="font-semibold text-gray-900">
                                <?php echo Helpers::escape($member['first_name'] . ' ' . $member['last_name']); ?>
                            </p>
                            <p class="text-sm text-gray-500"><?php echo Helpers::escape($member['gender']); ?></p>
                        </td>
                        <td class="py-4 pr-4 text-sm text-gray-600">
                            <p><?php echo Helpers::escape($member['email'] ?? 'No email'); ?></p>
                            <p><?php echo Helpers::escape($member['phone'] ?? 'No phone'); ?></p>
                        </td>
                        <td class="py-4 pr-4 capitalize"><?php echo Helpers::escape($member['membership_type']); ?></td>
                        <td class="py-4 pr-4">
                            <span class="badge-<?php echo $member['membership_status'] === 'active' ? 'published' : 'archived'; ?>">
                                <?php echo Helpers::escape($member['membership_status']); ?>
                            </span>
                        </td>
                        <td class="py-4 pr-4 text-sm text-gray-600">
                            <?php echo $member['join_date'] ? Helpers::escape(Helpers::formatDate($member['join_date'], 'M d, Y')) : 'N/A'; ?>
                        </td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <a href="<?php echo BASE_URL; ?>/admin/members/view/<?php echo (int) $member['id']; ?>" class="text-rccg-red font-semibold mr-4">View</a>
                            <a href="<?php echo BASE_URL; ?>/admin/members/edit/<?php echo (int) $member['id']; ?>" class="text-rccg-navy font-semibold">Edit</a>
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
                    <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo BASE_URL; ?>/admin/members?page=<?php echo $page - 1; ?>&q=<?php echo urlencode($search); ?>">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a class="btn-outline border-2 border-gray-300 text-gray-700 px-4 py-2" href="<?php echo BASE_URL; ?>/admin/members?page=<?php echo $page + 1; ?>&q=<?php echo urlencode($search); ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
