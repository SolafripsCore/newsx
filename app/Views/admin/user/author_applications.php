<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <form action="<?= $action; ?>" method="get" class="form-filter">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <?= view('admin/includes/_filter_rows'); ?>
            </div>
        </div>
    </form>

    <div class="card-body pt-5">
        <div class="table-responsive">
            <table id="tableApplications" class="table align-middle table-row-dashed table-bulk fs-6 gy-5">
                <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-20px"><?= trans('id'); ?></th>
                    <th class="min-w-125px"><?= trans('user'); ?></th>
                    <th class="min-w-100px"><?= trans('status'); ?></th>
                    <th class="min-w-100px"><?= trans('date'); ?></th>
                    <th class="text-end min-w-70px"><?= trans('options'); ?></th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                <?php if (!empty($applications)):
                    foreach ($applications as $ap):
                        $profileUrl = generateProfileURL($ap->slug ?? $ap->username); ?>
                        <tr>
                            <td><?= esc($ap->id); ?></td>

                            <td class="d-flex">
                                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                    <a href="<?= $profileUrl; ?>" target="_blank">
                                        <div class="symbol-label">
                                            <img src="<?= getUserAvatar($ap->avatar, $ap->storage_avatar ?? ''); ?>" alt="" class="w-100"/>
                                        </div>
                                    </a>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="<?= $profileUrl; ?>" class="text-gray-800 text-hover-primary mb-1" target="_blank"><?= esc($ap->username); ?></a>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span><?= esc($ap->email); ?></span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <?php if ($ap->status === 'pending'): ?>
                                    <span class="badge badge-light-warning fw-bold px-4 py-2"><?= trans("pending"); ?></span>
                                <?php elseif ($ap->status === 'approved'): ?>
                                    <span class="badge badge-light-success fw-bold px-4 py-2"><?= trans("approved"); ?></span>
                                <?php elseif ($ap->status === 'soft_reject'): ?>
                                    <span class="badge badge-light-info fw-bold px-4 py-2"><?= trans("soft_reject"); ?>&nbsp;(<?= trans("request_changes"); ?>)</span>
                                <?php else: ?>
                                    <span class="badge badge-light-danger fw-bold px-4 py-2"><?= trans("rejected"); ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= formatDate($ap->created_at); ?>
                                <?php if (!empty($ap->updated_at) && strtotime($ap->updated_at) > 0): ?>
                                    <div class="text-muted fs-8 mt-1">
                                        <?= trans('updated'); ?>: <?= timeAgo($ap->updated_at); ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="text-end text-nowrap">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <?= trans('options'); ?> <i class="ki-outline ki-down fs-5 ms-1"></i>
                                </a>

                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-175px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#modalAppDetails_<?= $ap->id; ?>"><?= trans('details'); ?></a>
                                    </div>

                                    <div class="menu-item px-3">
                                        <a href="<?= adminUrl('users/details/' . esc($ap->user_id)); ?>" class="menu-link px-3"><?= trans('user_details'); ?></a>
                                    </div>

                                    <div class="menu-item px-3">
                                        <a href="javascript:void(0)" class="menu-link px-3 text-danger js-action-trigger"
                                           data-url="<?= esc($action); ?>"
                                           data-action="delete"
                                           data-id="<?= $ap->id; ?>"
                                           data-message="<?= trans("confirm_delete", "attr"); ?>"
                                           data-confirm="1">
                                            <?= trans('delete'); ?>
                                        </a>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($applications)): ?>
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

