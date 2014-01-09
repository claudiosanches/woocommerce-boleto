<?php
/**
 * Plugin Name: WooCommerce Boleto
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-boleto
 * Description: WooCommerce Boleto is a brazilian payment gateway for WooCommerce
 * Author: claudiosanches, deblyn
 * Author URI: https://github.com/wpbrasil/
 * Version: 1.1.2
 * License: GPLv2 or later
 * Text Domain: wcboleto
 * Domain Path: /languages/
 */

/**
 * WooCommerce fallback notice.
 */
function wcboleto_woocommerce_fallback_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Boleto Gateway depends on the last version of %s to work!' , 'wcboleto' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', 'wcboleto' ) . '</a>' ) . '</p></div>';
}

/**
 * Load functions.
 */
function wcboleto_gateway_load() {

	/**
	 * Load textdomain.
	 */
	load_plugin_textdomain( 'wcboleto', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices', 'wcboleto_woocommerce_fallback_notice' );

		return;
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param array $methods Gateway methods.
	 *
	 * @return array         Gateway methods with boleto gateway.
	 */
	function wcboleto_add_gateway( $methods ) {
		$methods[] = 'WC_Boleto_Gateway';

		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'wcboleto_add_gateway' );

	// Include the WC_Boleto_Gateway class.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-boleto-gateway.php';

	// Include the WC_Boleto_Metabox class.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-boleto-metabox.php';
	new WC_Boleto_Metabox;
}

add_action( 'plugins_loaded', 'wcboleto_gateway_load', 0 );

/**
 * Create Payment Process page.
 */
function wcboleto_create_page() {
	if ( ! get_page_by_path( 'boleto' ) ) {

		$page = array(
			'post_title'     => __( 'Boleto', 'wcboleto' ),
			'post_name'      => 'boleto',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => '',
		);

		wp_insert_post( $page );
	}
}

register_activation_hook( __FILE__, 'wcboleto_create_page' );

/**
 * Add custom template page.
 */
function wcboleto_add_page_template( $page_template ) {
	if ( is_page( 'boleto' ) ) {
		$page_template = plugin_dir_path( __FILE__ ) . 'templates/boleto.php';
	}

	return $page_template;
}

add_filter( 'page_template', 'wcboleto_add_page_template' );

/**
 * Assets URL.
 *
 * @return string
 */
function wcboleto_assets_url() {
	return plugin_dir_url( __FILE__ ) . 'assets/';
}

/**
 * Display pending payment message in order details.
 *
 * @param  int $order_id Order id.
 *
 * @return string        Message HTML.
 */
function wcboleto_pending_payment_message( $order_id ) {
	$order = new WC_Order( $order_id );

	if ( 'on-hold' === $order->status && 'boleto' == $order->payment_method ) {
		$html = '<div class="woocommerce-info">';
		$html .= sprintf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'ref', $order->order_key, get_permalink( get_page_by_path( 'boleto' ) ) ), __( 'Pay the Boleto &rarr;', 'wcboleto' ) );

		$message = sprintf( __( '%sAttention!%s Not registered the payment the docket for this product yet.', 'wcboleto' ), '<strong>', '</strong>' ) . '<br />';
		$message .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', 'wcboleto' ) . '<br />';
		$message .= __( 'If you prefer, print and pay at any bank branch or home lottery.', 'wcboleto' ) . '<br />';
		$message .= __( 'Ignore this message if the payment has already been made​​.', 'wcboleto' ) . '<br />';

		$html .= apply_filters( 'wcboleto_pending_payment_message', $message, $order );

		$html .= '</div>';

		echo $html;
	}
}

add_action( 'woocommerce_view_order', 'wcboleto_pending_payment_message' );
