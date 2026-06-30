<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-bold text-rccg-red"><?php echo Helpers::escape($member['member_code']); ?></p>
            <h1 class="text-2xl font-bold text-rccg-navy">
                <?php echo Helpers::escape($member['first_name'] . ' ' . $member['last_name']); ?>
            </h1>
            <p class="text-gray-600 capitalize"><?php echo Helpers::escape($member['membership_type']); ?> member</p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo BASE_URL; ?>/admin/members" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Back</a>
            <a href="<?php echo BASE_URL; ?>/admin/members/edit/<?php echo (int) $member['id']; ?>" class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3">Edit</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-1">
            <?php if (!empty($member['profile_photo'])): ?>
                <img src="<?php echo Helpers::escape(UPLOAD_URL . 'members/' . $member['profile_photo']); ?>" alt="Profile photo" class="w-24 h-24 rounded-full object-cover mb-4 border">
            <?php else: ?>
                <div class="w-24 h-24 rounded-full bg-rccg-navy text-white flex items-center justify-center text-3xl font-bold mb-4">
                    <?php echo Helpers::escape(Helpers::getInitials($member['first_name'], $member['last_name'])); ?>
                </div>
            <?php endif; ?>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="font-bold text-gray-500">Status</dt>
                    <dd class="capitalize text-gray-900"><?php echo Helpers::escape($member['membership_status']); ?></dd>
                </div>
                <div>
                    <dt class="font-bold text-gray-500">Gender</dt>
                    <dd class="capitalize text-gray-900"><?php echo Helpers::escape($member['gender']); ?></dd>
                </div>
                <div>
                    <dt class="font-bold text-gray-500">Joined</dt>
                    <dd><?php echo $member['join_date'] ? Helpers::escape(Helpers::formatDate($member['join_date'])) : 'N/A'; ?></dd>
                </div>
            </dl>

            <?php if (!empty($qr)): ?>
                <div class="mt-5 border-t pt-5 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Attendance QR Code</p>
                    <img src="<?php echo Helpers::escape($qr); ?>" alt="Member QR code" class="mx-auto h-40 w-40">
                </div>
            <?php endif; ?>
        </div>

        <div class="card lg:col-span-2">
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Member Details</h2>
            <dl class="grid gap-4 md:grid-cols-2">
                <?php
                $details = [
                    'Email' => $member['email'] ?? null,
                    'Phone' => $member['phone'] ?? null,
                    'Alternative Phone' => $member['alt_phone'] ?? null,
                    'Date of Birth' => $member['date_of_birth'] ?? null,
                    'Occupation' => $member['occupation'] ?? null,
                    'State of Origin' => $member['state_of_origin'] ?? null,
                    'Marital Status' => $member['marital_status'] ?? null,
                    'Spouse Name' => $member['spouse_name'] ?? null,
                    'Emergency Contact' => $member['emergency_contact'] ?? null,
                    'Emergency Phone' => $member['emergency_phone'] ?? null,
                ];
                ?>
                <?php foreach ($details as $label => $detail): ?>
                    <div>
                        <dt class="text-sm font-bold text-gray-500"><?php echo Helpers::escape($label); ?></dt>
                        <dd class="text-gray-900"><?php echo Helpers::escape((string) ($detail ?: 'N/A')); ?></dd>
                    </div>
                <?php endforeach; ?>
                <div class="md:col-span-2">
                    <dt class="text-sm font-bold text-gray-500">Address</dt>
                    <dd class="text-gray-900"><?php echo Helpers::escape((string) ($member['address'] ?: 'N/A')); ?></dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-sm font-bold text-gray-500">Notes</dt>
                    <dd class="text-gray-900 whitespace-pre-line"><?php echo Helpers::escape((string) ($member['notes'] ?: 'N/A')); ?></dd>
                </div>
            </dl>
        </div>
    </div>
</section>
