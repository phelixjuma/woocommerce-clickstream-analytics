<?php

/*
Plugin Name: Woocommerce Clickstream Analytics
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A clickstream data tool that implements the  <a href="https://github.com/kuza-lab/ecommerce-tracking-analytics" target="_blank">Insense E-Commerce Clickstream Analytics</a> SDK. Insense Clickstream Analytics captures clickstream data from your site and the clickstream analytics reports can be accessed via your account on <a href="https://portal.insensedata.com" target="_blank">Insense</a> .
Version: 1.0
Author: Phelix Juma
Author URI: https://insensedata.com
License: GPL2
WC requires at least: 3.0
WC tested up to: 4.2.0
*/

/**
 * Copyright (c) 2020 Phelix Juma (email: jumaphelix@insensedata.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    if (!class_exists('WC_Clickstream_Analytics')) {

        class WC_Clickstream_Analytics {

            /**
             * Plugin version
             *
             * @var string
             */
            public $version = '1.0.0';

            /**
             * Holds various class instances
             *
             * @var array
             */
            private $container = array();

            /**
             * Constructor
             *
             * Sets up all the appropriate hooks and actions
             * within our plugin.
             */
            public function __construct() {
                $this->define_constants();
                $this->init_hooks();
                $this->includes();
                $this->init_classes();

                register_activation_hook( __FILE__, array( $this, 'activate' ) );
                do_action( 'wcia_loaded' );
            }

            /**
             * Magic getter to bypass referencing plugin.
             *
             * @param $prop
             *
             * @return mixed
             */
            public function __get( $prop ) {
                if ( array_key_exists( $prop, $this->container ) ) {
                    return $this->container[ $prop ];
                }

                return $this->{$prop};
            }

            /**
             * Magic isset to bypass referencing plugin.
             *
             * @param $prop
             *
             * @return mixed
             */
            public function __isset( $prop ) {
                return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
            }

            /**
             * Initializes the insense analytics class
             *
             */
            public static function init() {
                static $instance = false;

                if ( ! $instance ) {
                    $instance = new WC_Clickstream_Analytics();
                }

                return $instance;
            }

            /**
             * Include required files
             *
             * @return void
             */
            public function includes() {
                require_once WCIA_INCLUDES . '/class-event-dispatcher.php';
                require_once WCIA_INCLUDES . '/class-ajax.php';
                require_once WCIA_INCLUDES . '/class-admin.php';
            }

            /**
             * Define the constants
             *
             * @return void
             */
            public function define_constants() {
                define( 'WCIA_VERSION', $this->version );
                define( 'WCIA_FILE', __FILE__ );
                define( 'WCIA_PATH', dirname( WCIA_FILE ) );
                define( 'WCIA_INCLUDES', WCIA_PATH . '/includes' );
                define( 'WCIA_URL', plugins_url( '', WCIA_FILE ) );
                define( 'WCIA_ASSETS', WCIA_URL . '/assets' );
            }

            /**
             * Plugin activation routeis
             *
             *
             * @return void
             */
            public function activate() {
                $installed = get_option( 'wcia_installed' );

                if ( ! $installed ) {
                    update_option( 'wcia_installed', time() );
                }

                update_option( 'wcia_version', WCIA_VERSION );
            }

            /**
             * Initialize the hooks
             *
             * @return void
             */
            public function init_hooks() {

                add_action( 'init', array( $this, 'localization_setup' ) );

                add_action( 'admin_notices', array( $this, 'check_woocommerce_exist' ) );
                add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

                $this->init_tracker();
            }

            /**
             * Instantiate the required classes
             *
             * @return void
             */
            public function init_classes() {
                $this->container['ajax']                = new WCIA_Ajax();
                $this->container['event_dispatcher']    = new WCIA_Event_Dispatcher();
                $this->container['admin']               = new WCIA_Admin();
            }

            /**
             * Initialize plugin for localization
             *
             * @uses load_plugin_textdomain()
             */
            public function localization_setup() {
                load_plugin_textdomain( 'woocommerce-clickstream-analytics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            }

            /**
             * Plugin action links
             *
             * @param  array $links
             *
             * @return array
             */
            function plugin_action_links( $links ) {

                $links[] = '<a href="' . admin_url( 'admin.php?page=clickstream-analytics' ) . '">' . __( 'Settings', 'woocommerce-clickstream-analytics' ) . '</a>';

                return $links;
            }

            /**
             * Check Woocommerce exist
             *
             * @return void
             */
            public function check_woocommerce_exist() {
                if ( ! function_exists( 'WC' ) ) {
                    ?>
                    <div class="error notice is-dismissible">
                        <p><?php echo __( '<b>Woocommerce Clickstream Analytics</b> requires <a target="_blank" href="https://wordpress.org/plugins/woocommerce/">Woocommerce</a>', 'woocommerce-clickstream-analytics' ); ?></p>
                    </div>
                    <?php
                }
            }

        }

        /**
         * Instantiates the class
         *
         * @return bool|WC_Clickstream_Analytics
         */
        function wcia_init() {
            return WC_Clickstream_Analytics::init();
        }

        // instantiate the class
        wcia_init();

        /**
         * Manage Capability
         *
         * @return void
         */
        function wcia_manage_cap() {
            return apply_filters( 'wcia_capability', 'manage_options' );
        }

    }
}
