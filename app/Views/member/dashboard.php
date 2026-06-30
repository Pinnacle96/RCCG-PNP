<section class="space-y-6">
    <div class="card">
        <h1 class="text-2xl font-bold text-rccg-navy mb-2">
            Welcome<?php echo $member ? ', ' . Helpers::escape($member['first_name']) : ''; ?>
        </h1>
        <?php if ($member): ?>
            <p class="text-gray-600">Here is a snapshot of your activity at the parish.</p>
        <?php else: ?>
            <p class="text-gray-600">Your portal account is not linked to a member profile yet. Please contact the church office if you already have a member record.</p>
        <?php endif; ?>
    </div>

    <?php if ($member && $stats): ?>
        <div class="grid gap-4 md:grid-cols-3">
            <a href="<?php echo BASE_URL; ?>/portal/giving" class="card block hover:border-rccg-red transition">
                <p class="text-sm text-gray-500">Total Given (<?php echo (int) $currentYear; ?>)</p>
                <p class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency($stats['giving_year'])); ?></p>
            </a>
            <a href="<?php echo BASE_URL; ?>/portal/attendance" class="card block hover:border-rccg-red transition">
                <p class="text-sm text-gray-500">Attendance Rate (<?php echo (int) $currentYear; ?>)</p>
                <p class="text-2xl font-bold text-rccg-navy"><?php echo (int) $stats['attendance']['rate']; ?>%</p>
            </a>
            <a href="<?php echo BASE_URL; ?>/portal/profile" class="card block hover:border-rccg-red transition">
                <p class="text-sm text-gray-500">Profile</p>
                <p class="text-2xl font-bold text-rccg-navy">My Details</p>
            </a>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 md:grid-cols-3">
        <a href="<?php echo BASE_URL; ?>/portal/cellgroup" class="card block hover:border-rccg-red transition">
            <p class="text-sm text-gray-500">Cell Group</p>
            <p class="text-xl font-bold text-rccg-navy"><i class="fas fa-users mr-2 text-rccg-gold"></i>My Fellowship</p>
        </a>
        <a href="<?php echo BASE_URL; ?>/portal/ministry" class="card block hover:border-rccg-red transition">
            <p class="text-sm text-gray-500">Ministry</p>
            <p class="text-xl font-bold text-rccg-navy"><i class="fas fa-handshake mr-2 text-rccg-gold"></i>My Service</p>
        </a>
        <a href="<?php echo BASE_URL; ?>/give" class="card block hover:border-rccg-red transition">
            <p class="text-sm text-gray-500">Giving</p>
            <p class="text-xl font-bold text-rccg-red"><i class="fas fa-hand-holding-heart mr-2"></i>Give Now</p>
        </a>
    </div>
</section>
