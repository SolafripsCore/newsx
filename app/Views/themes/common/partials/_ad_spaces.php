<?php
$showAds = true;

// Check if user is logged in and has an ad-free subscription
if (authCheck() && !empty(user()->sub_is_ad_free)) {
    $showAds = false;
}

// Proceed to display ads if the user does not have an ad-free plan
if ($showAds && !empty($adSpace)):
    $adCodes = null;

    // Find the matching ad space for the active language
    if (!empty($adSpaces)) {
        foreach ($adSpaces as $item) {
            if ($item->lang_id == $activeLang->id && $item->ad_space === $adSpace) {
                $adCodes = $item;
                break;
            }
        }
    }

    if (!empty($adCodes)):
        if (trim($adCodes->ad_code_desktop ?? '') !== ''): ?>
            <div class="container container-bn<?= $adSpace === 'header' ? ' container-bn-header' : ''; ?> container-bn-ds<?= isset($class) ? ' ' . $class : ''; ?>">
                <div class="row">
                    <div class="bn-content<?= ($adSpace === 'sidebar_1' || $adSpace === 'sidebar_2') ? ' bn-sidebar-content' : ''; ?>">
                        <div class="bn-inner bn-ds-<?= $adCodes->id; ?>">
                            <?= trim($adCodes->ad_code_desktop); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;

        if (trim($adCodes->ad_code_mobile ?? '') !== ''): ?>
            <div class="container container-bn container-bn-mb<?= isset($class) ? ' ' . $class : ''; ?>">
                <div class="row">
                    <div class="bn-content">
                        <div class="bn-inner bn-mb-<?= $adCodes->id; ?>">
                            <?= trim($adCodes->ad_code_mobile); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    endif;
endif; ?>