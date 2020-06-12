<div class="wcia-two-column">

    <div class="settings-wrap">

        <h2><?php esc_html_e( 'API Key Settings', 'woocommerce-clickstream-analytics' ); ?></h2>

        <form action="" method="POST" id="integration-form">

            <div class="wc-ia-form-group">

                <label for="access_key">Access Key</label><br>
                <input id="access_key" name="access_key" value="<?php echo $this->options['access_key']; ?>" required/>

            </div>

            <div class="submit-area-">
                <?php wp_nonce_field( 'wcia-settings' ); ?>
                <input type="hidden" name="action" value="wcia_save_settings">

                <button class="button button-primary" id="wcia-submit"><?php esc_html_e( 'Save Changes', 'woocommerce-clickstream-analytics' ); ?></button>
            </div>
        </form>
    </div>

</div>
