<?php $base = BASE_URL . '/admin/giving'; ?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy">Record Giving</h1>
            <p class="text-gray-600">Manually record a cash, POS, transfer, or cheque gift.</p>
        </div>
        <a href="<?php echo $base; ?>" class="btn-primary bg-rccg-navy text-white px-5 py-3">Back</a>
    </div>

    <form method="post" action="<?php echo $base; ?>/record" class="card grid gap-5 md:grid-cols-2" data-validate>
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-gray-700">Member (optional)</label>
            <input type="hidden" name="member_id" id="member_id" value="0">
            <input class="form-input" type="search" id="member_search" autocomplete="off" placeholder="Search a member to link this gift, or leave blank">
            <div id="member_results" class="mt-1 hidden rounded-lg border border-gray-200 bg-white shadow-sm"></div>
            <p id="member_selected" class="mt-1 hidden text-sm text-green-700"></p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Amount (<?php echo CURRENCY; ?>) *</label>
            <input class="form-input" type="number" step="0.01" min="0" name="amount" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Giving Date *</label>
            <input class="form-input" type="date" name="giving_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Giving Type *</label>
            <select class="form-input" name="giving_type" required>
                <?php foreach ($types as $t): ?><option value="<?php echo $t; ?>"><?php echo ucfirst($t); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Method *</label>
            <select class="form-input" name="giving_method" required>
                <?php foreach ($methods as $m): ?><option value="<?php echo $m; ?>"><?php echo ucfirst(str_replace('_', ' ', $m)); ?></option><?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Giver Name</label>
            <input class="form-input" type="text" name="giver_name" placeholder="For non-members / anonymous">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Giver Email</label>
            <input class="form-input" type="email" name="giver_email">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Giver Phone</label>
            <input class="form-input" type="text" name="giver_phone">
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
            <textarea class="form-input" name="description" rows="2"></textarea>
        </div>

        <div class="md:col-span-2">
            <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">Save Giving</button>
        </div>
    </form>
</section>

<script>
(function () {
    const input = document.getElementById('member_search');
    const results = document.getElementById('member_results');
    const hidden = document.getElementById('member_id');
    const selected = document.getElementById('member_selected');
    let timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { results.classList.add('hidden'); return; }
        timer = setTimeout(async () => {
            try {
                const res = await window.fetchJSON('<?php echo BASE_URL; ?>/api/admin/members/search?q=' + encodeURIComponent(q));
                const json = await res.json();
                const list = (json.data && json.data.results) || [];
                results.innerHTML = list.length
                    ? list.map(m => `<button type="button" class="block w-full text-left px-4 py-2 hover:bg-gray-100" data-id="${m.id}" data-name="${m.first_name} ${m.last_name} (${m.member_code})">${m.first_name} ${m.last_name} · <span class="text-gray-500">${m.member_code}</span></button>`).join('')
                    : '<p class="px-4 py-2 text-sm text-gray-500">No members found.</p>';
                results.classList.remove('hidden');
            } catch (e) { results.classList.add('hidden'); }
        }, 300);
    });

    results.addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-id]');
        if (!btn) return;
        hidden.value = btn.dataset.id;
        selected.textContent = 'Linked to: ' + btn.dataset.name;
        selected.classList.remove('hidden');
        input.value = btn.dataset.name;
        results.classList.add('hidden');
    });
})();
</script>
