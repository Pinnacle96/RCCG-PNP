<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[1fr_0.75fr] lg:items-end">
            <div>
                <p class="eyebrow text-rccg-gold">Join</p>
                <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl">Become part of the family</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Complete the membership application below. Our team will review it and reach out to welcome you.</p>
            </div>
            <div class="page-hero-card">
                <p class="text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Simple process</p>
                <p class="mt-3 text-gray-100">Share your details, faith background, and ministry interests in three quick steps.</p>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-4xl mx-auto px-4">
            <form method="post" action="<?php echo BASE_URL; ?>/join" enctype="multipart/form-data" class="form-panel">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

                <!-- Step indicator -->
                <div class="mb-8 grid gap-3 text-sm font-extrabold sm:grid-cols-3">
                    <span class="join-indicator rounded-full bg-red-50 px-4 py-3 text-rccg-red" data-ind="1">1. Personal</span>
                    <span class="join-indicator rounded-full bg-gray-50 px-4 py-3 text-gray-400" data-ind="2">2. Faith</span>
                    <span class="join-indicator rounded-full bg-gray-50 px-4 py-3 text-gray-400" data-ind="3">3. Ministry</span>
                </div>

                <!-- Step 1: Personal -->
                <div class="join-step" data-step="1">
                    <h2 class="text-2xl font-extrabold text-rccg-navy mb-4">Personal Information</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div><label class="form-label">First Name *</label><input class="form-input" name="first_name" required></div>
                        <div><label class="form-label">Last Name *</label><input class="form-input" name="last_name" required></div>
                        <div><label class="form-label">Middle Name</label><input class="form-input" name="middle_name"></div>
                        <div>
                            <label class="form-label">Gender *</label>
                            <select class="form-select" name="gender" required>
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div><label class="form-label">Date of Birth</label><input class="form-input" type="date" name="date_of_birth"></div>
                        <div><label class="form-label">Marital Status</label>
                            <select class="form-select" name="marital_status">
                                <option value="">Select</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div><label class="form-label">Phone</label><input class="form-input" name="phone"></div>
                        <div><label class="form-label">Email</label><input class="form-input" type="email" name="email"></div>
                        <div class="md:col-span-2"><label class="form-label">Address</label><textarea class="form-input" name="address" rows="2"></textarea></div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="button" class="join-next btn-primary px-6 py-3" data-goto="2">Next</button>
                    </div>
                </div>

                <!-- Step 2: Faith -->
                <div class="join-step hidden" data-step="2">
                    <h2 class="text-2xl font-extrabold text-rccg-navy mb-4">Faith Background</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div><label class="form-label">Occupation</label><input class="form-input" name="occupation"></div>
                        <div><label class="form-label">State of Origin</label><input class="form-input" name="state_of_origin"></div>
                        <label class="flex items-center gap-3 md:col-span-2"><input type="checkbox" name="water_baptized" value="1"><span class="font-semibold text-gray-700">I have been baptized in water</span></label>
                        <label class="flex items-center gap-3 md:col-span-2"><input type="checkbox" name="holy_ghost_baptized" value="1"><span class="font-semibold text-gray-700">I have received the Holy Ghost baptism</span></label>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" class="join-back btn-secondary px-6 py-3" data-goto="1">Back</button>
                        <button type="button" class="join-next btn-primary px-6 py-3" data-goto="3">Next</button>
                    </div>
                </div>

                <!-- Step 3: Ministry -->
                <div class="join-step hidden" data-step="3">
                    <h2 class="text-2xl font-extrabold text-rccg-navy mb-4">Ministry Interest</h2>
                    <div class="grid gap-4">
                        <div><label class="form-label">Which ministries or units interest you?</label><textarea class="form-input" name="ministry_interest" rows="3" placeholder="e.g. Choir, Ushering, Children's church"></textarea></div>
                        <div>
                            <label class="form-label">Passport Photograph</label>
                            <input class="form-input" type="file" name="profile_photo" accept="image/*">
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP, max 5MB.</p>
                        </div>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" class="join-back btn-secondary px-6 py-3" data-goto="2">Back</button>
                        <button type="submit" class="btn-primary px-6 py-3">Submit Application</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const steps = document.querySelectorAll('.join-step');
    const indicators = document.querySelectorAll('.join-indicator');
    function go(n) {
        steps.forEach(function (s) { s.classList.toggle('hidden', s.getAttribute('data-step') !== String(n)); });
        indicators.forEach(function (i) {
            const on = i.getAttribute('data-ind') === String(n);
            i.classList.toggle('text-rccg-red', on);
            i.classList.toggle('text-gray-400', !on);
            i.classList.toggle('bg-red-50', on);
            i.classList.toggle('bg-gray-50', !on);
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    document.querySelectorAll('.join-next').forEach(function (b) {
        b.addEventListener('click', function () {
            // Validate visible required fields before advancing
            const current = b.closest('.join-step');
            const invalid = current.querySelector('input:invalid, select:invalid');
            if (invalid) { invalid.reportValidity(); return; }
            go(b.getAttribute('data-goto'));
        });
    });
    document.querySelectorAll('.join-back').forEach(function (b) {
        b.addEventListener('click', function () { go(b.getAttribute('data-goto')); });
    });
});
</script>
