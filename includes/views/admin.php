<div class="wrap wcia-admin">

    <?php do_action( 'wcia_before_nav' ); ?>

    <div>
        <h2>Insense Analytics</h2>
    </div>

    <?php
    $this->show_navigation();
    $tab   = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'settings';
    ?>
    <div id="ajax-message" class="updated inline" style="display: none; margin-bottom:35px"></div>
    <?php
        if ( $tab == 'settings' ) {
            include WCIA_INCLUDES . '/views/settings.php';
        } else {
            do_action( 'wcia_nav_content_'.$tab );
        }
    ?>

</div>