<?php if (!empty($gateway) && $gateway->name_key === 'paystack' && !empty($paystackAuthorizationUrl)): ?>

    <a href="<?= $paystackAuthorizationUrl; ?>" class="btn-primary-custom">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
            <line x1="2" y1="10" x2="22" y2="10"></line>
        </svg>
        <?= trans("complete_payment"); ?>
    </a>

<?php endif; ?>