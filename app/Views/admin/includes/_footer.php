<script>var hostUrl = "assets/";</script>
<script src="<?= base_url('assets/admin/plugins/global/plugins.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/scripts.bundle.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/main.js'); ?>"></script>

<style>
    .image-input-wrapper {
        background-image: url('<?= base_url("assets/admin/media/blank.svg"); ?>');
    }

    [data-bs-theme="dark"] .image-input-wrapper {
        background-image: url('<?= base_url("assets/admin/media/blank-dark.svg"); ?>');
    }
</style>

<?php if (empty($disableToastr)): ?>
    <?= view("admin/includes/_toastr"); ?>
<?php endif; ?>

<?= $this->renderSection('scripts'); ?>

</body>
</html>
