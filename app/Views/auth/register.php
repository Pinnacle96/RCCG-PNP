<div>
    <h1 class="font-display text-3xl font-bold text-rccg-navy mb-2">Create Account</h1>
    <p class="text-gray-600 mb-6">Register for access to the member portal.</p>

    <form method="post" action="<?php echo BASE_URL; ?>/register" data-validate class="space-y-4">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div>
            <label class="form-label" for="email">Email</label>
            <input class="form-input" id="email" type="email" name="email" required autocomplete="email">
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <input class="form-input" id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
        </div>

        <div>
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input class="form-input" id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>

        <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson w-full px-6 py-3 rounded-lg font-semibold" type="submit">Create Account</button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Already have an account?
        <a href="<?php echo BASE_URL; ?>/login" class="text-rccg-red font-semibold">Sign in</a>
    </p>
</div>
