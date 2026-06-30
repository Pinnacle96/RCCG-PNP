<section class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-rccg-navy">Communications</h1>
        <p class="text-gray-600">Send email broadcasts to members and review the delivery log.</p>
    </div>

    <div class="flex flex-wrap gap-2 border-b border-gray-200">
        <button type="button" class="comms-tab px-4 py-2 text-sm font-semibold border-b-2 -mb-px border-rccg-red text-rccg-red" data-tab="email">
            <i class="fas fa-envelope mr-2"></i>Email
        </button>
        <button type="button" class="comms-tab px-4 py-2 text-sm font-semibold border-b-2 -mb-px border-transparent text-gray-500 hover:text-rccg-navy" data-tab="sms">
            <i class="fas fa-comment-sms mr-2"></i>SMS
        </button>
        <button type="button" class="comms-tab px-4 py-2 text-sm font-semibold border-b-2 -mb-px border-transparent text-gray-500 hover:text-rccg-navy" data-tab="logs">
            <i class="fas fa-list mr-2"></i>Logs
        </button>
    </div>

    <!-- Email tab -->
    <div class="comms-panel card" data-panel="email">
        <form method="post" action="<?php echo BASE_URL; ?>/admin/communications/send" class="space-y-5">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="recipient_group">Recipients</label>
                    <select class="form-select" id="recipient_group" name="recipient_group">
                        <option value="all">All active members</option>
                        <option value="ministry">By ministry</option>
                        <option value="cellgroup">By cell group</option>
                    </select>
                </div>
                <div id="ministry-wrap" class="hidden">
                    <label class="form-label" for="ministry_id">Ministry</label>
                    <select class="form-select" id="ministry_id" name="ministry_id">
                        <?php foreach ($ministries as $m): ?>
                            <option value="<?php echo (int) $m['value']; ?>"><?php echo Helpers::escape($m['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="cellgroup-wrap" class="hidden">
                    <label class="form-label" for="cell_group_id">Cell Group</label>
                    <select class="form-select" id="cell_group_id" name="cell_group_id">
                        <?php foreach ($cellGroups as $c): ?>
                            <option value="<?php echo (int) $c['value']; ?>"><?php echo Helpers::escape($c['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label" for="subject">Subject</label>
                <input class="form-input" id="subject" type="text" name="subject" required>
            </div>

            <div>
                <label class="form-label" for="body">Message</label>
                <textarea class="form-input tinymce" id="body" name="body" rows="10"></textarea>
            </div>

            <div class="flex justify-end">
                <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit" onclick="return confirm('Send this email to the selected recipients?');">
                    <i class="fas fa-paper-plane mr-2"></i>Send Email
                </button>
            </div>
        </form>
    </div>

    <!-- SMS tab -->
    <div class="comms-panel card hidden" data-panel="sms">
        <form method="post" action="<?php echo BASE_URL; ?>/admin/communications/sms" class="space-y-5">
            <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="sms_recipient_group">Recipients</label>
                    <select class="form-select" id="sms_recipient_group" name="recipient_group">
                        <option value="all">All active members</option>
                        <option value="ministry">By ministry</option>
                        <option value="cellgroup">By cell group</option>
                    </select>
                </div>
                <div id="sms-ministry-wrap" class="hidden">
                    <label class="form-label" for="sms_ministry_id">Ministry</label>
                    <select class="form-select" id="sms_ministry_id" name="ministry_id">
                        <?php foreach ($ministries as $m): ?>
                            <option value="<?php echo (int) $m['value']; ?>"><?php echo Helpers::escape($m['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="sms-cellgroup-wrap" class="hidden">
                    <label class="form-label" for="sms_cell_group_id">Cell Group</label>
                    <select class="form-select" id="sms_cell_group_id" name="cell_group_id">
                        <?php foreach ($cellGroups as $c): ?>
                            <option value="<?php echo (int) $c['value']; ?>"><?php echo Helpers::escape($c['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label" for="sms_message">Message</label>
                <textarea class="form-input" id="sms_message" name="message" rows="4" maxlength="480" required></textarea>
                <p class="mt-1 text-xs text-gray-500"><span id="sms-count">0</span> characters (<span id="sms-parts">1</span> SMS part(s))</p>
            </div>

            <div class="flex justify-end">
                <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit" onclick="return confirm('Send this SMS to the selected recipients?');">
                    <i class="fas fa-paper-plane mr-2"></i>Send SMS
                </button>
            </div>
        </form>
    </div>

    <!-- Logs tab -->
    <div class="comms-panel card overflow-x-auto hidden" data-panel="logs">
        <table id="logsTable" class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-gray-500">
                    <th class="py-3 pr-4">Type</th>
                    <th class="py-3 pr-4">Recipient</th>
                    <th class="py-3 pr-4">Subject</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3">Sent</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">No messages sent yet.</td></tr>
                <?php endif; ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="py-3 pr-4 capitalize text-sm text-gray-600"><?php echo Helpers::escape($log['type']); ?></td>
                        <td class="py-3 pr-4 text-sm text-gray-700"><?php echo Helpers::escape($log['recipient']); ?></td>
                        <td class="py-3 pr-4 text-sm text-gray-700"><?php echo Helpers::escape($log['subject'] ?? ''); ?></td>
                        <td class="py-3 pr-4">
                            <span class="badge-<?php echo $log['status'] === 'sent' ? 'published' : 'archived'; ?>"><?php echo Helpers::escape($log['status']); ?></span>
                        </td>
                        <td class="py-3 text-sm text-gray-600"><?php echo Helpers::escape(Helpers::formatDate($log['sent_at'], 'M d, Y H:i')); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tab switching
    const tabs = document.querySelectorAll('.comms-tab');
    const panels = document.querySelectorAll('.comms-panel');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const target = tab.getAttribute('data-tab');
            tabs.forEach(function (t) {
                t.classList.remove('border-rccg-red', 'text-rccg-red');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            tab.classList.add('border-rccg-red', 'text-rccg-red');
            tab.classList.remove('border-transparent', 'text-gray-500');
            panels.forEach(function (p) {
                p.classList.toggle('hidden', p.getAttribute('data-panel') !== target);
            });
        });
    });

    // Recipient group conditional dropdowns (reused for email + sms)
    function bindGroup(selectId, minId, cellId) {
        const group = document.getElementById(selectId);
        const minWrap = document.getElementById(minId);
        const cellWrap = document.getElementById(cellId);
        if (!group) { return; }
        function sync() {
            minWrap.classList.toggle('hidden', group.value !== 'ministry');
            cellWrap.classList.toggle('hidden', group.value !== 'cellgroup');
        }
        group.addEventListener('change', sync);
        sync();
    }
    bindGroup('recipient_group', 'ministry-wrap', 'cellgroup-wrap');
    bindGroup('sms_recipient_group', 'sms-ministry-wrap', 'sms-cellgroup-wrap');

    // SMS character / part counter
    const smsMsg = document.getElementById('sms_message');
    if (smsMsg) {
        const count = document.getElementById('sms-count');
        const parts = document.getElementById('sms-parts');
        smsMsg.addEventListener('input', function () {
            const len = smsMsg.value.length;
            count.textContent = len;
            parts.textContent = Math.max(1, Math.ceil(len / 160));
        });
    }

    // TinyMCE rich text editor
    if (window.tinymce) {
        tinymce.init({
            selector: 'textarea.tinymce',
            menubar: false,
            plugins: 'lists link autolink',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
            height: 320
        });
    }

    // DataTables on logs
    if (window.jQuery && jQuery.fn && jQuery.fn.DataTable) {
        jQuery('#logsTable').DataTable({ order: [[4, 'desc']], pageLength: 25 });
    }
});
</script>
