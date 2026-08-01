<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<?php
$categoriesLangId = (int)inputGet('lang_id');
if (empty($categoriesLangId)) {
    $categoriesLangId = $activeLang->id;
}
?>

<div class="card card-categories">
    <form action="<?= adminUrl('categories'); ?>" method="get" class="form-filter">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="w-150px">
                    <select name="lang_id" class="form-select form-select-solid"  onchange="window.location.href = '<?= adminUrl('categories?lang_id='); ?>' + this.value;">
                        <?php foreach ($activeLanguages as $language): ?>
                            <option value="<?= $language->id; ?>" <?= $categoriesLangId == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card-toolbar gap-3">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4"><span class="path1"></span><span class="path2"></span></i>
                    <input type="text" name="q" value="<?= esc(inputGet('q')); ?>" class="form-control form-control-solid w-250px ps-12" placeholder="<?= trans("search", "attr"); ?>">
                </div>

                <div class="d-flex justify-content-end gap-3" data-kt-subscription-table-toolbar="base">
                    <a href="<?= adminUrl('categories/add'); ?>" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        <?= trans("add_category"); ?>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-5">

        <div id="category-root" class="list-group list-group-flush">
            <?= view('admin/category/_list', ['categories' => $categories, 'isSearchMode' => $isSearchMode]) ?>
        </div>

        <?php if (empty($categories)): ?>
            <p class="text-muted text-center mt-6">
                <?= trans("no_records_found"); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="d-flex justify-content-end align-items-center mt-5">
                <?= $pager->links(); ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    /* Dynamic subcategory loader */
    $(document).off('click', '.category-trigger').on('click', '.category-trigger', function (e) {
        e.stopPropagation();

        const $this = $(this);
        const id = $this.data('id');
        const langId = $this.data('lang-id');
        const isLoaded = $this.data('loaded');

        // Prevent multiple requests
        if ($this.hasClass('is-loading')) {
            return;
        }

        const $target = $(`#children-${id}`);
        const $arrow = $this.find('.arrow-icon');
        const $spinner = $this.find('.loading-spinner');

        // Toggle logic
        if ($target.is(':visible')) {
            $arrow.removeClass('rotate-90');
            $target.stop(true, true).slideUp(200);
            return;
        }

        $arrow.addClass('rotate-90');

        // Show already loaded content
        if (isLoaded) {
            $target.stop(true, true).slideDown(200);
            return;
        }

        // Lock and show spinner
        $this.addClass('is-loading');
        $spinner.show();

        $.ajax({
            url: `${VrConfig.adminUrl}/categories`,
            type: 'GET',
            data: {parent_id: id, lang_id: langId},
            dataType: 'json',
            success: function (response) {
                if (response && response.status === 1 && response.html !== undefined) {
                    $target.html(response.html).stop(true, true).slideDown(200);
                    $this.data('loaded', true);
                } else {
                    console.warn('Failed to load subcategories. Invalid response data.');
                    $arrow.removeClass('rotate-90');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Request Failed:', status, error);
                $arrow.removeClass('rotate-90');
            },
            complete: function () {
                $spinner.hide();
                $this.removeClass('is-loading');
            }
        });
    });
</script>
<?= $this->endSection(); ?>
