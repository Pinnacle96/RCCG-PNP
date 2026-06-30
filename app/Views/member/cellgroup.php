<section class="space-y-6">
    <?php if (!$group): ?>
        <div class="card">
            <h1 class="text-2xl font-bold text-rccg-navy mb-2">My Cell Group</h1>
            <p class="text-gray-600">You are not assigned to a cell group yet. Please contact the church office to be connected with a fellowship near you.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <h1 class="text-2xl font-bold text-rccg-navy mb-1"><?php echo Helpers::escape($group['name']); ?></h1>
            <?php if (!empty($group['zone'])): ?>
                <p class="text-sm text-rccg-gold font-semibold"><?php echo Helpers::escape($group['zone']); ?> Zone</p>
            <?php endif; ?>
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <div>
                    <p class="text-sm text-gray-500">Leader</p>
                    <p class="font-semibold text-rccg-navy"><?php echo Helpers::escape($leaders['leader'] ?? 'To be announced'); ?></p>
                </div>
                <?php if (!empty($leaders['co_leader'])): ?>
                <div>
                    <p class="text-sm text-gray-500">Co-Leader</p>
                    <p class="font-semibold text-rccg-navy"><?php echo Helpers::escape($leaders['co_leader']); ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-sm text-gray-500">Meeting Day</p>
                    <p class="font-semibold text-rccg-navy"><?php echo $group['meeting_day'] ? Helpers::escape(ucfirst($group['meeting_day'])) : '—'; ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Meeting Time</p>
                    <p class="font-semibold text-rccg-navy"><?php echo $group['meeting_time'] ? Helpers::escape(date('g:i A', strtotime($group['meeting_time']))) : '—'; ?></p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Venue</p>
                    <p class="font-semibold text-rccg-navy"><?php echo Helpers::escape($group['meeting_venue'] ?: '—'); ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Fellow Members (<?php echo count($fellows); ?>)</h2>
            <?php if (empty($fellows)): ?>
                <p class="text-gray-500">You are currently the only member listed in this group.</p>
            <?php else: ?>
                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($fellows as $f): ?>
                        <div class="flex items-center gap-3 rounded-lg border border-gray-100 p-3">
                            <img class="h-10 w-10 rounded-full object-cover"
                                 src="<?php echo !empty($f['profile_photo']) ? Helpers::escape(UPLOAD_URL . 'members/' . $f['profile_photo']) : Helpers::escape(Helpers::avatarUrl(Helpers::getInitials($f['first_name'], $f['last_name']))); ?>"
                                 alt="">
                            <div>
                                <p class="font-semibold text-rccg-navy text-sm"><?php echo Helpers::escape($f['first_name'] . ' ' . $f['last_name']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo Helpers::escape($f['member_code']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
