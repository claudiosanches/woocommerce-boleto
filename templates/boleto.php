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
        $logo = sanitize_text_field( $settings['boleto_logo'] );
        $shop_name = get_bloginfo( 'name' );
        $rate = str_replace( ',', '.', $settings['boleto_rate'] );

        // Sets the boleto data.
        $dadosboleto = array();
        foreach ( $order_data as $key => $value ) {
            $dadosboleto[ $key ] = $value;
        }

        // Client info.
        $dadosboleto['demonstrativo1'] = sprintf( __( 'Payment for purchase in %s', 'wcboleto' ), $shop_name );
        $dadosboleto['demonstrativo2'] = sprintf( __( 'Payment referred to the order #%s %sBank Rate - R$ %s', 'wcboleto' ), $dadosboleto['nosso_numero'], '<br />', number_format( $rate, 2, ',', '' ) );
        $dadosboleto['demonstrativo3'] = $shop_name . ' - ' . get_home_url();
        $dadosboleto['instrucoes1']    = __( '- Mr. Cash, charge a fine of 2% after maturity', 'wcboleto' );
        $dadosboleto['instrucoes2']    = __( '- Receive up to 10 days past due', 'wcboleto' );
        $dadosboleto['instrucoes3']    = sprintf( __( '- For questions please contact us: %s', 'wcboleto' ), get_option( 'woocommerce_email_from_address' ) );
        $dadosboleto['instrucoes4']    = '';

        // Bank data.
        $dadosboleto['agencia']        = $settings['bank_agency'];
        $dadosboleto['conta']          = $settings['bank_account'];
        $dadosboleto['conta_dv']       = $settings['bank_account_digit'];
        $dadosboleto['carteira']       = $settings['bank_wallet_code'];

        // Shop data.
        $dadosboleto['identificacao']  = $shop_name;
        $dadosboleto['cpf_cnpj']       = $settings['shop_cpf_cnpj'];
        $dadosboleto['endereco']       = $settings['shop_address'];
        $dadosboleto['cidade_uf']      = $settings['shop_city_state'];
        $dadosboleto['cedente']        = $settings['shop_corporate_name'];

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
