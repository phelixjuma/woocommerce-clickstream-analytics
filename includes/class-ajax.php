<?php

/**
 * Ajax handler class
 */
class WCIA_Ajax {

    /**
     * WC Conversion Tracking Ajax Class Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_wcia_save_settings', array( $this, 'wcia_save_settings' ) );
    }

    /**
     * Save integration settings
     *
     * @return void
     */
    public function wcia_save_settings() {
        if ( ! current_user_can( wcia_manage_cap() ) ) {
            return;
        }
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'wcia-settings' ) ) {
            die( 'wcia-settings !' );
        }

        if ( ! isset( $_POST['access_key'] ) ) {
            wp_send_json_error();
        }

        if ( empty( $_POST['access_key'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'Please set a value for access key!', 'woocommerce-clickstream-analytics' )
            ) );
        }

        $data['access_key'] = $_POST['access_key'];
        $data['disabled'] = 0;
        $data['show-cookie-consent'] = 0;
        $data['privacy_policies_link'] = "";
        if ( isset($_POST['disabled']) && !empty($_POST['disabled'])) {
            $data['disabled'] = 1;
        }
        if ( isset($_POST['show-cookie-consent']) && !empty($_POST['show-cookie-consent'])) {
            $data['show-cookie-consent'] = 1;
        }
        if ( isset($_POST['privacy_policies_link']) && !empty($_POST['privacy_policies_link'])) {
            $data['privacy_policies_link'] = $_POST['privacy_policies_link'];
        }

        update_option( 'wcia_settings', stripslashes_deep( $data ) );

        wp_send_json_success( array(
            'message' => __( 'Settings has been saved successfully!', 'woocommerce-clickstream-analytics' )
        ) );

    }
}
