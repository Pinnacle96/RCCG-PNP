<?php
$isEdit = !empty($user);
$action = BASE_URL . '/admin/users' . ($isEdit ? '/edit/' . (int) $user['id'] : '');
$currentRole = $user['role'] ?? ROLE_MEMBER;
$currentMember = (string) ($user['member_id'] ?? '');
$isActive = $isEdit ? (int) $user['is_active'] === 1 : true;
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo $isEdit ? 'Edit User' : 'Add User'; ?></h1>
            <p class="text-gray-600"><?php echo $isEdit ? 'Update this login account.' : 'Create a new login account.'; ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/users" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Back</a>
    </div>

    <form method="post" action="<?php echo Helpers::escape($action); ?>" class="card space-y-6">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="form-label" for="email">Email <span class="text-rccg-red">*</span></label>
                <input class="form-input" id="email" type="email" name="email" value="<?php echo Helpers::escape($user['email'] ?? ''); ?>" required>
            </div>

            <div>
                <label class="form-label" for="role">Role <span class="text-rccg-red">*</span></label>
                <select class="form-select" id="role" name="role" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo Helpers::escape($role); ?>" <?php echo $currentRole === $role ? 'selected' : ''; ?>>
                            <?php echo Helpers::escape(ucwords(str_replace('_', ' ', $role))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label" for="password">Password <?php echo $isEdit ? '' : '<span class="text-rccg-red">*</span>'; ?></label>
                <input class="form-input" id="password" type="password" name="password" minlength="8" <?php echo $isEdit ? '' : 'required'; ?> autocomplete="new-password">
                <p class="mt-1 text-xs text-gray-500"><?php echo $isEdit ? 'Leave blank to keep the current password.' : 'Minimum 8 characters.'; ?></p>
            </div>

            <div>
                <label class="form-label" for="member_id">Linked Member</label>
                <select class="form-select" id="member_id" name="member_id">
                    <option value="">— None —</option>
                    <?php foreach ($members as $m): ?>
                        <option value="<?php echo (int) $m['value']; ?>" <?php echo $currentMember === (string) $m['value'] ? 'selected' : ''; ?>>
                            <?php echo Helpers::escape($m['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1 text-xs text-gray-500">Link this account to a member record (optional).</p>
            </div>

            <div>
                <label class="flex items-center gap-3 pt-8">
                    <input class="form-checkbox" type="checkbox" name="is_active" value="1" <?php echo $isActive ? 'checked' : ''; ?>>
                    <span class="font-semibold text-gray-700">Active account</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="<?php echo BASE_URL; ?>/admin/users" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Cancel</a>
            <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">
                <?php echo $isEdit ? 'Save Changes' : 'Create User'; ?>
            </button>
        </div>
    </form>
</section>
