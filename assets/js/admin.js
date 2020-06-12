(function( $ ) {
    // Data Save
    $( '#wcia-submit' ).on( 'click', function( e ) {
        e.preventDefault();

        $( '#wcia-submit' ).addClass( 'updating-message' );

        wp.ajax.send( 'wcia_save_settings', {
            data: $( '#integration-form' ).serialize(),
            success: function( response ) {

                $("#ajax-message")
                    .html('<p><strong>' + response.message + '</strong></p>')
                    .show()
                    .delay(3000)
                    .slideUp('fast');

                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');

                $( '#wcia-submit' ).removeClass( 'updating-message' );
            },
            error: function(error) {

                $("#ajax-message")
                    .html('<p style="color:red"><strong>' + error.message + '</strong></p>')
                    .show()
                    .delay(3000)
                    .slideUp('fast');
                //alert(error.message);
            }
        });

        return false;
    });

})( jQuery );
