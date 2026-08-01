<?= $this->extend($viewsPath . '/layout'); ?>
<?= $this->section('content'); ?>

    <section class="section section-page account-settings">
        <div class="container-xl pb-5">

            <?= loadCommonView('account/_breadcrumb'); ?>

            <div class="row">
                <div class="col-sm-12 col-lg-3 pe-lg-4 mb-5">
                    <?= loadCommonView('account/_sidebar'); ?>
                </div>

                <div class="col-sm-12 col-lg-9">
                    <?= loadCommonView('partials/_alert'); ?>

                    <div class="profile-form-title">
                        <h2><?= esc($title); ?></h2>
                        <p><?= trans("payment_history_exp"); ?></p>
                    </div>

                    <div class="account-table-container">
                        <div class="account-table-responsive">
                            <table class="account-table">
                                <thead>
                                <tr>
                                    <th><?= trans('item_description'); ?></th>
                                    <th><?= trans('amount'); ?></th>
                                    <th><?= trans('date'); ?></th>
                                    <th><?= trans('status'); ?></th>
                                    <th><?= trans('options'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($transactions)): ?>
                                    <?php foreach ($transactions as $item): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <span class="tx-title"><?= esc($item->item_title); ?></span>
                                                    <div class="tx-meta">
                                                        <span><?= trans($item->item_type ?? 'subscription', true); ?></span>
                                                        <span style="opacity: 0.5;">•</span>
                                                        <span><?= trans($item->payment_method, true); ?></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="tx-amount"><?= esc($item->total_amount); ?> <?= esc($item->currency); ?></span>
                                            </td>

                                            <td>
                                                <span class="tx-date"><?= formatDateClient($item->created_at); ?></span>
                                            </td>

                                            <td>
                                                <?php
                                                $badgeClass = match ($item->status) {
                                                    'succeeded' => 'success',
                                                    'pending' => 'pending',
                                                    'cancelled' => 'cancelled',
                                                    default => 'default'
                                                };
                                                ?>
                                                <span class="badge-status <?= $badgeClass; ?>">
                                                    <?= trans($item->status, true); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ($item->status === 'succeeded'): ?>
                                                    <a href="<?= generateURL('payment', 'invoice') . '/' . $item->id; ?>" class="btn-invoice-account" target="_blank" aria-label="<?= trans('invoice', 'attr'); ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/>
                                                            <path d="M14 2v5a1 1 0 0 0 1 1h5"/>
                                                            <path d="M10 9H8"/>
                                                            <path d="M16 13H8"/>
                                                            <path d="M16 17H8"/>
                                                        </svg>
                                                        <?= trans('invoice'); ?>
                                                    </a>
                                                <?php elseif ($item->status === 'pending'):
                                                    if ($item->payment_method == 'bank_transfer'):
                                                        if (empty($item->transfer_note)): ?>
                                                            <div class="d-block mb-2">
                                                                <button type="button" class="btn-invoice-account" data-bs-toggle="modal" data-bs-target="#reportPaymentModal<?= $item->id; ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/>
                                                                        <path d="m21.854 2.147-10.94 10.939"/>
                                                                    </svg>
                                                                    <?= trans("report_payment"); ?>
                                                                </button>
                                                            </div>
                                                            <div class="d-block">
                                                                <button type="button" class="btn-invoice-account btn-cancel-bank-transfer" data-id="<?= esc($item->id); ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="10"/>
                                                                        <path d="m15 9-6 6"/>
                                                                        <path d="m9 9 6 6"/>
                                                                    </svg>
                                                                    <?= trans("cancel"); ?>
                                                                </button>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="badge-status default"><?= trans("reported"); ?></span>
                                                            <div class="text-muted font-monospace user-select-all ps-1 mt-1 vr-fs-sm">
                                                                #<?= esc($item->gateway_transaction_id); ?>
                                                            </div>
                                                        <?php endif;
                                                    endif;
                                                endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <h5 class="fw-semibold text-gray-800 mt-0 mb-1"><?= trans("no_records_found"); ?></h5>
                                                <p class="text-muted small mb-0"><?= trans("msg_no_purchase_yet"); ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                        <div class="col-12 mt-5">
                            <?= $pager->links(); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

<?php if (!empty($transactions)): ?>
    <?php foreach ($transactions as $item): ?>

        <div class="custom-modal-wrapper">
            <div class="modal fade post-format-modal" id="reportPaymentModal<?= $item->id; ?>" tabindex="-1" aria-labelledby="addPostModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered max-w-600">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <h4 class="modal-title" id="addPostModalLabel">
                                <?= trans("report_payment"); ?>
                            </h4>
                            <p class="modal-subtitle">
                                <?= trans("report_payment_exp"); ?>
                            </p>
                        </div>

                        <form action="<?= base_url('report-bank-transfer-payment-post'); ?>" method="post" class="needs-validation" novalidate>
                            <?= csrf_field(); ?>
                            <input type="hidden" name="transaction_id" value="<?= esc($item->id); ?>">
                            <input type="hidden" name="back_url" value="<?= esc(current_url(true)); ?>">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label"><?= trans("reference_code"); ?></label>
                                    <div class="bg-body-tertiary rounded-3 p-3 mb-4">
                                        <span class="d-block text-muted small mb-1"><?= trans("your_reference_code"); ?></span>
                                        <span class="fs-5 fw-bold text-dark user-select-all" style="letter-spacing: 2px;">
                                            <?= esc($item->gateway_transaction_id); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required"><?= trans("sender_name"); ?></label>
                                    <input type="text" name="sender_name" class="form-control form-input" placeholder="<?= trans("sender_name", "attr"); ?>" maxlength="100" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?= trans("transfer_note"); ?> <span class="text-muted fw-normal">(<?= trans("optional"); ?>)</span></label>
                                    <textarea name="transfer_note" class="form-control form-input form-textarea" rows="2" placeholder="<?= trans("transfer_note", "attr"); ?>" maxlength="400"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                <button type="submit" class="btn btn-custom btn-block fw-semibold"><?= trans("submit"); ?></button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection(); ?>