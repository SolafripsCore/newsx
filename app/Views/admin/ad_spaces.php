<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

    <form action="<?= base_url("Admin/adSpacesPost"); ?>" method="post" class="form d-flex flex-column flex-xl-row kt-form mb-5 mb-xl-7 mb-xxl-10" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <input type="hidden" name="id" value="<?= $adSpace->id; ?>">

        <div class="col-12">
            <div class="card card-flush py-4">
                <div class="card-header min-h-30px"></div>
                <div class="card-body pt-5">

                    <div class="row fv-row">
                        <div class="col-md-6 mb-6">
                            <label class="required form-label"><?= trans("language"); ?></label>
                            <select name="lang_id" class="form-select" onchange="window.location.href = '<?= adminUrl("ad-spaces"); ?>'+'?lang='+this.value+'&ad_space=<?= strSlug($adSpaceKey); ?>';"
                                    data-control="select2" data-hide-search="true" data-placeholder="<?= trans("select_an_option", "attr"); ?>">
                                <?php foreach ($activeLanguages as $language): ?>
                                    <option value="<?= $language->id; ?>"<?= $langId == $language->id ? 'selected' : ''; ?>><?= esc($language->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-6">
                            <label class="required form-label"><?= trans("select_ad_spaces"); ?></label>
                            <select class="form-select" onchange="window.location.href = '<?= adminUrl("ad-spaces"); ?>'+'?lang=<?= (int)$langId; ?>&ad_space='+this.value;"
                                    data-control="select2" data-hide-search="true" data-placeholder="<?= trans("select_an_option", "attr"); ?>">
                                <?php foreach ($arrayAdSpaces as $key => $value): ?>
                                    <option value="<?= $key; ?>" <?= $key == $adSpace->ad_space ? 'selected' : ''; ?>><?= esc($value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?php if (!empty($arrayAdSpaces[$adSpace->ad_space])): ?>
                        <h4 class="mt-8 mb-6">
                            <?= $arrayAdSpaces[$adSpace->ad_space]; ?>
                            <?php if ($adSpace->ad_space == 'posts_top' || $adSpace->ad_space == 'posts_bottom'): ?>
                                <div class="text-gray-600 fs-7 mt-2"><?= trans("ad_space_posts_exp"); ?></div>
                            <?php endif; ?>
                        </h4>
                    <?php endif; ?>

                    <div class="fv-row bg-opacity-5 bg-gray-600 py-4 px-5 rounded-2 mb-8">
                        <div class="mb-6">
                            <span class="badge badge-lg badge-light-primary fw-bold my-2 fs-6"><?= trans("banner_desktop"); ?></span>
                            <small class="text-primary"><?= trans("banner_desktop_exp"); ?></small>
                        </div>

                        <div class="mb-6">
                            <label class="required form-label"><?= trans("ad_size"); ?></label>
                            <div class="d-flex align-items-center gap-3 mw-300px">
                                <input type="number" name="desktop_width" class="form-control" value="<?= $adSpace->desktop_width; ?>" min="1" max="5000" placeholder="<?= trans("width"); ?>" required>
                                <input type="number" name="desktop_height" class="form-control" value="<?= $adSpace->desktop_height; ?>" min="1" max="5000" placeholder="<?= trans("height"); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-xl-6 mb-6">
                                <label class="form-label"><?= trans("paste_ad_code"); ?></label>
                                <textarea class="form-control min-h-90px" name="ad_code_desktop" placeholder="<?= trans('paste_ad_code'); ?>"><?= $adSpace->ad_code_desktop; ?></textarea>
                            </div>
                            <div class="col-md-12 col-xl-6 mb-6">
                                <label class="form-label"><?= trans("upload_your_banner"); ?>&nbsp;<small class="text-gray-600">(<?= trans("create_ad_exp"); ?>)</small></label>
                                <input type="text" class="form-control mb-3" name="url_ad_code_desktop" placeholder="<?= trans('paste_ad_url'); ?>">
                                <div class="file-upload">
                                    <input type="file" name="file_ad_code_desktop" accept=".png, .jpg, .jpeg, .gif, .webp" class="d-none" data-upload-input>
                                    <button type="button" class="btn btn-sm btn-light-info" data-upload-button>
                                        <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i><?= trans("select_file"); ?>
                                    </button>
                                    <div class="mt-2 text-muted" data-upload-filename></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fv-row bg-opacity-5 bg-gray-600 py-4 px-5 rounded-2">
                        <div class="mb-6">
                            <span class="badge badge-lg badge-light-primary fw-bold my-2 fs-6"><?= trans("banner_mobile"); ?></span>
                            <small class="text-primary"><?= trans("banner_mobile_exp"); ?></small>
                        </div>

                        <div class="mb-6">
                            <label class="required form-label"><?= trans("ad_size"); ?></label>
                            <div class="d-flex align-items-center gap-3 mw-300px">
                                <input type="number" name="mobile_width" class="form-control" value="<?= $adSpace->mobile_width; ?>" min="1" max="5000" placeholder="<?= trans("width"); ?>" required>
                                <input type="number" name="mobile_height" class="form-control" value="<?= $adSpace->mobile_height; ?>" min="1" max="5000" placeholder="<?= trans("height"); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-xl-6 mb-6">
                                <label class="form-label"><?= trans("paste_ad_code"); ?></label>
                                <textarea class="form-control min-h-90px" name="ad_code_mobile" placeholder="<?= trans('paste_ad_code'); ?>"><?= $adSpace->ad_code_mobile; ?></textarea>
                            </div>
                            <div class="col-md-12 col-xl-6 mb-6">
                                <label class="form-label"><?= trans("upload_your_banner"); ?>&nbsp;<small class="text-gray-600">(<?= trans("create_ad_exp"); ?>)</small></label>
                                <input type="text" class="form-control mb-3" name="url_ad_code_mobile" placeholder="<?= trans('paste_ad_url'); ?>">
                                <div class="file-upload">
                                    <input type="file" name="file_ad_code_mobile" accept=".png, .jpg, .jpeg, .gif, .webp" class="d-none" data-upload-input>
                                    <button type="button" class="btn btn-sm btn-light-info" data-upload-button>
                                        <i class="ki-duotone ki-file-up fs-2"><span class="path1"></span><span class="path2"></span></i><?= trans("select_file"); ?>
                                    </button>
                                    <div class="mt-2 text-muted" data-upload-filename></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($activeTheme->theme != 'classic' && ($adSpace->ad_space == 'sidebar_1' || $adSpace->ad_space == 'sidebar_2')): ?>
                        <div class="fv-row mt-6">
                            <div class="mw-700px">
                                <label class="required form-label"><?= trans("where_to_display"); ?></label>
                                <select name="display_category_id" class="form-select" data-control="select2" data-hide-search="true" data-placeholder="<?= trans("select_an_option", "attr"); ?>" required>
                                    <option></option>
                                    <option value="latest_posts" <?= empty($adSpace->display_category_id) ? 'selected' : ''; ?>><?= trans("latest_posts"); ?></option>
                                    <?php if (!empty($categories)):
                                        foreach ($categories as $category):
                                            if ($category->block_type == 'block-2' || $category->block_type == 'block-3' || $category->block_type == 'block-4'): ?>
                                                <option value="<?= $category->id; ?>" <?= $adSpace->display_category_id == $category->id ? 'selected' : ''; ?>><?= esc($category->name); ?>&nbsp;(<small class="text-gray-600"><?= trans("category"); ?></small>)</option>
                                            <?php endif;
                                        endforeach;
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($adSpace->ad_space == 'in_article_1' || $adSpace->ad_space == 'in_article_2'): ?>
                        <div class="fv-row mt-6">
                            <div class="mw-700px">
                                <label class="required form-label"><?= trans("paragraph"); ?></label>&nbsp;<small class="text-gray-600">(<?= trans("ad_space_paragraph_exp"); ?>)</small>
                                <select name="paragraph_number" class="form-select" data-control="select2" data-hide-search="true" data-placeholder="<?= trans("select_an_option", "attr"); ?>">
                                    <option></option>
                                    <?php for ($i = 1; $i <= 50; $i++): ?>
                                        <option value="<?= $i; ?>" <?= $adSpace->paragraph_number == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end mt-6">
                        <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                            <span class="indicator-label"><?= trans("save_changes"); ?></span>
                            <span class="indicator-progress"><?= trans("submitting"); ?><span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <form action="<?= base_url("Admin/adsenseCodePost"); ?>" method="post" class="form d-flex flex-column flex-xl-row kt-form  mt-7">
        <?= csrf_field(); ?>

        <div class="col-md-12 col-lg-6">
            <div class="card card-flush py-4">
                <div class="card-header">
                    <div class="card-title">
                        <h2 class="d-flex flex-column">
                            <span><?= trans("adsense_activation_code"); ?></span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6"><?= trans("custom_footer_codes_exp"); ?></span>
                        </h2>
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="mb-6 fv-row">
                        <textarea name="adsense_activation_code" class="form-control min-h-90px" placeholder="<?= trans('adsense_activation_code'); ?>"><?= $config->adsense_activation_code; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                            <span class="indicator-label"><?= trans("save_changes"); ?></span>
                            <span class="indicator-progress"><?= trans("submitting"); ?><span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

<?= $this->endSection(); ?>