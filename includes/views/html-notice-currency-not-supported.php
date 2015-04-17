<?php
/**
 * Admin View: Notice - Currency not supported.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error">
	<p><strong><?php _e( 'WooCommerce Boleto Disabled', 'woocommerce-boleto' ); ?></strong>: <?php printf( __( 'Currency <code>%s</code> is not supported. Works only with Brazilian Real.', 'woocommerce-boleto' ), get_woocommerce_currency() ); ?>
	</p>
</div>
