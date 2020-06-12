<?php

/**
 * Event Manager
 */
class WCIA_Event_Dispatcher {


    /**
     * Constructor for WCIA_Event_Dispatcher class
     */
    public function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        $this->setActions();
    }

    /**
     * Enqueue Script
     *
     * @return void
     */
    public function enqueue_scripts() {

        /**
         * All script goes here
         */
        wp_enqueue_script( 'wcia-axios', plugins_url( 'assets/js/axios.min.js', WCIA_FILE ), filemtime( WCIA_PATH . '/assets/js/axios.min.js' ) );
        wp_enqueue_script( 'wcia-analytics', plugins_url( 'assets/js/analytics.min.js', WCIA_FILE ), filemtime( WCIA_PATH . '/assets/js/analytics.min.js' ) );

        wp_enqueue_script( 'wcia-analytics-init', plugins_url( 'assets/js/init.js', WCIA_FILE ), array('jquery') ,filemtime( WCIA_PATH . '/assets/js/init.js' ) );


        wp_localize_script( 'wcia-analytics-init', 'data',
            array(
                'current_user' => wp_get_current_user(),
                'access_key'    => get_option('wcia_settings')['access_key']
            )
        );
    }

    private function setActions() {
        //==  user info events
        // registered
        add_action( 'woocommerce_registration_redirect', array( $this, 'registered' ) );
        // logged in
        add_filter( 'woocommerce_login_redirect', array( $this, 'logged_in' ) );
        add_action( 'woocommerce_after_customer_login_form', array( $this, 'logged_in' ) );
        // logged out

        // == browsing
        // product searched
        add_action( 'pre_get_posts', array( $this, 'product_searched' ) );
        // product list viewed
        add_action( 'woocommerce_after_shop_loop', array( $this, 'product_list_viewed' ) ); // category view
        // product list filtered

        // == promotions
        // promotion viewed
        //promotion clicked

        // ==ordering process
        // product clicked

        // product viewed           -> Key Event 1
        add_action( 'woocommerce_after_single_product', array( $this, 'product_viewed' ) );

        // product added            -> Key Event 2
        // add_action( 'woocommerce_add_to_cart', array( $this, 'product_added_to_cart' ), 9999, 4 );
        add_action( 'wp_footer', array( $this, 'product_added_to_cart' ) );

        // product removed
        // add_action( 'woocommerce_cart_item_removed', array( $this, 'product_removed_from_cart' ), 9999, 4 );
        // cart viewed
        add_action( 'woocommerce_after_cart', array( $this, 'cart_viewed' ));

        // checkout started         -> Key Event 3
        add_action( 'woocommerce_before_checkout_form', array( $this, 'checkout_started' ) );

        // checkout step viewed
        // checkout step completed
        //add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'checkout_billing_step_started' ) ); // billing step
        //add_action( 'woocommerce_after_checkout_registration_form', array( $this, 'checkout_registration_step_started' ) ); // registration step
        //add_action( 'woocommerce_after_checkout_shipping_form', array( $this, 'checkout_shipping_step_started' ) ); // shipping step
        //add_action( 'woocommerce_after_customer_login_form', array( $this, 'checkout_login_step_started' ) ); // shipping step
        // payment info entered
        add_action( 'woocommerce_after_account_payment_methods', array($this, 'payment_info_entered'));
        // order updated
        // add_action( '', array( $this, 'order_updated' ) );

        // order completed          -> Key Event 4
        // add_action( 'woocommerce_thankyou', array( $this, 'order_completed' ), 10, 1 );
        add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'order_completed' ), 10,1);

        // order cancelled
        add_action( 'woocommerce_cancelled_order', array( $this, 'order_cancelled' ) );
        // order refunded
        add_action( 'woocommerce_order_fully_refunded', array( $this, 'order_refunded' ) );

        // == coupons
        // coupon entered
        // add_action( 'woocommerce_cart_coupon', array( $this, 'coupon_entered' ) );
        //coupon applied
        add_action( 'woocommerce_applied_coupon', array( $this, 'coupon_applied' ) );
        // coupon denied
        // coupon removed
        add_action( 'woocommerce_removed_coupon', array( $this, 'coupon_removed' ) );

        // == wishlisting
        // product added to wishlist
        // add_filter( 'yith_wcwl_added_to_wishlist', array( $this, 'product_added_to_wishlist' ) );
        add_action( 'woocommerce_wishlist_add_item', array( $this, 'product_added_to_wishlist' ) );
        // product removed from wish list
        // wishlist product added to cart

        // == sharing
        // product shared
        // add_action( 'woocommerce_share', array( $this, 'product_shared' ) );
        // cart shared

        // == Reviewing
        // product reviewed
        // add_action( 'woocommerce_checkout_order_review', array( $this, 'order_reviewed' ) );

        // == Page Loading
        // page loaded
        // add_action( 'template_redirect', array( $this, 'page_loaded' ) );
        // add_action( 'wp_head', array( $this, 'page_loaded' ) );

        // == Error
        // error occurred
        add_action( 'woocommerce_cart_has_errors', array( $this, 'error_occurred' ) );
        add_action( 'woocommerce_cart_has_errors', array( $this, 'error_occurred' ) );
        add_action( 'woocommerce_login_failed', array( $this, 'error_occurred' ) );
    }

    public function registered() {
    }

    public function logged_in() {
    }

    public function product_searched($query) {
        if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
            // print_r($query->query['s']);
            ?>
            <script>
                idt_analytics.onProductSearched("query");
            </script>
            <?php
        }
    }

    public function product_list_viewed() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onProductListViewed();
            </script>
            <?php
        }
    }

    public function product_viewed() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onProductViewed();
            </script>
            <?php
        }
    }

    public function product_added_to_cart() {
        if(! is_admin()) {

            ?>
                <script type="text/javascript">
                    // Ready state
                    (function($){

                        $( document.body ).on( 'added_to_cart', function(){
                            // alert('EVENT: added_to_cart');
                            idt_analytics.onProductAdded();
                        });

                    })(jQuery);
                </script>
            <?php

        }
    }

    public function product_removed_from_cart() {
        if(! is_admin()) {
            ?>
            <script type="text/javascript">
                // Ready state
                (function($){

                    $( document.body ).on( 'cart_item_removed', function(){
                        idt_analytics.onProductRemoved();
                    });

                })(jQuery);
            </script>
            <?php
        }
    }

    public function cart_viewed() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onCartViewed();
            </script>
            <?php
        }
    }

    public function checkout_started() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onCheckOutStarted();
            </script>
            <?php
        }
    }

    public function order_completed( $text ) {

        ?>
        <script>
            idt_analytics.onOrderCompleted();
        </script>
        <?php

        return $text;
    }

    public function order_cancelled() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onOrderCancelled();
            </script>
            <?php
        }
    }

    public function order_refunded() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onOrderRefunded();
            </script>
            <?php
        }
    }

    public function coupon_entered() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onCouponEntered();
            </script>
            <?php
        }
    }

    public function coupon_applied() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onCouponApplied();
            </script>
            <?php
        }
    }

    public function coupon_denied($text) {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onCouponDenied();
            </script>
            <?php
        }
        return $text;
    }

    public function coupon_removed() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onCouponRemoved();
            </script>
            <?php
        }
    }

    public function product_added_to_wishlist() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onProductAddedToWishList();
            </script>
            <?php
        }
    }

    /**
     * Page Loaded event
     */
    public function page_loaded() {
        if(! is_admin()) {
            ?>
            <script>
                let page = location.pathname.substr(1).split("/")[0];
                page = page.length == 0 ? "home" : page;

                idt_analytics.onPageLoaded(page);

            </script>
            <?php
        }
    }

    public function error_occurred() {
        if(! is_admin()) {
            ?>
            <script>
                idt_analytics.onError();
            </script>
            <?php
        }
    }

}