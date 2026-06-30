<section class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-6">
        <div class="card">
            <p class="text-sm text-gray-500">Members</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $stats['members']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Sermons</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $stats['sermons']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Events</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $stats['events']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Giving</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo Helpers::escape(Helpers::currency((float) $stats['giving'])); ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Services</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $stats['services']; ?></p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Attendance</p>
            <p class="text-3xl font-bold text-rccg-navy"><?php echo (int) $stats['attendance']; ?></p>
        </div>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold text-rccg-navy mb-2">Admin Foundation Ready</h1>
        <p class="text-gray-600">The protected dashboard route is wired. The next pass can add live statistics and module navigation backed by the database.</p>
    </div>
</section>
