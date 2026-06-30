<div>
    <h1 class="font-display text-3xl font-bold text-rccg-navy mb-2">Forgot Password</h1>
    <p class="text-gray-600 mb-6">Enter your email and we will prepare a reset link.</p>

    <form method="post" action="<?php echo BASE_URL; ?>/forgot-password" data-validate class="space-y-4">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div>
            <label class="form-label" for="email">Email</label>
            <input class="form-input" id="email" type="email" name="email" required autocomplete="email">
        </div>

        <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson w-full px-6 py-3 rounded-lg font-semibold" type="submit">Send Reset Link</button>
    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
        Remember your password?
        <a href="<?php echo BASE_URL; ?>/login" class="text-rccg-red font-semibold">Sign in</a>
    </p>
</div>
