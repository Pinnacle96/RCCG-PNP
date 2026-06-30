<?php $base = BASE_URL . '/admin/prayer'; ?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-rccg-navy"><?php echo Helpers::escape($request['subject']); ?></h1>
            <p class="text-gray-600">
                From <?php echo Helpers::escape($request['requester_name']); ?>
                · <?php echo Helpers::escape(ucfirst($request['category'])); ?>
                · <?php echo Helpers::escape(Helpers::formatDateTime($request['created_at'])); ?>
            </p>
        </div>
        <a href="<?php echo $base; ?>" class="btn-primary bg-rccg-navy text-white px-5 py-3">Back</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card space-y-4 lg:col-span-2">
            <div>
                <h2 class="text-lg font-bold text-rccg-navy">Request</h2>
                <p class="mt-2 whitespace-pre-line text-gray-700"><?php echo Helpers::escape($request['request_text']); ?></p>
            </div>
            <div class="grid gap-3 text-sm sm:grid-cols-2">
                <p><span class="text-gray-500">Email:</span> <?php echo Helpers::escape($request['email'] ?: 'Not provided'); ?></p>
                <p><span class="text-gray-500">Phone:</span> <?php echo Helpers::escape($request['phone'] ?: 'Not provided'); ?></p>
                <p><span class="text-gray-500">Privacy:</span> <?php echo $request['is_private'] ? 'Private' : 'Public wall'; ?></p>
                <p><span class="text-gray-500">Prayer count:</span> <?php echo (int) $request['prayer_count']; ?></p>
            </div>

            <?php if (!empty($request['email'])): ?>
                <form method="post" action="<?php echo $base; ?>/reply/<?php echo (int) $request['id']; ?>" class="space-y-3 border-t border-gray-100 pt-4">
                    <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                    <h3 class="font-bold text-rccg-navy">Email the requester</h3>
                    <textarea class="form-input" name="reply_text" rows="4" placeholder="Write an encouraging response..." required></textarea>
                    <button class="btn-primary bg-rccg-red text-white hover:bg-rccg-crimson px-5 py-3" type="submit"><i class="fas fa-paper-plane mr-2"></i>Send Reply</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="card space-y-4">
            <h2 class="text-lg font-bold text-rccg-navy">Manage</h2>
            <form method="post" action="<?php echo $base; ?>/update/<?php echo (int) $request['id']; ?>" class="space-y-4">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Status</label>
                    <select class="form-input" name="status">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $request['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Assign to</label>
                    <select class="form-input" name="assigned_to">
                        <option value="0">Unassigned</option>
                        <?php foreach ($team as $member): ?>
                            <option value="<?php echo (int) $member['id']; ?>" <?php echo (int) $request['assigned_to'] === (int) $member['id'] ? 'selected' : ''; ?>><?php echo Helpers::escape($member['email']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-700">Answered note</label>
                    <textarea class="form-input" name="answered_note" rows="3" placeholder="Testimony / outcome..."><?php echo Helpers::escape($request['answered_note'] ?? ''); ?></textarea>
                </div>
                <button class="btn-primary bg-rccg-navy text-white px-5 py-3 w-full" type="submit">Save</button>
            </form>

            <form method="post" action="<?php echo $base; ?>/delete/<?php echo (int) $request['id']; ?>" onsubmit="return confirm('Delete this prayer request?');" class="border-t border-gray-100 pt-4">
                <input type="hidden" name="<?php echo CSRF_TOKEN_NAME; ?>" value="<?php echo Helpers::escape($csrf); ?>">
                <button class="text-rccg-red font-semibold" type="submit"><i class="fas fa-trash mr-2"></i>Delete request</button>
            </form>
        </div>
    </div>
</section>
