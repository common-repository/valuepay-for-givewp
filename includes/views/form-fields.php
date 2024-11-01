<?php if ( !defined( 'ABSPATH' ) ) exit; ?>

<p id="valuepay-identity-type-wrap" class="form-row form-row-wide">
    <label class="give-label" for="valuepay-identity-type">
        <?php _e( 'Identity Type', 'valuepay-givewp' ); ?>
    </label>

    <select
        class="give-input"
        name="valuepay_identity_type"
        placeholder="<?php _e( 'Identity Type', 'valuepay-givewp' ); ?>"
        id="valuepay-identity-type"
    >
        <?php
        $identity_types = valuepay_givewp_get_identity_types();
        $identity_type = isset( $_POST['valuepay_identity_type'] ) ? give_clean( $_POST['valuepay_identity_type'] ) : '';

        foreach ( $identity_types as $key => $value ) {
            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $identity_type ) . '>' . esc_html( $value ) . '</option>';
        }
        ?>
    </select>
</p>

<p id="valuepay-identity-value-wrap" class="form-row form-row-wide">
    <label class="give-label" for="valuepay-identity-value">
        <?php _e( 'Identity Value', 'valuepay-givewp' ); ?>
    </label>

    <input
        class="give-input"
        type="text"
        name="valuepay_identity_value"
        placeholder="<?php _e( 'Identity Value', 'valuepay-givewp' ); ?>"
        id="valuepay-identity-value"
    >
        <?php echo isset( $_POST['valuepay_identity_value'] ) ? give_clean( $_POST['valuepay_identity_value'] ) : ''; ?>
    </input>
</p>

<p id="valuepay-telephone-wrap" class="form-row form-row-wide">
    <label class="give-label" for="valuepay-telephone">
        <?php _e( 'Telephone', 'valuepay-givewp' ); ?>
        <span class="give-required-indicator">*</span>
    </label>

    <input
        class="give-input required"
        type="text"
        name="valuepay_telephone"
        placeholder="<?php _e( 'Telephone', 'valuepay-givewp' ); ?>"
        id="valuepay-telephone"
        required aria-required="true"
    >
        <?php echo isset( $_POST['valuepay_telephone'] ) ? give_clean( $_POST['valuepay_telephone'] ) : ''; ?>
    </input>
</p>
