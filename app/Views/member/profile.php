<section class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-rccg-navy">My Profile</h1>
        <p class="text-gray-600">View and update your contact information.</p>
    </div>

    <?php if (!$member): ?>
        <div class="card">
            <h2 class="text-xl font-bold text-rccg-navy mb-2">Profile Not Linked</h2>
            <p class="text-gray-600">This portal account is not linked to a member record yet. An administrator can link it by matching your account email with your member profile email.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card text-center">
                <?php if (!empty($member['profile_photo'])): ?>
                    <img src="<?php echo Helpers::escape(UPLOAD_URL . 'members/' . $member['profile_photo']); ?>" alt="Profile photo" class="w-24 h-24 rounded-full object-cover mx-auto mb-4 border">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-full bg-rccg-navy text-white flex items-center justify-center text-3xl font-bold mb-4 mx-auto">
                        <?php echo Helpers::escape(Helpers::getInitials($member['first_name'], $member['last_name'])); ?>
                    </div>
                <?php endif; ?>
                <p class="text-sm font-bold text-rccg-red"><?php echo Helpers::escape($member['member_code']); ?></p>
                <h2 class="text-xl font-bold text-rccg-navy">
                    <?php echo Helpers::escape($member['first_name'] . ' ' . $member['last_name']); ?>
                </h2>
                <p class="text-gray-600 capitalize"><?php echo Helpers::escape($member['membership_status']); ?> member</p>

                <?php if (!empty($qr)): ?>
                    <div class="mt-5 border-t pt-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Attendance QR Code</p>
                        <img src="<?php echo Helpers::escape($qr); ?>" alt="Member QR code" class="mx-auto h-40 w-40">
                        <p class="mt-2 text-xs text-gray-500">Show this at check-in to mark your attendance.</p>
                    </div>
                <?php endif; ?>
            </div>

            <form method="post" action="<?php echo BASE_URL; ?>/portal/profile" enctype="multipart/form-data" data-validate class="card lg:col-span-2 space-y-5">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

                <div>
                    <label class="form-label" for="profile_photo">Profile Photo</label>
                    <input class="form-input" id="profile_photo" type="file" name="profile_photo" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP, max 5MB.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input class="form-input" id="email" type="email" name="email" value="<?php echo Helpers::escape((string) ($member['email'] ?? '')); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="phone">Phone</label>
                        <input class="form-input" id="phone" name="phone" value="<?php echo Helpers::escape((string) ($member['phone'] ?? '')); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="alt_phone">Alternative Phone</label>
                        <input class="form-input" id="alt_phone" name="alt_phone" value="<?php echo Helpers::escape((string) ($member['alt_phone'] ?? '')); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="occupation">Occupation</label>
                        <input class="form-input" id="occupation" name="occupation" value="<?php echo Helpers::escape((string) ($member['occupation'] ?? '')); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="emergency_contact">Emergency Contact</label>
                        <input class="form-input" id="emergency_contact" name="emergency_contact" value="<?php echo Helpers::escape((string) ($member['emergency_contact'] ?? '')); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="emergency_phone">Emergency Phone</label>
                        <input class="form-input" id="emergency_phone" name="emergency_phone" value="<?php echo Helpers::escape((string) ($member['emergency_phone'] ?? '')); ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-input" id="address" name="address" rows="3"><?php echo Helpers::escape((string) ($member['address'] ?? '')); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end border-t pt-5">
                    <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">Save Profile</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</section>
