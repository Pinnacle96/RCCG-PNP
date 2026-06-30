<?php
$isEdit = $mode === 'edit';
$value = function (string $key, string $default = '') use ($service): string {
    return Helpers::escape((string) ($service[$key] ?? $default));
};
$selected = function (string $key, string $option, string $default = '') use ($service): string {
    return (($service[$key] ?? $default) === $option) ? 'selected' : '';
};
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo $isEdit ? 'Edit Service' : 'Create Service'; ?></h1>
            <p class="text-gray-600"><?php echo $isEdit ? 'Update service details and counts.' : 'Create a service before marking attendance.'; ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/attendance" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Back</a>
    </div>

    <form method="post" action="<?php echo Helpers::escape($action); ?>" class="card space-y-8">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Service Details</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="service_type">Service Type</label>
                    <select class="form-select" id="service_type" name="service_type">
                        <option value="sunday_first" <?php echo $selected('service_type', 'sunday_first', 'sunday_first'); ?>>Sunday First</option>
                        <option value="sunday_second" <?php echo $selected('service_type', 'sunday_second'); ?>>Sunday Second</option>
                        <option value="wednesday" <?php echo $selected('service_type', 'wednesday'); ?>>Wednesday</option>
                        <option value="friday" <?php echo $selected('service_type', 'friday'); ?>>Friday</option>
                        <option value="special" <?php echo $selected('service_type', 'special'); ?>>Special</option>
                        <option value="cell" <?php echo $selected('service_type', 'cell'); ?>>Cell</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="service_date">Service Date</label>
                    <input class="form-input" id="service_date" type="date" name="service_date" required value="<?php echo $value('service_date', date('Y-m-d')); ?>">
                </div>
                <div>
                    <label class="form-label" for="preacher">Preacher</label>
                    <input class="form-input" id="preacher" name="preacher" value="<?php echo $value('preacher'); ?>">
                </div>
                <div class="md:col-span-3">
                    <label class="form-label" for="theme">Theme</label>
                    <input class="form-input" id="theme" name="theme" value="<?php echo $value('theme'); ?>">
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-rccg-navy mb-4">Manual Counts & Giving Summary</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label" for="men_count">Men</label>
                    <input class="form-input" id="men_count" type="number" min="0" name="men_count" value="<?php echo $value('men_count', '0'); ?>">
                </div>
                <div>
                    <label class="form-label" for="women_count">Women</label>
                    <input class="form-input" id="women_count" type="number" min="0" name="women_count" value="<?php echo $value('women_count', '0'); ?>">
                </div>
                <div>
                    <label class="form-label" for="children_count">Children</label>
                    <input class="form-input" id="children_count" type="number" min="0" name="children_count" value="<?php echo $value('children_count', '0'); ?>">
                </div>
                <div>
                    <label class="form-label" for="visitors_count">Visitors</label>
                    <input class="form-input" id="visitors_count" type="number" min="0" name="visitors_count" value="<?php echo $value('visitors_count', '0'); ?>">
                </div>
                <div>
                    <label class="form-label" for="offering_amount">Offering Amount</label>
                    <input class="form-input" id="offering_amount" type="number" min="0" step="0.01" name="offering_amount" value="<?php echo $value('offering_amount', '0.00'); ?>">
                </div>
                <div>
                    <label class="form-label" for="tithe_amount">Tithe Amount</label>
                    <input class="form-input" id="tithe_amount" type="number" min="0" step="0.01" name="tithe_amount" value="<?php echo $value('tithe_amount', '0.00'); ?>">
                </div>
            </div>
        </div>

        <div>
            <label class="form-label" for="notes">Notes</label>
            <textarea class="form-input" id="notes" name="notes" rows="4"><?php echo $value('notes'); ?></textarea>
        </div>

        <label class="flex items-center gap-3">
            <input class="form-checkbox" type="checkbox" name="is_closed" value="1" <?php echo !empty($service['is_closed']) ? 'checked' : ''; ?>>
            <span class="font-semibold text-gray-700">Close this service record</span>
        </label>

        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="<?php echo BASE_URL; ?>/admin/attendance" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Cancel</a>
            <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">
                <?php echo $isEdit ? 'Save Changes' : 'Create Service'; ?>
            </button>
        </div>
    </form>
</section>
