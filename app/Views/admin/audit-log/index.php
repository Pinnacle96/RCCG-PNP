<section class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-rccg-navy">Audit Log</h1>
        <p class="text-gray-600">Record of privileged admin actions (most recent 500).</p>
    </div>

    <div class="card">
        <form method="get" action="<?php echo BASE_URL; ?>/admin/audit-log" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="form-label" for="action">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">All actions</option>
                    <?php foreach ($actions as $a): ?>
                        <option value="<?php echo Helpers::escape($a['action']); ?>" <?php echo $filters['action'] === $a['action'] ? 'selected' : ''; ?>>
                            <?php echo Helpers::escape(ucfirst($a['action'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="user_id">User</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">All users</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo (int) $u['value']; ?>" <?php echo (int) $filters['user_id'] === (int) $u['value'] ? 'selected' : ''; ?>>
                            <?php echo Helpers::escape($u['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="from">From</label>
                <input class="form-input" type="date" id="from" name="from" value="<?php echo Helpers::escape($filters['from']); ?>">
            </div>
            <div>
                <label class="form-label" for="to">To</label>
                <input class="form-input" type="date" id="to" name="to" value="<?php echo Helpers::escape($filters['to']); ?>">
            </div>
            <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">Filter</button>
            <a href="<?php echo BASE_URL; ?>/admin/audit-log" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Reset</a>
        </form>
    </div>

    <div class="card overflow-x-auto">
        <table id="auditTable" class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">When</th>
                    <th class="py-3 pr-4">User</th>
                    <th class="py-3 pr-4">Action</th>
                    <th class="py-3 pr-4">Module</th>
                    <th class="py-3 pr-4">Description</th>
                    <th class="py-3">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                    <tr><td colspan="6" class="py-8 text-center text-gray-500">No audit entries found.</td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="py-3 pr-4 text-sm text-gray-600 whitespace-nowrap"><?php echo Helpers::escape(Helpers::formatDate($row['created_at'], 'M d, Y H:i')); ?></td>
                        <td class="py-3 pr-4 text-sm text-gray-700"><?php echo Helpers::escape($row['email'] ?? 'System'); ?></td>
                        <td class="py-3 pr-4">
                            <span class="inline-block rounded bg-gray-100 px-2 py-1 text-xs font-semibold capitalize text-gray-700"><?php echo Helpers::escape($row['action']); ?></span>
                        </td>
                        <td class="py-3 pr-4 text-sm text-gray-600"><?php echo Helpers::escape($row['module'] ?? '—'); ?></td>
                        <td class="py-3 pr-4 text-sm text-gray-700"><?php echo Helpers::escape($row['description'] ?? ''); ?></td>
                        <td class="py-3 text-sm text-gray-500"><?php echo Helpers::escape($row['ip_address'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
        jQuery('#auditTable').DataTable({ order: [], pageLength: 50 });
    }
});
</script>
