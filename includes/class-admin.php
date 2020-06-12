<?php

/**
 * The admin page handler class
 */
class WCIA_Admin {

    private $options = [];

    /**
     * Constructor for WCIA_Admin class
     */
    function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );

        $this->options = get_option('wcia_settings');
    }

    /**
     * Enqueue Script
     *
     * @return void
     */
    public function enqueue_scripts() {

        /**
         * All style goes here
         */
        wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', WCIA_FILE ), false, filemtime( WCIA_PATH . '/assets/css/style.css' ) );

        /**
         * All script goes here
         */
        wp_enqueue_script( 'wcia-admin', plugins_url( 'assets/js/admin.js', WCIA_FILE ), array( 'jquery', 'wp-util' ), filemtime( WCIA_PATH . '/assets/js/admin.js' ), true );

        wp_localize_script(
            'wcia-admin', 'wc_insense_analytics', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
            )
        );
    }

    /**
     * Add menu page
     *
     * @return void
     */
    public function admin_menu_page() {

        $menu_page      = apply_filters( 'wcia_menu_page', 'woocommerce' );
        $capability     = wcia_manage_cap();

        add_submenu_page( $menu_page, __( 'Insense Analytics', 'woocommerce-clickstream-analytics' ), __( 'Insense Analytics', 'woocommerce-clickstream-analytics' ), $capability, 'clickstream-analytics', array( $this, 'insense_analytics_template' ) );
    }

    /**
     * Conversion Tracking View Page
     *
     * @return void
     */
    public function insense_analytics_template() {
        include dirname(__FILE__) . '/views/admin.php';
    }

    /**
     * Get navigation tab
     *
     * @return array
     */
    public function wcia_get_tab() {

        $sections   = array(
            array(
                'id'    => 'settings',
                'title' => __( 'Settings', 'woocommerce-clickstream-analytics' ),
            ),
        );

        return apply_filters( 'wcia_nav_tab', $sections );
    }

    /**
     * Show navigation
     *
     * @return void
     */
    public function show_navigation() {
        $count  = count( $this->wcia_get_tab() );
        $tabs   = $this->wcia_get_tab();
        $active = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings';

        if ( $count == 0 ) {
            return;
        }

        $html = '<h2 class="nav-tab-wrapper">';
        foreach ( $tabs as $tab ) {
            $active_class   = ( $tab['id'] == $active ) ? 'nav-tab-active' : '';

            if ( !empty( $tab['id'] ) ) {
                $html  .= sprintf( '<a href="admin.php?page=clickstream-analytics&tab=%s" class="nav-tab %s">%s</a>', $tab['id'], $active_class, $tab['title'] );
            } else {
                $html  .= sprintf( '<a href="#" class="nav-tab disabled">%s</a>', $tab['title'] );
            }
        }

        $html   .= '</h2>';

        echo $html;
    }
}