<?php if (!empty($applications)):
    foreach ($applications as $ap): ?>

        <div class="modal fade" id="modalAppDetails_<?= $ap->id; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content">
                    <form action="<?= $action; ?>" method="post" class="js-app-form">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="action" value="update_request">
                        <input type="hidden" name="id" value="<?= $ap->id; ?>">
                        <input type="hidden" name="user_id" value="<?= $ap->user_id; ?>">

                        <div class="modal-header">
                            <h3 class="modal-title"><?= trans("details"); ?></h3>
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                        </div>

                        <div class="modal-body scroll-y mx-lg-5 my-7">
                            <div class="d-flex flex-column scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">

                                <?php if ($ap->status !== 'approved'): ?>
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-bold form-label mb-2"><span class="required"><?= trans("status"); ?></span></label>
                                        <select name="status" class="form-select fw-bold js-status-select" data-target="<?= $ap->id; ?>" required>
                                            <option value="pending" <?= $ap->status === 'pending' ? 'selected' : ''; ?>><?= trans("pending"); ?></option>
                                            <option value="approved" <?= $ap->status === 'approved' ? 'selected' : ''; ?>><?= trans("approved"); ?></option>
                                            <option value="soft_reject" <?= $ap->status === 'soft_reject' ? 'selected' : ''; ?>><?= trans("soft_reject"); ?>&nbsp;(<?= trans("request_changes"); ?>)</option>
                                            <option value="hard_reject" <?= $ap->status === 'hard_reject' ? 'selected' : ''; ?>><?= trans("rejected"); ?></option>
                                        </select>
                                    </div>

                                    <?php if ((int)$config->reward_system_status === 1): ?>
                                        <div class="fv-row mb-7" id="rewardWrapper_<?= $ap->id; ?>">
                                            <div class="p-4 rounded border border-dashed border-success">
                                                <div class="d-flex flex-stack">
                                                    <div class="me-5">
                                                        <label class="fs-6 fw-semibold"><?= trans("enable_reward_system"); ?></label>
                                                    </div>
                                                    <?= formSwitch('reward_system', 0); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="fv-row mb-7" id="feedbackWrapper_<?= $ap->id; ?>" style="<?= $ap->status === 'soft_reject' ? 'display:block;' : 'display:none;'; ?>">
                                        <label class="fs-6 fw-bold form-label mb-2"><?= trans("reviewer_feedback"); ?></label>
                                        <textarea class="form-control" name="reviewer_feedback" rows="4" placeholder="<?= trans("reviewer_feedback", "attr"); ?>"><?= esc($ap->reviewer_feedback); ?></textarea>
                                    </div>

                                    <div class="separator border-2 border-dashed mb-7"></div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-6 mb-7">
                                        <label class="fs-6 fw-bold form-label mb-2"><?= trans("username"); ?></label>
                                        <div class="p-4 bg-light rounded text-break">
                                            <?= esc($ap->username); ?>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-7">
                                        <label class="fs-6 fw-bold form-label mb-2"><?= trans("email"); ?></label>
                                        <div class="p-4 bg-light rounded text-break">
                                            <?= esc($ap->email); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-7">
                                        <label class="fs-6 fw-bold form-label mb-2"><?= trans("first_name"); ?></label>
                                        <div class="p-4 bg-light rounded text-break">
                                            <?= esc($ap->first_name); ?>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-7">
                                        <label class="fs-6 fw-bold form-label mb-2"><?= trans("last_name"); ?></label>
                                        <div class="p-4 bg-light rounded text-break">
                                            <?= esc($ap->last_name); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-7">
                                    <label class="fs-6 fw-bold form-label mb-2"><?= trans("author_app_portfolio_previous_work"); ?></label>
                                    <div class="p-4 bg-light rounded text-break">
                                        <?= esc($ap->portfolio_url); ?>
                                    </div>
                                </div>

                                <div class="mb-7">
                                    <label class="fs-6 fw-bold form-label mb-2"><?= trans("author_app_about_you_expertise"); ?></label>
                                    <div class="p-4 bg-light rounded text-gray-700 fs-6 text-break">
                                        <?= esc($ap->about_expertise); ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?= trans("close"); ?></button>
                                <?php if ($ap->status !== 'approved'): ?>
                                    <button type="submit" class="btn btn-primary" data-kt-indicator="off">
                                        <span class="indicator-label"><?= trans("save_changes"); ?></span>
                                        <span class="indicator-progress"><?= trans("submitting"); ?><span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php endforeach;
endif; ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // Handle visibility of feedback and reward wrappers
        const statusSelects = document.querySelectorAll('.js-status-select');

        statusSelects.forEach(function (select) {

            // Helper function to handle wrapper visibility
            const toggleVisibility = (status, targetId) => {
                const feedbackWrapper = document.getElementById('feedbackWrapper_' + targetId);
                const rewardWrapper = document.getElementById('rewardWrapper_' + targetId);

                // Handle Soft Reject Feedback
                if (feedbackWrapper) {
                    feedbackWrapper.style.display = (status === 'soft_reject' || status === 'hard_reject') ? 'block' : 'none';
                }

                // Handle Approved Reward System
                if (rewardWrapper) {
                    rewardWrapper.style.display = (status === 'approved') ? 'block' : 'none';
                }
            };

            // Initialize visibility based on the current default value of the select
            const initialTargetId = select.getAttribute('data-target');
            toggleVisibility(select.value, initialTargetId);

            // Listen for changes
            select.addEventListener('change', function () {
                const targetId = this.getAttribute('data-target');
                toggleVisibility(this.value, targetId);
            });
        });

        // Handle SweetAlert confirmation and submit state
        const appForms = document.querySelectorAll('.js-app-form');

        appForms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const statusSelect = form.querySelector('.js-status-select');
                const selectedStatus = statusSelect.value;

                let swalMsg = <?= json_encode(trans('confirm_action')); ?>;
                let swalMsgUndone = <?= json_encode(trans('action_cannot_be_undone')); ?>;

                // Append irreversible action warning
                if (selectedStatus === 'approved' || selectedStatus === 'hard_reject') {
                    swalMsg += '\n\n' + swalMsgUndone;
                }

                // Show SweetAlert confirmation
                Swal.fire(window.swalOptions(swalMsg)).then(result => {
                    if (result.isConfirmed) {
                        submitBtn.setAttribute('data-kt-indicator', 'on');
                        submitBtn.disabled = true;
                        form.submit();
                    }
                });
            });
        });

    });
</script>
<?= $this->endSection(); ?>
