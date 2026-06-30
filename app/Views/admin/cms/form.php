<?php
$isEdit = $mode === 'edit';
$fieldValue = function (string $name) use ($row): string {
    $value = $row[$name] ?? '';
    if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
        $value = str_replace(' ', 'T', substr($value, 0, 16));
    }
    return Helpers::escape((string) $value);
};
$isChecked = function (string $name) use ($row, $isEdit): string {
    if (!$isEdit) {
        return 'checked';
    }
    return !empty($row[$name]) ? 'checked' : '';
};
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo $isEdit ? 'Edit ' : 'Add '; ?><?php echo Helpers::escape($config['singular']); ?></h1>
            <p class="text-gray-600"><?php echo $isEdit ? 'Update this record.' : 'Create a new record.'; ?></p>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin/<?php echo Helpers::escape($module); ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Back</a>
    </div>

    <form method="post" action="<?php echo Helpers::escape($action); ?>" enctype="multipart/form-data" class="card space-y-6">
        <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">

        <div class="grid gap-4 md:grid-cols-2">
            <?php foreach ($config['fields'] as $name => $field): ?>
                <?php
                $type = $field['type'] ?? 'text';
                $required = !empty($field['required']) ? 'required' : '';
                $wide = in_array($type, ['textarea', 'richtext'], true) ? 'md:col-span-2' : '';
                ?>
                <div class="<?php echo $wide; ?>">
                    <?php if ($type === 'checkbox'): ?>
                        <label class="flex items-center gap-3 pt-8">
                            <input class="form-checkbox" type="checkbox" name="<?php echo Helpers::escape($name); ?>" value="1" <?php echo $isChecked($name); ?>>
                            <span class="font-semibold text-gray-700"><?php echo Helpers::escape($field['label']); ?></span>
                        </label>
                    <?php else: ?>
                        <label class="form-label" for="<?php echo Helpers::escape($name); ?>"><?php echo Helpers::escape($field['label']); ?></label>
                        <?php if ($type === 'upload'): ?>
                            <?php $current = (string) ($row[$name] ?? ''); ?>
                            <?php if ($current !== ''): ?>
                                <div class="mb-2 flex items-center gap-3">
                                    <?php if (($field['accept'] ?? 'image') === 'image'): ?>
                                        <img src="<?php echo Helpers::escape(UPLOAD_URL . ($field['subdir'] ?? '') . $current); ?>" alt="current" class="h-16 w-16 rounded object-cover border">
                                    <?php endif; ?>
                                    <a href="<?php echo Helpers::escape(UPLOAD_URL . ($field['subdir'] ?? '') . $current); ?>" target="_blank" class="text-sm text-rccg-red break-all"><?php echo Helpers::escape($current); ?></a>
                                </div>
                            <?php endif; ?>
                            <input class="form-input" id="<?php echo Helpers::escape($name); ?>" type="file" name="<?php echo Helpers::escape($name); ?>" accept="<?php echo ($field['accept'] ?? 'image') === 'audio' ? 'audio/*' : 'image/*'; ?>" <?php echo ($current === '' ? $required : ''); ?>>
                            <p class="mt-1 text-xs text-gray-500"><?php echo $current !== '' ? 'Upload a new file to replace the current one.' : 'Select a file to upload.'; ?></p>
                        <?php elseif ($type === 'richtext'): ?>
                            <textarea class="form-input tinymce" id="<?php echo Helpers::escape($name); ?>" name="<?php echo Helpers::escape($name); ?>" rows="10" <?php echo $required; ?>><?php echo $fieldValue($name); ?></textarea>
                        <?php elseif ($type === 'textarea'): ?>
                            <textarea class="form-input" id="<?php echo Helpers::escape($name); ?>" name="<?php echo Helpers::escape($name); ?>" rows="6" <?php echo $required; ?>><?php echo $fieldValue($name); ?></textarea>
                        <?php elseif ($type === 'select'): ?>
                            <select class="form-select" id="<?php echo Helpers::escape($name); ?>" name="<?php echo Helpers::escape($name); ?>" <?php echo $required; ?>>
                                <option value="">Select</option>
                                <?php foreach (($field['options'] ?? []) as $value => $label): ?>
                                    <option value="<?php echo Helpers::escape((string) $value); ?>" <?php echo ((string) ($row[$name] ?? '') === (string) $value) ? 'selected' : ''; ?>>
                                        <?php echo Helpers::escape((string) $label); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php foreach (($options[$name] ?? []) as $option): ?>
                                    <option value="<?php echo Helpers::escape((string) $option['value']); ?>" <?php echo ((string) ($row[$name] ?? '') === (string) $option['value']) ? 'selected' : ''; ?>>
                                        <?php echo Helpers::escape((string) $option['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input class="form-input" id="<?php echo Helpers::escape($name); ?>" type="<?php echo Helpers::escape($type); ?>" name="<?php echo Helpers::escape($name); ?>" value="<?php echo $fieldValue($name); ?>" <?php echo $required; ?>>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-end gap-3 border-t pt-6">
            <a href="<?php echo BASE_URL; ?>/admin/<?php echo Helpers::escape($module); ?>" class="btn-outline border-2 border-gray-300 text-gray-700 px-5 py-3">Cancel</a>
            <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-6 py-3" type="submit">
                <?php echo $isEdit ? 'Save Changes' : 'Create Record'; ?>
            </button>
        </div>
    </form>
</section>

<?php $hasRichText = false; foreach ($config['fields'] as $f) { if (($f['type'] ?? '') === 'richtext') { $hasRichText = true; break; } } ?>
<?php if ($hasRichText): ?>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.tinymce) {
        tinymce.init({
            selector: 'textarea.tinymce',
            menubar: false,
            plugins: 'lists link autolink code',
            toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link | code | removeformat',
            height: 360
        });
    }
});
</script>
<?php endif; ?>
