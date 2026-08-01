<?= $this->extend($viewsPath . '/layout'); ?>
<?= $this->section('content'); ?>

<?php

use App\Models\AuthorApplicationModel;

$authorStatus = 'default';
$reviewerFeedback = '';
if (!empty($authorApplication)) {
    $authorStatus = $authorApplication->status;
    $reviewerFeedback = $authorApplication->reviewer_feedback;
}

$isPending = $authorStatus === AuthorApplicationModel::STATUS_PENDING;
$isApproved = $authorStatus === AuthorApplicationModel::STATUS_APPROVED;
$isSoftReject = $authorStatus === AuthorApplicationModel::STATUS_SOFT_REJECT;
$isHardReject = $authorStatus === AuthorApplicationModel::STATUS_HARD_REJECT;

if (authCheck() && ((int)$user->role_id === 2 || isSuperAdmin())) {
    $isApproved = true;
}
?>

    <div class="author-app-wrapper position-relative overflow-hidden">
        <div class="app-container position-relative z-1 mx-auto px-4">

            <header class="app-hero text-center mx-auto">
                <div class="app-hero-badge">
                    <div class="app-badge-icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <span class="app-badge-text"><?= trans("author_program"); ?></span>
                </div>
                <h1 id="reward-heading" class="app-hero-title mb-4"><?= esc($page->title); ?></h1>
                <p class="app-hero-desc mb-0"><?= esc($page->description); ?></p>
            </header>

            <?= loadCommonView('partials/_alert'); ?>

            <?php if ($isApproved): ?>
                <div class="app-status-card text-center mx-auto">
                    <div class="app-status-icon-wrap success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="app-status-icon">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <h2 class="app-status-title fw-bold mb-3"><?= trans("you_are_author"); ?></h2>
                    <p class="app-status-desc mx-auto mb-0"><?= trans("you_are_author_exp"); ?></p>
                    <div class="mt-5 d-flex flex-wrap align-items-center justify-content-center gap-3">
                        <a href="<?= adminUrl(); ?>" class="app-status-back-btn d-inline-block px-4 py-2 fw-semibold text-decoration-none rounded-pill"><?= trans("go_to_dashboard"); ?></a>
                    </div>
                </div>

            <?php elseif ($isPending): ?>
                <div class="app-status-card text-center mx-auto">
                    <div class="app-status-icon-wrap pending rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="app-status-icon">
                            <path d="M5 22h14"/>
                            <path d="M5 2h14"/>
                            <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/>
                            <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>
                        </svg>
                    </div>
                    <h2 class="app-status-title fw-bold mb-3"><?= trans("submission_under_review"); ?></h2>
                    <p class="app-status-desc mx-auto mb-0"><?= trans("submission_under_review_exp"); ?></p>
                    <a href="<?= langBaseUrl(); ?>" class="app-status-back-btn d-inline-block mt-5 px-4 py-2 fw-semibold text-decoration-none rounded-pill"><?= trans("go_to_homepage"); ?></a>
                </div>

            <?php elseif ($isHardReject): ?>
                <div class="app-status-card text-center mx-auto">
                    <div class="app-status-icon-wrap danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="app-status-icon">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <h2 class="app-status-title fw-bold mb-3"><?= trans("submission_declined"); ?></h2>
                    <p class="app-status-desc mx-auto mb-0"><?= trans("submission_declined_exp"); ?></p>
                    <a href="<?= langBaseUrl(); ?>" class="app-status-back-btn d-inline-block mt-5 px-4 py-2 fw-semibold text-decoration-none rounded-pill"><?= trans("go_to_homepage"); ?></a>
                </div>

            <?php else: ?>

                <section class="app-timeline-section">
                    <div class="app-timeline-grid position-relative">
                        <div class="app-step text-center position-relative z-2">
                            <div class="app-step-icon-wrap mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="app-step-icon">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <polyline points="16 11 18 13 22 9"/>
                                </svg>
                            </div>
                            <h3 class="app-step-title fw-bold mb-3"><?= trans("author_app_submit_app_form"); ?></h3>
                            <p class="app-step-desc mx-auto mb-0"><?= trans("author_app_submit_app_form_exp"); ?></p>
                        </div>

                        <div class="app-step text-center position-relative z-2">
                            <div class="app-step-icon-wrap mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="app-step-icon">
                                    <path d="M12 19l7-7 3 3-7 7-3-3z"/>
                                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/>
                                    <path d="M2 2l7.586 7.586"/>
                                    <circle cx="11" cy="11" r="2"/>
                                </svg>
                            </div>
                            <h3 class="app-step-title fw-bold mb-3"><?= trans("author_app_publish_org_content"); ?></h3>
                            <p class="app-step-desc mx-auto mb-0"><?= trans("author_app_publish_org_content_exp"); ?></p>
                        </div>

                        <?php if ((int)$config->reward_system_status === 1): ?>
                            <div class="app-step text-center position-relative z-2">
                                <div class="app-step-icon-wrap mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="app-step-icon">
                                        <line x1="12" y1="1" x2="12" y2="23"/>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </div>
                                <h3 class="app-step-title fw-bold mb-3"><?= trans("author_app_earn_per_view"); ?></h3>
                                <p class="app-step-desc mx-auto mb-0"><?= trans("author_app_earn_per_view_exp"); ?></p>
                            </div>
                        <?php else: ?>
                            <div class="app-step text-center position-relative z-2">
                                <div class="app-step-icon-wrap mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 21v-6"/>
                                        <path d="M12 21V9"/>
                                        <path d="M19 21V3"/>
                                    </svg>
                                </div>
                                <h3 class="app-step-title fw-bold mb-3"><?= trans("author_app_track_your_perf"); ?></h3>
                                <p class="app-step-desc mx-auto mb-0"><?= trans("author_app_track_your_perf_exp"); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="app-split-grid align-items-start">

                    <div class="app-form-plate">
                        <?php if ($isSoftReject): ?>
                            <div class="app-soft-alert p-4 mb-5 d-flex align-items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="app-soft-alert-icon flex-shrink-0 mt-1">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                                </svg>
                                <div>
                                    <h3 class="app-soft-alert-title fw-bold mb-1"><?= trans("author_app_revision_required"); ?></h3>
                                    <p class="app-soft-alert-desc mb-3"><?= trans("author_app_revision_required_exp"); ?></p>
                                    <div class="app-admin-note p-3 rounded-end-3 fs-6 fst-italic">
                                        <strong><?= trans("reviewer_feedback"); ?>:</strong>&nbsp;<?= esc($reviewerFeedback); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="app-form-header">
                            <h2 class="app-form-title fw-bold"><?= trans("author_application_form"); ?></h2>
                            <p class="app-form-subtitle mb-0"><?= trans("author_app_submit_app_form_exp"); ?></p>
                        </div>

                        <form action="<?= base_url('become-author-post'); ?>" method="post" class="needs-validation" novalidate>
                            <?= csrf_field(); ?>

                            <div class="row g-4 mb-4">
                                <div class="col-sm-6">
                                    <div class="app-input-wrap grid-input">
                                        <label for="inputFirstName" class="form-label"><?= trans("first_name"); ?></label>
                                        <input type="text" name="first_name" class="form-control form-input" id="inputFirstName" value="<?= !empty($user) ? esc($user->first_name) : ''; ?>" placeholder="<?= trans("first_name", "attr"); ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="app-input-wrap grid-input">
                                        <label for="inputLastName" class="form-label"><?= trans("last_name"); ?></label>
                                        <input type="text" name="last_name" class="form-control form-input" id="inputLastName" value="<?= !empty($user) ? esc($user->last_name) : ''; ?>" placeholder="<?= trans("last_name", "attr"); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="app-input-wrap">
                                <label for="inputPortfolioUrl" class="form-label"><?= trans("author_app_portfolio_previous_work"); ?> (<?= trans("optional"); ?>)</label>
                                <input type="text" class="form-control form-input" id="inputPortfolioUrl" name="portfolio_url" placeholder="<?= trans("author_app_portfolio_previous_work_placeholder", "attr"); ?>"
                                       value="<?= esc($authorApplication->portfolio_url ?? ''); ?>">
                                <span class="app-hint"><?= trans("author_app_portfolio_previous_work_exp"); ?></span>
                            </div>

                            <div class="app-input-wrap">
                                <label for="inputAboutExpertise" class="form-label"><?= trans("author_app_about_you_expertise"); ?></label>
                                <textarea class="form-control form-input form-textarea" id="inputAboutExpertise" name="about_expertise" rows="5" placeholder="<?= trans("author_app_about_you_expertise_placeholder", "attr"); ?>" required><?= esc($authorApplication->about_expertise ?? ''); ?></textarea>
                            </div>

                            <?php if (authCheck()): ?>
                                <button type="submit" class="btn btn-custom app-submit-btn w-100">
                                    <?= trans("author_app_submit_request"); ?>
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-custom app-submit-btn w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <?= trans("author_app_submit_request"); ?>
                                </button>
                            <?php endif; ?>

                        </form>
                    </div>

                    <div class="app-guidelines-plate">
                        <h3 class="app-gl-title fw-bold mb-4"><?= trans("author_app_before_you_apply"); ?></h3>

                        <div class="app-gl-list d-flex flex-column gap-4">

                            <div class="app-gl-item d-flex align-items-start">
                                <div class="app-gl-dot rounded-circle flex-shrink-0 mt-1"></div>
                                <div class="app-gl-text-wrap flex-grow-1 ms-4">
                                    <span class="app-gl-heading d-block fw-bold mb-1"><?= trans("author_app_editorial_standards"); ?></span>
                                    <span class="app-gl-desc d-block"><?= trans("author_app_editorial_standards_exp"); ?></span>
                                </div>
                            </div>

                            <div class="app-gl-item d-flex align-items-start">
                                <div class="app-gl-dot rounded-circle flex-shrink-0 mt-1"></div>
                                <div class="app-gl-text-wrap flex-grow-1 ms-4">
                                    <span class="app-gl-heading d-block fw-bold mb-1"><?= trans("author_app_strict_authenticity"); ?></span>
                                    <span class="app-gl-desc d-block"><?= trans("author_app_strict_authenticity_exp"); ?></span>
                                </div>
                            </div>

                            <div class="app-gl-item d-flex align-items-start">
                                <div class="app-gl-dot rounded-circle flex-shrink-0 mt-1"></div>
                                <div class="app-gl-text-wrap flex-grow-1 ms-4">
                                    <span class="app-gl-heading d-block fw-bold mb-1"><?= trans("author_app_community_engagement"); ?></span>
                                    <span class="app-gl-desc d-block"><?= trans("author_app_community_engagement_exp"); ?></span>
                                </div>
                            </div>

                        </div>
                    </div>

                </section>
            <?php endif; ?>

            <?php if (!$isApproved && !$isPending && !$isHardReject && !empty($page->page_content)): ?>
                <div class="app-editor-content">
                    <?= $page->page_content; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

<?= $this->endSection(); ?>