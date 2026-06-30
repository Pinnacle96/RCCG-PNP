<div>
    <h1 class="font-display text-3xl font-bold text-rccg-navy mb-2">Reset Password</h1>
    <p class="text-gray-600 mb-6">Choose a new password for your account.</p>

    <form method="post" action="<?php echo BASE_URL; ?>/reset-password" data-validate class="space-y-4">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
        <input type="hidden" name="token" value="<?php echo Helpers::escape($resetToken); ?>">

        <div>
            <label class="form-label" for="password">New Password</label>
            <input class="form-input" id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
        </div>

        <div>
            <label class="form-label" for="password_confirmation">Confirm New Password</label>
            <input class="form-input" id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>

        <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson w-full px-6 py-3 rounded-lg font-semibold" type="submit">Reset Password</button>
    </form>
</div>
