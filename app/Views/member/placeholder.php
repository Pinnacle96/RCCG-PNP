<section class="space-y-6">
    <div class="card">
        <h1 class="text-2xl font-bold text-rccg-navy mb-2"><?php echo Helpers::escape($title); ?></h1>
        <?php if (!empty($unlinked)): ?>
            <p class="text-gray-600">Your portal account is not linked to a member profile yet. Please contact the church office so we can connect your records.</p>
        <?php else: ?>
            <p class="text-gray-600">This member portal section is wired and ready for its feature implementation.</p>
        <?php endif; ?>
    </div>
</section>
