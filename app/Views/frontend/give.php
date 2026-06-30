<?php
/**
 * @var string $csrf
 * @var array{bank_name: ?string, bank_account_name: ?string, bank_account_number: ?string} $settings
 */
?>
<main>
    <section class="page-hero">
        <div class="hero-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 lg:grid-cols-[1fr_0.8fr] lg:items-end">
            <div>
                <p class="eyebrow text-rccg-gold">Give</p>
                <h1 class="mt-4 font-display text-4xl font-extrabold leading-tight md:text-6xl">Partner with the work God is doing</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">Your generosity supports worship, discipleship, care, outreach, and the practical ministry of the parish.</p>
                <div class="mt-8 bg-white/10 backdrop-blur rounded-xl p-6 border border-white/20">
                    <p class="text-gray-100 italic text-lg">
                        "Give, and it shall be given unto you; good measure, pressed down, and shaken together, and running over, shall men give into your bosom. For with the same measure that ye mete withal it shall be measured to you again."
                    </p>
                    <p class="text-rccg-gold font-bold mt-2">Luke 6:38 (KJV)</p>
                </div>
            </div>
            <div class="page-hero-card">
                <p class="text-sm font-extrabold uppercase tracking-wide text-rccg-gold">Secure giving</p>
                <p class="mt-3 text-gray-100">Give online through Paystack or make a bank transfer.</p>
            </div>
        </div>
    </section>

    <section class="soft-band py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <p class="eyebrow">Choose your method</p>
                <h2 class="mt-2 section-title">How would you like to give today?</h2>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Online Giving Section -->
                <div class="card shadow-xl transform hover:-translate-y-2 transition duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="feature-icon bg-gradient-to-br from-rccg-navy to-rccg-red"><i class="fas fa-credit-card"></i></span>
                        <h3 class="text-xl font-extrabold text-rccg-navy">Online Giving</h3>
                    </div>
                    <p class="text-gray-600 mb-6">Give securely online using Paystack with your debit card or bank account.</p>
                    <form method="post" action="<?php echo BASE_URL; ?>/give/initiate" class="space-y-5">
                        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                        <input type="hidden" name="giving_method" value="online">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><label class="form-label">Full name</label><input class="form-input" name="giver_name" required></div>
                            <div><label class="form-label">Email</label><input class="form-input" type="email" name="giver_email" required></div>
                            <div><label class="form-label">Phone</label><input class="form-input" name="giver_phone"></div>
                            <div>
                                <label class="form-label">Giving type</label>
                                <select class="form-select" name="giving_type">
                                    <option value="offering">Offering</option>
                                    <option value="tithe">Tithe</option>
                                    <option value="seed">Seed</option>
                                    <option value="project">Project</option>
                                    <option value="welfare">Welfare</option>
                                    <option value="mission">Mission</option>
                                    <option value="thanksgiving">Thanksgiving</option>
                                    <option value="vow">Vow</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div><label class="form-label">Amount (NGN)</label><input class="form-input" type="number" min="1" step="0.01" name="amount" required></div>
                        </div>
                        <div>
                            <label class="form-label">Optional note</label>
                            <textarea class="form-input" name="description" rows="3"></textarea>
                        </div>
                        <button class="btn-primary px-6 py-3 w-full"><i class="fas fa-heart"></i> Give Online</button>
                    </form>
                </div>

                <!-- Bank Transfer Section -->
                <div class="card shadow-xl transform hover:-translate-y-2 transition duration-300">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="feature-icon bg-gradient-to-br from-rccg-navy to-rccg-red"><i class="fas fa-building-columns"></i></span>
                        <h3 class="text-xl font-extrabold text-rccg-navy">Bank Transfer</h3>
                    </div>

                    <?php if (!empty($settings['bank_name']) && !empty($settings['bank_account_name']) && !empty($settings['bank_account_number'])): ?>
                        <div style="background: linear-gradient(135deg, #0a1628 0%, #c41e3a 100%); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);" class="mb-6 transform hover:scale-[1.02] transition">
                            <p style="color: #d4a853; font-size: 0.875rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;" class="flex items-center gap-2">
                                <i class="fas fa-university"></i> Our Bank Account
                            </p>
                            <div class="space-y-4">
                                <div>
                                    <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">Bank</p>
                                    <p style="color: white; font-size: 1.25rem; font-weight: bold;"><?php echo Helpers::escape($settings['bank_name']); ?></p>
                                </div>
                                <div>
                                    <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">Account Name</p>
                                    <p style="color: white; font-size: 1.125rem; font-weight: bold;"><?php echo Helpers::escape($settings['bank_account_name']); ?></p>
                                </div>
                                <div>
                                    <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem;">Account Number</p>
                                    <p style="color: #d4a853; font-size: 2.25rem; font-weight: 800; letter-spacing: 0.05em;"><?php echo Helpers::escape($settings['bank_account_number']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-gray-100 rounded-xl p-6 mb-6 border border-gray-200">
                            <p class="text-gray-600"><i class="fas fa-info-circle" style="color: #0a1628;"></i> Bank account details will be available soon.</p>
                        </div>
                    <?php endif; ?>

                    <p class="text-gray-600 mb-6">Please use your name and giving type as the transfer reference.</p>

                    <form method="post" action="<?php echo BASE_URL; ?>/give/initiate" class="space-y-5 pt-4 border-t border-gray-200">
                        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                        <input type="hidden" name="giving_method" value="bank_transfer">
                        <p class="text-sm font-bold text-rccg-navy">Record your transfer:</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><label class="form-label">Full name</label><input class="form-input" name="giver_name" required></div>
                            <div><label class="form-label">Email</label><input class="form-input" type="email" name="giver_email" required></div>
                            <div><label class="form-label">Phone</label><input class="form-input" name="giver_phone"></div>
                            <div>
                                <label class="form-label">Giving type</label>
                                <select class="form-select" name="giving_type">
                                    <option value="offering">Offering</option>
                                    <option value="tithe">Tithe</option>
                                    <option value="seed">Seed</option>
                                    <option value="project">Project</option>
                                    <option value="welfare">Welfare</option>
                                    <option value="mission">Mission</option>
                                    <option value="thanksgiving">Thanksgiving</option>
                                    <option value="vow">Vow</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div><label class="form-label">Amount (NGN)</label><input class="form-input" type="number" min="1" step="0.01" name="amount" required></div>
                        </div>
                        <div>
                            <label class="form-label">Optional note</label>
                            <textarea class="form-input" name="description" rows="3"></textarea>
                        </div>
                        <button class="btn-secondary px-6 py-3 w-full"><i class="fas fa-check-circle"></i> I've Made the Transfer</button>
                    </form>
                </div>
            </div>

            <div class="mt-10 card text-center">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <span class="feature-icon"><i class="fas fa-shield-heart"></i></span>
                    <h3 class="text-xl font-extrabold text-rccg-navy">Stewardship</h3>
                </div>
                <p class="text-gray-600">Every gift is handled with care and recorded for accountability.</p>
            </div>
        </div>
    </section>
</main>
