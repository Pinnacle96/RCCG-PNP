<?php
$groupKeys = array_keys($schema);
$first = $groupKeys[0] ?? 'general';
?>

<section class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-rccg-navy">Site Settings</h1>
        <p class="text-gray-600">Configure church information, services, giving, and integrations.</p>
    </div>

    <form method="post" action="<?php echo BASE_URL; ?>/admin/settings" class="space-y-6">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div class="flex flex-wrap gap-2 border-b border-gray-200">
            <?php foreach ($schema as $key => $group): ?>
                <button type="button"
                        class="settings-tab px-4 py-2 text-sm font-semibold border-b-2 -mb-px transition <?php echo $key === $first ? 'border-rccg-red text-rccg-red' : 'border-transparent text-gray-500 hover:text-rccg-navy'; ?>"
                        data-tab="<?php echo Helpers::escape($key); ?>">
                    <i class="fas <?php echo Helpers::escape($group['icon']); ?> mr-2"></i><?php echo Helpers::escape($group['title']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($schema as $key => $group): ?>
            <div class="settings-panel card <?php echo $key === $first ? '' : 'hidden'; ?>" data-panel="<?php echo Helpers::escape($key); ?>">
                <h2 class="mb-4 text-lg font-bold text-rccg-navy"><?php echo Helpers::escape($group['title']); ?></h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <?php foreach ($group['fields'] as $name => $field): ?>
                        <?php
                        $type = $field['type'] ?? 'text';
                        $value = (string) ($values[$name] ?? '');
                        $wide = $type === 'textarea' ? 'md:col-span-2' : '';
                        ?>
                        <div class="<?php echo $wide; ?>">
                            <?php if ($type === 'checkbox'): ?>
                                <label class="flex items-center gap-3 pt-2">
                                    <input class="form-checkbox" type="checkbox" name="<?php echo Helpers::escape($name); ?>" value="1" <?php echo $value === '1' ? 'checked' : ''; ?>>
                                    <span class="font-semibold text-gray-700"><?php echo Helpers::escape($field['label']); ?></span>
                                </label>
                            <?php elseif ($type === 'textarea'): ?>
                                <label class="form-label" for="<?php echo Helpers::escape($name); ?>"><?php echo Helpers::escape($field['label']); ?></label>
                                <textarea class="form-input" id="<?php echo Helpers::escape($name); ?>" name="<?php echo Helpers::escape($name); ?>" rows="3"><?php echo Helpers::escape($value); ?></textarea>
                            <?php else: ?>
                                <label class="form-label" for="<?php echo Helpers::escape($name); ?>"><?php echo Helpers::escape($field['label']); ?></label>
                                <input class="form-input" id="<?php echo Helpers::escape($name); ?>" type="<?php echo Helpers::escape($type); ?>" name="<?php echo Helpers::escape($name); ?>" value="<?php echo Helpers::escape($value); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-end gap-3">
            <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.settings-tab');
    const panels = document.querySelectorAll('.settings-panel');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            const target = tab.getAttribute('data-tab');
            tabs.forEach(function (t) {
                t.classList.remove('border-rccg-red', 'text-rccg-red');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            tab.classList.add('border-rccg-red', 'text-rccg-red');
            tab.classList.remove('border-transparent', 'text-gray-500');
            panels.forEach(function (p) {
                p.classList.toggle('hidden', p.getAttribute('data-panel') !== target);
            });
        });
    });
});
</script>
