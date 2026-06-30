<main class="soft-band py-20">
    <section class="max-w-3xl mx-auto px-4 text-center">
        <?php if ($gift): ?>
            <span class="empty-state-icon"><i class="fas fa-receipt"></i></span>
            <p class="eyebrow">Giving reference</p>
            <h1 class="mt-3 font-display text-4xl font-extrabold text-rccg-navy md:text-5xl"><?php echo Helpers::escape($gift['reference_no']); ?></h1>
            <p class="mt-4 text-gray-600">
                Status: <strong class="capitalize"><?php echo Helpers::escape($gift['payment_status']); ?></strong>
                for <?php echo Helpers::escape(Helpers::currency((float) $gift['amount'])); ?>.
            </p>
            <?php if ($gift['payment_status'] === 'success' && $receiptPath): ?>
                <a href="<?php echo BASE_URL . '/' . Helpers::escape($receiptPath); ?>" class="btn-primary mt-8 px-6 py-3" target="_blank" rel="noopener">Open Receipt</a>
            <?php elseif ($gift['giving_method'] === 'bank_transfer'): ?>
                <p class="mx-auto mt-4 max-w-xl text-gray-500">Please complete your bank transfer. The finance team will confirm it manually.</p>
            <?php else: ?>
                <p class="mx-auto mt-4 max-w-xl text-gray-500">If you already completed payment, verification will update this record shortly.</p>
            <?php endif; ?>
        <?php else: ?>
            <span class="empty-state-icon"><i class="fas fa-circle-question"></i></span>
            <h1 class="font-display text-4xl font-extrabold text-rccg-navy">Giving record not found</h1>
        <?php endif; ?>
        <div><a href="<?php echo BASE_URL; ?>/give" class="mt-8 inline-flex font-extrabold text-rccg-red">Back to giving</a></div>
    </section>
</main>
