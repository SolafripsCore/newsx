<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<?php if (isSuperAdmin()):
    if (is_dir(FCPATH . 'install')): ?>
        <div class="mb-2">
            <?= renderAlert('danger', 'danger', 'Security Warning:', 'The <strong>"install"</strong> folder still exists on your server. Please delete it to secure your website.', 'w-800px mw-100', false); ?>
        </div>
    <?php endif;
    if (file_exists(FCPATH . 'update_database.php')):?>
        <div class="mb-6">
            <?= renderAlert('danger', 'danger', 'Security Warning:', 'The <strong>"update_database.php"</strong> file still exists on your server. Please delete it to secure your website.', 'w-800px mw-100', false); ?>
        </div>
    <?php endif;
endif; ?>

<div class="row gx-xl-7 mb-xl-7 gx-xxl-10 mb-xxl-10">

    <?php if (isSuperAdmin()): ?>
        <?= view("admin/home/_cards_admin"); ?>
    <?php else: ?>
        <?= view("admin/home/_cards_author"); ?>
    <?php endif; ?>

    <div class="col-lg-12 col-xl-12 <?= isSuperAdmin() ? 'col-xxl-6' : 'col-xxl-8'; ?> mb-5 mb-xl-0">
        <?= view("admin/home/_earnings_and_stats"); ?>
    </div>

</div>

<?php if (isSuperAdmin()): ?>
    <div class="row gx-xl-7 gx-xxl-10">
        <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-7 mb-xxl-10">
            <?= view("admin/home/_pending_posts"); ?>
        </div>
        <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-7 mb-xxl-10">
            <?= view("admin/home/_pending_comments"); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isSuperAdmin() && ((int)$premiumMembership->status === 1 || isPostFormatActive('event', $config))): ?>
    <div class="row gx-xl-7 mb-xl-7 gx-xxl-10 mb-xxl-10">
        <?php if ((int)$premiumMembership->status === 1): ?>
            <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-7 mb-xxl-10">
                <?= view("admin/home/_latest_transactions"); ?>
            </div>
        <?php endif; ?>
        <?php if (isPostFormatActive('event', $config)): ?>
            <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-0">
                <?= view("admin/home/_upcoming_events"); ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?= view("admin/home/_modal_earnings"); ?>

<?= $this->endSection(); ?>

