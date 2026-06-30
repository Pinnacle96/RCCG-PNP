<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Users &amp; Roles</h1>
            <p class="text-gray-600">Manage login accounts and access levels.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/users/add" class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3">
            <i class="fas fa-user-plus mr-2"></i>Add User
        </a>
    </div>

    <div class="card overflow-x-auto">
        <table id="usersTable" class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Member</th>
                    <th class="py-3 pr-4">Role</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Last Login</th>
                    <th class="py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6" class="py-8 text-center text-gray-500">No users found.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="py-4 pr-4 font-semibold text-rccg-navy"><?php echo Helpers::escape($row['email']); ?></td>
                        <td class="py-4 pr-4 text-sm text-gray-600"><?php echo Helpers::escape($row['member_name'] ?? '—'); ?></td>
                        <td class="py-4 pr-4">
                            <span class="inline-block rounded bg-gray-100 px-2 py-1 text-xs font-semibold capitalize text-gray-700"><?php echo Helpers::escape(str_replace('_', ' ', $row['role'])); ?></span>
                        </td>
                        <td class="py-4 pr-4">
                            <span class="badge-<?php echo (int) $row['is_active'] === 1 ? 'published' : 'archived'; ?>">
                                <?php echo (int) $row['is_active'] === 1 ? 'Active' : 'Disabled'; ?>
                            </span>
                        </td>
                        <td class="py-4 pr-4 text-sm text-gray-600">
                            <?php echo $row['last_login'] ? Helpers::escape(Helpers::formatDate($row['last_login'], 'M d, Y H:i')) : 'Never'; ?>
                        </td>
                        <td class="py-4 text-right whitespace-nowrap">
                            <a href="<?php echo BASE_URL; ?>/admin/users/edit/<?php echo (int) $row['id']; ?>" class="text-rccg-navy font-semibold mr-4">Edit</a>
                            <form method="post" action="<?php echo BASE_URL; ?>/admin/users/delete/<?php echo (int) $row['id']; ?>" class="inline" onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                                <button type="submit" class="text-rccg-red font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
        jQuery('#usersTable').DataTable({ order: [], pageLength: 25, columnDefs: [{ orderable: false, targets: -1 }] });
    }
});
</script>
