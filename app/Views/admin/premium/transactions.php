<?= $this->extend('admin/layout'); ?>
<?= $this->section('content'); ?>

<div class="card">
    <form action="<?= $action; ?>" method="get" class="form-filter">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <?= view('admin/includes/_filter_rows'); ?>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end gap-3" data-kt-subscription-table-toolbar="base">
                    <?= view('admin/includes/_filters', [
                            'filterPageUrl' => $action,
                            'filters'       => [
                                    'transaction_type',
                                    'payment_method',
                                    'date_range',
                                    'search' => trans("search")
                            ]
                    ]); ?>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body pt-5">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="min-w-20px"><?= trans('id'); ?></th>
                    <th class="min-w-200px"><?= trans('item_description'); ?></th>
                    <th class="min-w-125px"><?= trans('user'); ?></th>
                    <th class="min-w-150px"><?= trans('payment_details'); ?></th>
                    <th class="min-w-100px"><?= trans('amount'); ?></th>
                    <th class="min-w-125px"><?= trans('date'); ?></th>
                    <th class="text-end min-w-70px"><?= trans('options'); ?></th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                <?php if (!empty($transactions)):
                    foreach ($transactions as $item):
                        $isMembership = ($item->item_type === 'subscription');
                        $typeBadgeClass = $isMembership ? 'badge-light-primary' : 'badge-light-info';
                        $detailsUrl = adminUrl("premium-membership/transaction-details/" . $item->id);
                        ?>
                        <tr>
                            <td><?= esc($item->id); ?></td>

                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="text-gray-900 fs-6">
                                        <?php if ($item->item_type === 'subscription'): ?>
                                            <a href="<?= adminUrl("premium-membership/plans?id=" . $item->item_id); ?>" class="text-gray-700 text-hover-primary fw-bold">
                                                <?= esc($item->item_title); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= adminUrl("posts?id=" . $item->item_id); ?>" class="text-gray-700 text-hover-primary fw-bold">
                                                <?= esc($item->item_title); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center mt-1">
                                        <span class="fs-7">#<?= esc($item->order_token); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center mt-1">
                                            <span class="badge <?= $typeBadgeClass; ?> px-2 py-1 fs-8">
                                                <?= trans($item->item_type ?? 'subscription', true); ?>
                                            </span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-column">
                                    <a href="<?= adminUrl("users/details/" . $item->user_id); ?>" class="text-gray-800 text-hover-primary fw-bold fs-6">
                                        <?= esc($item->user_username); ?>
                                    </a>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="fw-semibold text-gray-800 fs-7"><?= trans($item->payment_method, true); ?></span>
                                    <span class="text-muted fs-7" style="word-break: break-all;">
                                            TXN:
                                        <a href="<?= $detailsUrl; ?>" class="text-gray-600 text-hover-primary">
                                            <?= esc($item->gateway_transaction_id); ?>
                                        </a>
                                    </span>
                                </div>
                                <?php if (!empty($item->transfer_note)): ?>
                                    <div class="mt-2">
                                        <a href="javascript:void(0)" class="badge badge-light-info text-info border border-info border-dashed text-hover-primary" data-bs-toggle="modal" data-bs-target="#viewPaymentReportModal<?= $item->id; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                                <line x1="16" y1="13" x2="8" y2="13"/>
                                                <line x1="16" y1="17" x2="8" y2="17"/>
                                                <polyline points="10 9 9 9 8 9"/>
                                            </svg>
                                            <?= trans("payment_report"); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                $badgeClass = match ($item->status) {
                                    'succeeded' => 'badge-light-success',
                                    'pending' => 'badge-light-warning',
                                    'cancelled' => 'badge-light-danger',
                                    default => 'badge-light-secondary'
                                };
                                ?>
                                <div class="d-flex flex-column align-items-start gap-2">
                                        <span class="fw-bold text-gray-900 fs-6">
                                            <?= esc($item->total_amount); ?>&nbsp;<?= esc($item->currency); ?>
                                        </span>
                                    <span class="badge <?= $badgeClass; ?> px-2 py-1 fs-8 text-capitalize">
                                            <?= trans($item->status, true); ?>
                                        </span>
                                </div>
                            </td>

                            <td>
                                <span class="text-gray-700 fs-7"><?= formatDate($item->created_at); ?></span>
                            </td>

                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <?= trans('options'); ?><i class="ki-duotone ki-down fs-5 ms-2"></i>
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 menu-table-options py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="<?= $detailsUrl; ?>" class="menu-link px-3"><?= trans('details'); ?></a>
                                    </div>
                                    <?php if ($item->status == 'pending'): ?>
                                        <div class="menu-item px-3">
                                            <a href="javascript:void(0)" class="menu-link px-3 js-action-trigger"
                                               data-url="<?= $action; ?>"
                                               data-action="approve"
                                               data-id="<?= $item->id; ?>"
                                               data-message="<?= trans("confirm_action", "attr"); ?>"
                                               data-confirm="1">
                                                <?= trans('approve'); ?>
                                            </a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="javascript:void(0)" class="menu-link px-3 js-action-trigger"
                                               data-url="<?= $action; ?>"
                                               data-action="cancel"
                                               data-id="<?= $item->id; ?>"
                                               data-message="<?= trans("msg_cancel_order", "attr"); ?>"
                                               data-confirm="1">
                                                <?= trans('cancel'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="menu-item px-3">
                                        <a href="javascript:void(0)" class="menu-link px-3 text-danger js-action-trigger"
                                           data-url="<?= $action; ?>"
                                           data-action="delete"
                                           data-id="<?= $item->id; ?>"
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

        <?php if (empty($transactions)): ?>
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

<?php if (!empty($transactions)):
    foreach ($transactions as $item):
        if (!empty($item->transfer_note)):
            $transferData = safeDecode($item->transfer_note, true);
            $senderName = $transferData['sender_name'] ?? '';
            $transferNote = $transferData['note'] ?? ''; ?>

            <div class="modal fade" tabindex="-1" id="viewPaymentReportModal<?= $item->id; ?>">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title"><?= trans("payment_report"); ?></h3>
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                        </div>

                        <div class="modal-body">
                            <div class="mb-4">
                                <label class="text-muted fw-semibold fs-7 text-uppercase mb-1"><?= trans("sender_name"); ?></label>
                                <div class="fs-5 text-dark fw-bold user-select-all">
                                    <?= esc($senderName); ?>
                                </div>
                            </div>

                            <?php if (!empty($transferNote)): ?>
                                <div>
                                    <label class="text-muted fw-semibold fs-7 text-uppercase mb-1"><?= trans("transfer_note"); ?></label>
                                    <div class="p-3 bg-light rounded text-dark fs-6 user-select-all">
                                        <?= esc(nl2br($transferNote)); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?= trans("close"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    endforeach;
endif; ?>

<?= $this->endSection(); ?>
