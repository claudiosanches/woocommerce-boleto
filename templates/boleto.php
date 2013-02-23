<?php
/**
 * WooCommerce Boleto Template.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

// Test if exist ref.
if ( isset( $_GET['ref'] ) ) {
    global $wpdb;

    // Sanitize the ref.
    $ref = sanitize_title( $_GET['ref'] );

    // Gets post_id.
    $order_info = $wpdb->get_row( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_order_key' AND meta_value = '$ref'" );

    if ( $order_info ) {
        // Gets the data saved from boleto.
        $order_data = get_post_meta( $order_info->post_id, 'wc_boleto_data', true );

        // Gets current bank.
        $settings = get_option( 'woocommerce_boleto_settings' );
        $bank = sanitize_text_field( $settings['bank'] );

        // Sets the boleto data.
        $dadosboleto = array();
        foreach ( $order_data as $key => $value ) {
            $dadosboleto[ $key ] = $value;
        }

        // Extra fields.
        $dadosboleto['quantidade']     = '';
        $dadosboleto['valor_unitario'] = '';
        $dadosboleto['aceite']         = '';
        $dadosboleto['especie']        = 'R$';
        $dadosboleto['especie_doc']    = '';

        // Include bank templates.
        include WC_BOLETO_PATH . 'banks/' . $bank . '/functions.php';
        include WC_BOLETO_PATH . 'banks/' . $bank . '/layout.php';

        exit;
    }
}

// If an error occurred is redirected to the homepage.
wp_redirect( home_url() );
exit;
