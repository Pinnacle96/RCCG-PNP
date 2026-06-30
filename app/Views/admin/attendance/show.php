<?php
$serviceLabel = ucfirst(str_replace('_', ' ', $service['service_type']));
?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape($serviceLabel); ?></h1>
            <p class="text-gray-600">
                <?php echo Helpers::escape(Helpers::formatDate($service['service_date'], 'F d, Y')); ?>
                <?php if (!empty($service['theme'])): ?>
                    · <?php echo Helpers::escape($service['theme']); ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL; ?>/admin/attendance/edit/<?php echo (int) $service['id']; ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Edit Service</a>
            <a href="<?php echo BASE_URL; ?>/admin/attendance" class="btn-primary bg-rccg-navy text-white px-5 py-3">Back</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="card">
            <p class="text-sm text-gray-500">Marked Present</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $service['marked_count']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Manual</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $methodTotals['manual']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">QR</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $methodTotals['qr']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Visitors</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $service['visitors_count']; ?></p>
        </div>
    </div>

    <?php if ((int) $service['is_closed'] !== 1): ?>
    <div class="card space-y-4">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-rccg-navy">QR Scan Check-in</h2>
                <p class="text-gray-600">Scan a member&rsquo;s QR code (from their portal profile) to mark them present.</p>
            </div>
            <div class="flex gap-2">
                <button id="qr-start" type="button" class="btn-primary bg-rccg-red text-white px-5 py-3">Start Scanner</button>
                <button id="qr-stop" type="button" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3 hidden">Stop</button>
            </div>
        </div>
        <div id="qr-area" class="hidden grid gap-4 md:grid-cols-2">
            <div>
                <video id="qr-video" class="w-full rounded bg-black" playsinline></video>
                <canvas id="qr-canvas" class="hidden"></canvas>
            </div>
            <div>
                <p id="qr-status" class="font-semibold text-rccg-navy">Point the camera at a member QR code.</p>
                <ul id="qr-log" class="mt-3 space-y-1 text-sm text-gray-600 max-h-48 overflow-y-auto"></ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card space-y-4">
            <div>
                <h2 class="text-xl font-bold text-rccg-navy">Mark Attendance</h2>
                <p class="text-gray-600">Search active members who have not already been marked present.</p>
            </div>

            <form method="get" action="<?php echo BASE_URL; ?>/admin/attendance/view/<?php echo (int) $service['id']; ?>" class="flex gap-3">
                <input class="form-input" type="search" name="q" value="<?php echo Helpers::escape($search); ?>" placeholder="Search name, code, email, phone">
                <button class="btn-primary bg-rccg-navy text-white px-5 py-3" type="submit">Search</button>
            </form>

            <div class="space-y-3 max-h-[520px] overflow-y-auto">
                <?php if (empty($members)): ?>
                    <p class="text-gray-500">No available members found.</p>
                <?php endif; ?>

                <?php foreach ($members as $member): ?>
                    <form method="post" action="<?php echo BASE_URL; ?>/admin/attendance/mark/<?php echo (int) $service['id']; ?>" class="flex items-center justify-between gap-3 border border-gray-100 rounded p-3">
                        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                        <input type="hidden" name="member_id" value="<?php echo (int) $member['id']; ?>">
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo Helpers::escape($member['first_name'] . ' ' . $member['last_name']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo Helpers::escape($member['member_code']); ?> · <?php echo Helpers::escape($member['phone'] ?: 'No phone'); ?></p>
                        </div>
                        <button class="btn-primary bg-rccg-red text-white px-4 py-2" type="submit">Mark</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card space-y-4">
            <div>
                <h2 class="text-xl font-bold text-rccg-navy">Present Members</h2>
                <p class="text-gray-600">Members already checked into this service.</p>
            </div>

            <div class="space-y-3 max-h-[620px] overflow-y-auto">
                <?php if (empty($present)): ?>
                    <p class="text-gray-500">No members marked present yet.</p>
                <?php endif; ?>

                <?php foreach ($present as $row): ?>
                    <form method="post" action="<?php echo BASE_URL; ?>/admin/attendance/remove/<?php echo (int) $service['id']; ?>" class="flex items-center justify-between gap-3 border border-gray-100 rounded p-3">
                        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                        <input type="hidden" name="member_id" value="<?php echo (int) $row['member_id']; ?>">
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo Helpers::escape($row['first_name'] . ' ' . $row['last_name']); ?></p>
                            <p class="text-sm text-gray-500">
                                <?php echo Helpers::escape($row['member_code']); ?> ·
                                <?php echo Helpers::escape(Helpers::formatDateTime($row['check_in_time'])); ?> ·
                                <?php echo Helpers::escape($row['method']); ?>
                            </p>
                        </div>
                        <button class="text-rccg-red font-semibold" type="submit">Remove</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php if ((int) $service['is_closed'] !== 1): ?>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startBtn = document.getElementById('qr-start');
    const stopBtn = document.getElementById('qr-stop');
    const area = document.getElementById('qr-area');
    const video = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const statusEl = document.getElementById('qr-status');
    const logEl = document.getElementById('qr-log');
    const endpoint = '<?php echo BASE_URL; ?>/api/admin/attendance/qr';
    const serviceId = <?php echo (int) $service['id']; ?>;
    const csrf = '<?php echo Helpers::escape($csrf); ?>';
    let stream = null, scanning = false, lastCode = '', lastTime = 0;

    async function start() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            await video.play();
            area.classList.remove('hidden');
            stopBtn.classList.remove('hidden');
            startBtn.classList.add('hidden');
            scanning = true;
            requestAnimationFrame(tick);
        } catch (e) {
            statusEl.textContent = 'Unable to access the camera: ' + e.message;
            area.classList.remove('hidden');
        }
    }

    function stop() {
        scanning = false;
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
        area.classList.add('hidden');
        stopBtn.classList.add('hidden');
        startBtn.classList.remove('hidden');
    }

    function tick() {
        if (!scanning) return;
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = window.jsQR ? jsQR(img.data, img.width, img.height) : null;
            if (code && code.data) {
                const now = Date.now();
                if (code.data !== lastCode || now - lastTime > 3000) {
                    lastCode = code.data; lastTime = now;
                    checkIn(code.data.trim());
                }
            }
        }
        requestAnimationFrame(tick);
    }

    function checkIn(codeText) {
        statusEl.textContent = 'Checking in ' + codeText + '…';
        const fd = new FormData();
        fd.append('_csrf_token', csrf);
        fd.append('service_id', serviceId);
        fd.append('code', codeText);
        fetch(endpoint, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(res => {
                statusEl.textContent = res.message || (res.success ? 'Marked present.' : 'Failed.');
                const li = document.createElement('li');
                li.textContent = (res.success ? '✓ ' : '✗ ') + (res.message || codeText);
                li.className = res.success ? 'text-green-700' : 'text-rccg-red';
                logEl.prepend(li);
            })
            .catch(() => { statusEl.textContent = 'Network error during check-in.'; });
    }

    startBtn.addEventListener('click', start);
    stopBtn.addEventListener('click', stop);
    window.addEventListener('beforeunload', stop);
});
</script>
<?php endif; ?>
