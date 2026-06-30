<?php
$isEdit = $mode === 'edit';
$value = function (string $key, string $default = '') use ($member): string {
    return Helpers::escape((string) ($member[$key] ?? $default));
};
$selected = function (string $key, string $option, string $default = '') use ($member): string {
    return (($member[$key] ?? $default) === $option) ? 'selected' : '';
};
$checked = function (string $key) use ($member): string {
    return !empty($member[$key]) ? 'checked' : '';
};
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo $isEdit ? 'Edit Member' : 'Add Member'; ?></h1>
            <p class="text-gray-600"><?php echo $isEdit ? 'Update member information.' : 'Create a new member record.'; ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/members" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Back</a>
    </div>

    <form method="post" action="<?php echo Helpers::escape($action); ?>" enctype="multipart/form-data" data-validate class="card space-y-8">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Profile Photo</h2>
            <div class="flex items-center gap-4">
                <?php if (!empty($member['profile_photo'])): ?>
                    <img src="<?php echo Helpers::escape(UPLOAD_URL . 'members/' . $member['profile_photo']); ?>" alt="Current photo" class="h-20 w-20 rounded-full object-cover border">
                <?php else: ?>
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-gray-400"><i class="fas fa-user text-2xl"></i></div>
                <?php endif; ?>
                <div>
                    <input class="form-input" type="file" name="profile_photo" accept="image/*">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP, max 5MB.<?php echo !empty($member['profile_photo']) ? ' Upload a new photo to replace the current one.' : ''; ?></p>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Personal Information</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="first_name">First Name</label>
                    <input class="form-input" id="first_name" name="first_name" required value="<?php echo $value('first_name'); ?>">
                </div>
                <div>
                    <label class="form-label" for="middle_name">Middle Name</label>
                    <input class="form-input" id="middle_name" name="middle_name" value="<?php echo $value('middle_name'); ?>">
                </div>
                <div>
                    <label class="form-label" for="last_name">Last Name</label>
                    <input class="form-input" id="last_name" name="last_name" required value="<?php echo $value('last_name'); ?>">
                </div>
                <div>
                    <label class="form-label" for="gender">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="">Select gender</option>
                        <option value="male" <?php echo $selected('gender', 'male'); ?>>Male</option>
                        <option value="female" <?php echo $selected('gender', 'female'); ?>>Female</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                    <input class="form-input" id="date_of_birth" type="date" name="date_of_birth" value="<?php echo $value('date_of_birth'); ?>">
                </div>
                <div>
                    <label class="form-label" for="occupation">Occupation</label>
                    <input class="form-input" id="occupation" name="occupation" value="<?php echo $value('occupation'); ?>">
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Contact</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" id="email" type="email" name="email" value="<?php echo $value('email'); ?>">
                </div>
                <div>
                    <label class="form-label" for="phone">Phone</label>
                    <input class="form-input" id="phone" name="phone" value="<?php echo $value('phone'); ?>">
                </div>
                <div>
                    <label class="form-label" for="alt_phone">Alternative Phone</label>
                    <input class="form-input" id="alt_phone" name="alt_phone" value="<?php echo $value('alt_phone'); ?>">
                </div>
                <div>
                    <label class="form-label" for="state_of_origin">State of Origin</label>
                    <input class="form-input" id="state_of_origin" name="state_of_origin" value="<?php echo $value('state_of_origin'); ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label" for="address">Address</label>
                    <textarea class="form-input" id="address" name="address" rows="3"><?php echo $value('address'); ?></textarea>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Membership</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="membership_type">Membership Type</label>
                    <select class="form-select" id="membership_type" name="membership_type">
                        <option value="full" <?php echo $selected('membership_type', 'full', 'full'); ?>>Full</option>
                        <option value="associate" <?php echo $selected('membership_type', 'associate'); ?>>Associate</option>
                        <option value="worker" <?php echo $selected('membership_type', 'worker'); ?>>Worker</option>
                        <option value="junior" <?php echo $selected('membership_type', 'junior'); ?>>Junior</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="membership_status">Status</label>
                    <select class="form-select" id="membership_status" name="membership_status">
                        <option value="active" <?php echo $selected('membership_status', 'active', 'active'); ?>>Active</option>
                        <option value="inactive" <?php echo $selected('membership_status', 'inactive'); ?>>Inactive</option>
                        <option value="transferred" <?php echo $selected('membership_status', 'transferred'); ?>>Transferred</option>
                        <option value="deceased" <?php echo $selected('membership_status', 'deceased'); ?>>Deceased</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="join_date">Join Date</label>
                    <input class="form-input" id="join_date" type="date" name="join_date" value="<?php echo $value('join_date', date('Y-m-d')); ?>">
                </div>
                <div>
                    <label class="form-label" for="baptism_date">Baptism Date</label>
                    <input class="form-input" id="baptism_date" type="date" name="baptism_date" value="<?php echo $value('baptism_date'); ?>">
                </div>
                <label class="flex items-center gap-3 pt-8">
                    <input class="form-checkbox" type="checkbox" name="water_baptized" value="1" <?php echo $checked('water_baptized'); ?>>
                    <span class="font-semibold text-gray-700">Water Baptized</span>
                </label>
                <label class="flex items-center gap-3 pt-8">
                    <input class="form-checkbox" type="checkbox" name="holy_ghost_baptized" value="1" <?php echo $checked('holy_ghost_baptized'); ?>>
                    <span class="font-semibold text-gray-700">Holy Ghost Baptized</span>
                </label>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Family & Emergency</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="marital_status">Marital Status</label>
                    <select class="form-select" id="marital_status" name="marital_status">
                        <option value="">Select status</option>
                        <option value="single" <?php echo $selected('marital_status', 'single'); ?>>Single</option>
                        <option value="married" <?php echo $selected('marital_status', 'married'); ?>>Married</option>
                        <option value="divorced" <?php echo $selected('marital_status', 'divorced'); ?>>Divorced</option>
                        <option value="widowed" <?php echo $selected('marital_status', 'widowed'); ?>>Widowed</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="spouse_name">Spouse Name</label>
                    <input class="form-input" id="spouse_name" name="spouse_name" value="<?php echo $value('spouse_name'); ?>">
                </div>
                <div>
                    <label class="form-label" for="wedding_anniversary">Wedding Anniversary</label>
                    <input class="form-input" id="wedding_anniversary" type="date" name="wedding_anniversary" value="<?php echo $value('wedding_anniversary'); ?>">
                </div>
                <div>
                    <label class="form-label" for="emergency_contact">Emergency Contact</label>
                    <input class="form-input" id="emergency_contact" name="emergency_contact" value="<?php echo $value('emergency_contact'); ?>">
                </div>
                <div>
                    <label class="form-label" for="emergency_phone">Emergency Phone</label>
                    <input class="form-input" id="emergency_phone" name="emergency_phone" value="<?php echo $value('emergency_phone'); ?>">
                </div>
            </div>
        </div>

        <div>
            <label class="form-label" for="notes">Notes</label>
            <textarea class="form-input" id="notes" name="notes" rows="4"><?php echo $value('notes'); ?></textarea>
        </div>

        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="<?php echo BASE_URL; ?>/admin/members" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Cancel</a>
            <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">
                <?php echo $isEdit ? 'Save Changes' : 'Create Member'; ?>
            </button>
        </div>
    </form>
</section>
