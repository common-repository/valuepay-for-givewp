<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php
$collection_id  = give_get_meta( $form_id, '_valuepay_givewp_collection_id', true );
$mandate_id     = give_get_meta( $form_id, '_valuepay_givewp_mandate_id', true );
$frequency_type = give_get_meta( $form_id, '_valuepay_givewp_frequency_type', true );

$frequency_type_label = $frequency_type === 'weekly' ? __( 'Weekly', 'valuepay-givewp' ) : __( 'Monthly', 'valuepay-givewp' );

// Show payment type input in donation form only if recurring payment option is enabled
if ( $mandate_id ) :
    ?>
    <p id="valuepay-payment-type-wrap" class="form-row form-row-wide">
        <label class="give-label" for="valuepay-payment-type">
            <?php _e( 'Payment Type', 'valuepay-givewp' ); ?>
            <span class="give-required-indicator">*</span>
        </label>

        <select
            class="give-input required"
            name="valuepay_payment_type"
            placeholder="<?php _e( 'Payment Type', 'valuepay-givewp' ); ?>"
            id="valuepay-payment-type"
            required aria-required="true"
        >
            <?php
            $payment_type = isset( $_POST['valuepay_payment_type'] ) ? give_clean( $_POST['valuepay_payment_type'] ) : '';

            if ( $collection_id ) {
                echo '<option value="single ' . selected( 'single', $payment_type ) . '">' . esc_html__( 'One Time Payment', 'valuepay-givewp' ) . '</option>';
            }

            if ( $mandate_id ) {
                echo '<option value="recurring ' . selected( 'recurring', $payment_type ) . '">' . esc_html( sprintf( __( 'Recurring %s Payment', 'valuepay-givewp' ), $frequency_type_label ) ) . '</option>';
            }
            ?>
        </select>
    </p>
<?php endif; ?>
