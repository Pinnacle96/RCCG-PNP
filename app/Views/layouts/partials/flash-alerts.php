<!-- Flash Messages -->
<?php if (!empty($flash)): ?>
    <div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
        <?php foreach ($flash as $message): ?>
            <div class="alert alert-<?php echo Helpers::escape($message['type']); ?> alert-dismissible fade show" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-<?php echo $message['type'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?> mr-3 text-<?php echo $message['type'] === 'error' ? 'red' : 'green'; ?>-600 text-xl"></i>
                    <div>
                        <p class="font-semibold text-gray-800">
                            <?php echo $message['type'] === 'error' ? 'Error' : 'Success'; ?>
                        </p>
                        <p class="text-gray-600 text-sm">
                            <?php echo Helpers::escape($message['message']); ?>
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('[data-dismiss="alert"]').forEach(function(btn) {
            btn.closest('.alert').style.display = 'none';
        });
    }, 5000);
});
</script>
