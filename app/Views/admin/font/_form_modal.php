<?php
$modalId = !empty($font) ? 'modalEditFont_' . $font->id : 'modalAddFont';

$selectedLangId = old('lang_id') ?: ($category->lang_id ?? null) ?: $activeLang->id;

$fontName = !empty($font) ? $font->font_name : '';
$fontType = !empty($font) ? $font->font_type : '';

$showFontUpload = true;
if (!empty($font) && (int)$font->is_default === 1) {
    $showFontUpload = false;
}

$isFileRequired = empty($font) ? true : false;
?>
<div class="modal fade" id="<?= $modalId; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <form action="<?= base_url("Admin/addFont"); ?>" method="post" class="kt-form" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= !empty($font) ? $font->id : ''; ?>">

                <div class="modal-header">
                    <h3 class="modal-title"><?= !empty($font) ? trans("update_font") : trans("add_font"); ?></h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>

                <div class="modal-body scroll-y mx-lg-5 my-7">
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">

                        <div class="fv-row mb-6">
                            <label class="fs-5 fw-bold form-label mb-2 required">
                                <?= trans("name"); ?>
                            </label>
                            <input type="text" name="font_name" value="<?= esc($fontName); ?>" class="form-control" placeholder="E.g: Open Sans" maxlength="255" required>
                        </div>

                        <div class="fv-row mb-6">
                            <label class="fs-5 fw-bold form-label mb-2 required">
                                <?= trans('font_type'); ?>
                            </label>

                            <select name="font_type" class="form-select" data-control="select2" data-hide-search="true" data-placeholder="<?= trans('select_an_option', 'attr'); ?>" required>
                                <option></option>
                                <?php foreach (getAppDefault('fontTypes') as $value => $label): ?>
                                    <option value="<?= esc($value); ?>" <?= $fontType === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($showFontUpload): ?>
                            <div class="fv-row mb-6">
                                <label class="fs-5 fw-bold form-label mb-3 <?= $isFileRequired ? 'required' : ''; ?>">
                                    <?= trans("font_file"); ?>&nbsp;(.woff2)
                                </label>

                                <div class="row g-5">
                                    <?php
                                    $uniqueSuffix = !empty($font) ? $font->id : 'new';
                                    $fontWeights = [
                                            '400' => ['title' => 'Regular'],
                                            '600' => ['title' => 'Semi-Bold'],
                                            '700' => ['title' => 'Bold']
                                    ];

                                    foreach ($fontWeights as $weight => $info):
                                        $isThisInputRequired = ($weight == '400' && $isFileRequired) ? 'required' : '';
                                        $inputId = "file_" . $weight . "_" . $uniqueSuffix;
                                        $badgeId = "badge_" . $weight . "_" . $uniqueSuffix; ?>
                                        <div class="col-md-4">
                                            <div class="card card-dashed h-100 bg-gray-50 border-gray-300 border-dashed p-6 text-center">

                                                <input type="file"
                                                       id="<?= $inputId; ?>"
                                                       name="font_file_<?= $weight ?>"
                                                       class="visually-hidden"
                                                       accept=".woff2, font/woff2, application/font-woff2"
                                                        <?= $isThisInputRequired; ?>
                                                       onchange="var badge = document.getElementById('<?= $badgeId; ?>');
                                                               if(this.files[0]) {
                                                               badge.textContent = this.files[0].name;
                                                               badge.classList.remove('d-none');
                                                               } else {
                                                               badge.classList.add('d-none');
                                                               }">

                                                <div class="mb-4">
                                                    <span class="text-gray-800 fw-bold fs-6 d-block">
                                                        <?= $weight ?>&nbsp;(<?= $info['title']; ?>)
                                                        <?= $isThisInputRequired ? '<span class="text-danger">*</span>' : ''; ?>
                                                    </span>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="<?= $inputId; ?>" class="btn btn-sm btn-secondary" style="cursor: pointer;">
                                                        <i class="bi bi-folder2-open me-1"></i><?= trans("select_file"); ?>
                                                    </label>
                                                </div>

                                                <div>
                                                    <span id="<?= $badgeId; ?>" class="badge badge-light-primary border border-primary d-none text-wrap text-break" style="max-width: 100%;"></span>
                                                </div>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="d-flex justify-content-end gap-5">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?= trans("close"); ?></button>
                        <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                            <span class="indicator-label"><?= !empty($font) ? trans("save_changes") : trans("add_font"); ?></span>
                            <span class="indicator-progress"><?= trans("submitting"); ?><span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>