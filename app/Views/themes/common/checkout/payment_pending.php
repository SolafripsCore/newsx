<?= $this->extend($viewsPath . '/layout'); ?>
<?= $this->section('content'); ?>

    <div class="checkout-section">
        <main class="container py-5 d-flex flex-column align-items-center text-center flex-grow-1 justify-content-center">

            <div class="col-lg-7 col-md-9 w-100">

                <div class="success-header">
                    <div class="success-icon-wrapper bg-pending-soft text-pending border-pending">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="32" height="32">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <h1><?= trans("order_received"); ?></h1>
                    <p class="text-muted">
                        <?php if ($gateway === 'nowpayments'): ?>
                            <?= trans("order_successfully_created"); ?>
                        <?php else: ?>
                            <?= trans("payment_is_pending_exp"); ?>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="checkout-card success-card mb-5 mx-auto p-0 overflow-hidden text-center">

                    <div class="p-4 p-sm-5">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3 badge-icon bg-pending-soft text-pending">
                            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>

                        <h3 class="card-title justify-content-center mb-2 item-title">
                            <?= esc($itemName); ?>
                        </h3>

                        <div class="d-flex justify-content-center align-items-baseline mb-3">
                            <span class="price-display">
                                <?= priceFormatted($amount); ?>
                            </span>
                        </div>

                        <span class="status-badge d-inline-flex align-items-center gap-1 bg-pending-soft text-pending border-pending border">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?= trans("pending_payment"); ?>
                        </span>
                    </div>

                    <div class="dashed-divider"></div>

                    <div class="p-4 p-sm-5 text-start details-grid">

                        <?php if ($gateway === 'bank_transfer'): ?>
                            <h5 class="fw-bold mb-3 vr-text-main">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-2 text-primary align-text-bottom">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <?= trans("payment_instructions"); ?>
                            </h5>
                            <p class="text-muted mb-4 fs-6">
                                <?= trans("payment_instructions_exp"); ?>
                            </p>

                            <div class="alert mt-3 d-flex align-items-center rounded-3 mb-4 shadow-sm callout-primary">
                                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-primary me-3 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <strong class="callout-primary-text"><?= trans("transfer_note"); ?> (<?= trans("reference_code"); ?>):</strong>
                                    <div class="mt-2">
                                        <span class="badge bg-white text-primary border border-primary-subtle fs-5 py-2 px-3 user-select-all shadow-sm reference-badge">
                                            <?= esc($referenceId); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 p-md-4 rounded-3 bg-body-tertiary border border-secondary-subtle">
                                <div class="text-break editor-content lh-lg">
                                    <?= $gatewayData; ?>
                                </div>
                            </div>

                        <?php elseif ($gateway === 'nowpayments'): ?>
                            <div class="text-center py-4">
                                <h5 class="fw-bold mb-2 vr-text-main"><?= trans("waiting_for_blockchain_confirmation"); ?></h5>
                                <p class="text-muted mb-0 mx-auto max-w-400">
                                    <?= trans("waiting_for_blockchain_confirmation_exp"); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="d-flex gap-3 btn-action-group justify-content-center mx-auto mb-5 max-w-500">
                    <a href="<?= generateURL('account_settings', 'payment_history'); ?>" class="btn-primary-custom">
                        <?= trans("view_my_transactions"); ?>
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ms-2">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

            </div>
        </main>
    </div>

<?= $this->endSection(); ?>