<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<p id="valuepay-bank-wrap" class="form-row form-row-wide">
    <label class="give-label" for="valuepay-bank">
        <?php _e( 'Payer Bank', 'valuepay-givewp' ); ?>
    </label>

    <select
        class="give-input"
        name="valuepay_bank"
        placeholder="<?php _e( 'Payer Bank', 'valuepay-givewp' ); ?>"
        id="valuepay-bank"
    >
        <?php
        $bank = isset( $_POST['valuepay_bank'] ) ? give_clean( $_POST['valuepay_bank'] ) : '';

        echo '<option>' . esc_html__( 'Select any bank', 'valuepay-givewp' ) . '</option>';

        if ( $banks ) {
            foreach ( $banks as $key => $value ) {
                echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $bank ) . '>' . esc_html( $value ) . '</option>';
            }
        }
        ?>
    </select>
</p>
