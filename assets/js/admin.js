jQuery( document ).ready( function( $ ) {
    $( '#woocommerce_boleto_bank' ).change( function () {
        $( '#mainform' ).submit();
    });
});
