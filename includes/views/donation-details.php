<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="valuepay-payment-details postbox">
    <h3 class="hndle"><?php esc_html_e( 'ValuePay Payment Details', 'valuepay-givewp' ); ?></h3>

    <div class="inside">
        <div class="column-container">
            <div class="column">
                <p>
                    <strong><?php esc_html_e( 'Bill ID: ', 'valuepay-givewp' ); ?></strong><br>
                    <?php echo esc_html( give_get_meta( $payment_id, '_give_payment_transaction_id', true ) ?: '–' ); ?>
                </p>
            </div>
            <div class="column">
                <p>
                    <strong><?php esc_html_e( 'Telephone: ', 'valuepay-givewp' ); ?></strong><br>
                    <?php echo esc_html( give_get_payment_meta( $payment_id, 'valuepay_telephone', true ) ?: '–' ); ?>
                </p>
            </div>

        </div>
    </div>
</div>
