<div class="wcia-two-column">

    <div class="settings-wrap">

        <h2><?php esc_html_e( 'API Key Settings', 'woocommerce-clickstream-analytics' ); ?></h2>

        <form action="" method="POST" id="integration-form">

            <div class="wc-ia-form-group">

                <label for="access_key">Access Key (register on <a href="https:/portal.insensedata.com" target="_blank">Insense</a> to get an access key)</label><br>
                <input id="access_key" name="access_key" value="<?php echo $this->options['access_key']; ?>" required/>

            </div>

            <div class="wc-ia-form-group">

                <label for="privacy_policies_link">Privacy Policies Link</label><br>
                <input id="privacy_policies_link" name="privacy_policies_link" value="<?php echo $this->options['privacy_policies_link']; ?>" required/>

            </div>

            <div class="wc-ia-form-group">

                <input type="checkbox" name="show-cookie-consent"  <?php if($this->options['show-cookie-consent'] == 1): ?>checked<?php endif; ?>/>
                Show Cookie Consent ?

            </div>
            <br>

            <div class="wc-ia-form-group wcia-admin">

                <input type="checkbox" name="disabled"  <?php if($this->options['disabled'] == 1): ?>checked<?php endif; ?>/>
                Disable

            </div>
            <br>

            <div class="submit-area-">
                <?php wp_nonce_field( 'wcia-settings' ); ?>
                <input type="hidden" name="action" value="wcia_save_settings">

                <button class="button button-primary" id="wcia-submit"><?php esc_html_e( 'Save Changes', 'woocommerce-clickstream-analytics' ); ?></button>
            </div>
        </form>
    </div>

</div>
