<?php
/**
 * Plugin Name: WooCommerce Boleto
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-boleto
 * Description: WooCommerce Boleto is a brazilian payment gateway for WooCommerce
 * Author: claudiosanches, deblyn
 * Author URI: http://claudiosmweb.com
 * Version: 1.4.1
 * License: GPLv2 or later
 * Text Domain: woocommerce-boleto
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Boleto' ) ) :

/**
 * WooCommerce Boleto main class.
 */
class WC_Boleto {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.4.1';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin actions.
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			// Public includes.
			$this->includes();

			// Admin includes.
			if ( is_admin() ) {
				$this->admin_includes();
			}

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
			add_action( 'init', array( $this, 'add_boleto_endpoint' ) );
			add_action( 'template_include', array( $this, 'boleto_template' ) );
			add_action( 'woocommerce_view_order', array( $this, 'pending_payment_message' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-boleto' );

		load_textdomain( 'woocommerce-boleto', trailingslashit( WP_LANG_DIR ) . 'woocommerce-boleto/woocommerce-boleto-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-boleto', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Includes.
	 *
	 * @return void
	 */
	private function includes() {
		include_once 'includes/class-wc-boleto-gateway.php';
	}

	/**
	 * Includes.
	 *
	 * @return void
	 */
	private function admin_includes() {
		require_once 'includes/class-wc-boleto-admin.php';
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 *
	 * @return array          Payment methods with Boleto.
	 */
	public function add_gateway( $methods ) {
		$methods[] = 'WC_Boleto_Gateway';

		return $methods;
	}

	/**
	 * Created the boleto endpoint.
	 *
	 * @return void
	 */
	public function add_boleto_endpoint() {
		add_rewrite_endpoint( 'boleto', EP_PERMALINK | EP_ROOT );
	}

	/**
	 * Plugin activate method.
	 *
	 * @return void
	 */
	public static function activate() {
		// Add the boleto endpoint.
		add_rewrite_endpoint( 'boleto', EP_PERMALINK | EP_ROOT );

		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivate method.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Add custom template page.
	 *
	 * @param   [varname] [description]
	 *
	 * @return string
	 */
	public function boleto_template( $template ) {
		global $wp_query;

		if ( isset( $wp_query->query_vars['boleto'] ) ) {
			return plugin_dir_path( __FILE__ ) . 'templates/boleto.php';
		}

		return $template;
	}

	/**
	 * Gets the boleto URL.
	 *
	 * @param  string $code Boleto code.
	 *
	 * @return string       Boleto URL.
	 */
	public static function get_boleto_url( $code ) {
		$home = esc_url( home_url( '/' ) );

		if ( get_option( 'permalink_structure' ) ) {
			return trailingslashit( $home ) . 'boleto/' . $code;
		} else {
			return add_query_arg( array( 'boleto' => $code ), $home );
		}
	}

	/**
	 * Display pending payment message in order details.
	 *
	 * @param  int $order_id Order id.
	 *
	 * @return string        Message HTML.
	 */
	public function pending_payment_message( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( 'on-hold' === $order->status && 'boleto' == $order->payment_method ) {
			$html = '<div class="woocommerce-info">';
			$html .= sprintf( '<a class="button" href="%s" target="_blank" style="display: block !important; visibility: visible !important;">%s</a>', self::get_boleto_url( $order->order_key ), __( 'Pay the Ticket &rarr;', 'woocommerce-boleto' ) );

			$message = sprintf( __( '%sAttention!%s Not registered the payment the docket for this product yet.', 'woocommerce-boleto' ), '<strong>', '</strong>' ) . '<br />';
			$message .= __( 'Please click the following button and pay the Ticket in your Internet Banking.', 'woocommerce-boleto' ) . '<br />';
			$message .= __( 'If you prefer, print and pay at any bank branch or lottery retailer.', 'woocommerce-boleto' ) . '<br />';
			$message .= __( 'Ignore this message if the payment has already been made​​.', 'woocommerce-boleto' ) . '<br />';

			$html .= apply_filters( 'wcboleto_pending_payment_message', $message, $order );

			$html .= '</div>';

			echo $html;
		}
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Boleto Gateway depends on the last version of %s to work!', 'woocommerce-boleto' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', 'woocommerce-boleto' ) . '</a>' ) . '</p></div>';
	}
}

/**
 * Plugin activation and deactivation methods.
 */
register_activation_hook( __FILE__, array( 'WC_Boleto', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WC_Boleto', 'deactivate' ) );

/**
 * Initialize the plugin.
 */
add_action( 'plugins_loaded', array( 'WC_Boleto', 'get_instance' ), 0 );

endif;

/**
 * Assets URL.
 *
 * @return string
 */
function wcboleto_assets_url() {
	return plugin_dir_url( __FILE__ ) . 'assets/';
}
