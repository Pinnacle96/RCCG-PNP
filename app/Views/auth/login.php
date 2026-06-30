<div>
    <h1 class="font-display text-3xl font-bold text-rccg-navy mb-2">Welcome Back</h1>
    <p class="text-gray-600 mb-6">Sign in to continue to the parish dashboard.</p>

    <form method="post" action="<?php echo BASE_URL; ?>/login" data-validate class="space-y-4">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div>
            <label class="form-label" for="email">Email</label>
            <input class="form-input" id="email" type="email" name="email" required autocomplete="email">
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <input class="form-input" id="password" type="password" name="password" required autocomplete="current-password">
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input class="form-checkbox" type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson w-full px-6 py-3 rounded-lg font-semibold" type="submit">Sign In</button>
    </form>

    <div class="flex items-center justify-between text-sm text-gray-600 mt-6">
        <a href="<?php echo BASE_URL; ?>/forgot-password" class="text-rccg-red font-semibold">Forgot password?</a>
        <a href="<?php echo BASE_URL; ?>/register" class="text-rccg-red font-semibold">Create account</a>
    </div>
</div>
