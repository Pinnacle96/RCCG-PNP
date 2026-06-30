<section class="space-y-6">
    <div class="card">
        <h1 class="text-2xl font-bold text-rccg-navy mb-1">My Ministry</h1>
        <p class="text-gray-600">Ministries and units you currently serve in.</p>
    </div>

    <?php if (empty($ministries)): ?>
        <div class="card">
            <p class="text-gray-500">You are not enrolled in any ministry yet. Explore our
                <a href="<?php echo BASE_URL; ?>/ministries" class="text-rccg-red font-semibold">ministries</a>
                and reach out to get involved.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-4 md:grid-cols-2">
            <?php foreach ($ministries as $m): ?>
                <div class="card">
                    <div class="flex items-start justify-between">
                        <h2 class="text-lg font-bold text-rccg-navy"><?php echo Helpers::escape($m['name']); ?></h2>
                        <?php if (!empty($m['member_role'])): ?>
                            <span class="rounded-full bg-rccg-gold/20 px-3 py-1 text-xs font-semibold text-rccg-navy"><?php echo Helpers::escape($m['member_role']); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($m['short_desc'])): ?>
                        <p class="text-sm text-gray-600 mt-2"><?php echo Helpers::escape($m['short_desc']); ?></p>
                    <?php endif; ?>
                    <div class="mt-3 space-y-1 text-sm text-gray-600">
                        <?php if (!empty($m['meeting_schedule'])): ?>
                            <p><i class="fas fa-clock mr-2 text-rccg-gold"></i><?php echo Helpers::escape($m['meeting_schedule']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($m['meeting_venue'])): ?>
                            <p><i class="fas fa-map-marker-alt mr-2 text-rccg-gold"></i><?php echo Helpers::escape($m['meeting_venue']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($m['joined_date'])): ?>
                            <p><i class="fas fa-calendar-check mr-2 text-rccg-gold"></i>Joined <?php echo Helpers::escape(Helpers::formatDate($m['joined_date'])); ?></p>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo BASE_URL . '/ministries/' . Helpers::escape($m['slug']); ?>" class="inline-block mt-4 text-rccg-red font-semibold text-sm">View ministry &rarr;</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
