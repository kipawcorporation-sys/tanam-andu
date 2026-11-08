<?php
/**
 * Partial: Flash Messages Alert
 * Menampilkan pesan sukses, error, peringatan, atau info dari Session.
 */

$flashTypes = [
    'success' => ['class' => 'alert-success', 'icon' => 'bi-check-circle-fill'],
    'error'   => ['class' => 'alert-danger',  'icon' => 'bi-exclamation-triangle-fill'],
    'warning' => ['class' => 'alert-warning', 'icon' => 'bi-exclamation-circle-fill'],
    'info'    => ['class' => 'alert-info',    'icon' => 'bi-info-circle-fill'],
];
?>

<div class="flash-messages-container mb-4">
    <?php foreach ($flashTypes as $type => $meta): ?>
        <?php if (Session::hasFlash($type)): ?>
            <?php 
                $messages = Session::getFlash($type);
                $messages = is_array($messages) ? $messages : [$messages];
            ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert <?= $meta['class'] ?> alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi <?= $meta['icon'] ?> me-2 fs-5"></i>
                    <div><?= $msg ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

