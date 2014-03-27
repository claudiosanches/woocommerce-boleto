<?php
/**
 * Plugin Name: WooCommerce Boleto
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-boleto
 * Description: WooCommerce Boleto is a brazilian payment gateway for WooCommerce
 * Author: claudiosanches, deblyn
 * Author URI: https://github.com/wpbrasil/
 * Version: 1.2.2
 * License: GPLv2 or later
 * Text Domain: woocommerce-boleto
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Boleto' ) ) :

/**
 * WooCommerce Boleto main class.
 */
class WC_Boleto {

	/**
	 * Plugin version.
	 *
	 * @since 1.2.2
	 *
	 * @var   string
	 */
	const VERSION = '1.2.2';

	/**
	 * Integration id.
	 *
	 * @since 1.2.0
	 *
	 * @var   string
	 */
	protected static $gateway_id = 'boleto';

	/**
	 * Plugin slug.
	 *
	 * @since 1.2.0
	 *
	 * @var   string
	 */
	protected static $plugin_slug = 'woocommerce-boleto';

	/**
	 * Instance of this class.
	 *
	 * @since 1.2.0
	 *
	 * @var   object
	 */
	protected static $instance = null;

	public function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Initialize the plugin actions.
		$this->init();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since  1.2.0
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
	 * Return the plugin slug.
	 *
	 * @since  1.2.0
	 *
	 * @return string Plugin slug variable.
	 */
	public static function get_plugin_slug() {
		return self::$plugin_slug;
	}

	/**
	 * Return the gateway id/slug.
	 *
	 * @since  1.2.0
	 *
	 * @return string Gateway id/slug variable.
	 */
	public static function get_gateway_id() {
		return self::$gateway_id;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since  1.2.0
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$domain = self::$plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Initialize the plugin public actions.
	 *
	 * @since  1.2.0
	 *
	 * @return  void
	 */
	protected function init() {
		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			// Include the WC_Boleto_Gateway class.
			include_once 'includes/class-wc-boleto-gateway.php';

			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
			add_action( 'init', array( $this, 'add_boleto_endpoint' ) );
			add_action( 'template_redirect', array( $this, 'boleto_template' ) );
			add_action( 'woocommerce_view_order', array( $this, 'pending_payment_message' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @since  1.2.0
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
	 * @since  1.2.0
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
	 * @since  1.2.0
	 *
	 * @return string
	 */
	public function boleto_template() {
		global $wp_query;

		if ( ! isset( $wp_query->query_vars['boleto'] ) ) {
			return;
		}

		// Support for plugin older versions.
		$boleto_code = isset( $_GET['ref'] ) ? $_GET['ref'] : $wp_query->query_vars['boleto'];
		include_once plugin_dir_path( __FILE__ ) . 'templates/boleto.php';

		exit;
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
	 * @since  1.2.0
	 *
	 * @param  int $order_id Order id.
	 *
	 * @return string        Message HTML.
	 */
	public function pending_payment_message( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( 'on-hold' === $order->status && 'boleto' == $order->payment_method ) {
			$html = '<div class="woocommerce-info">';
			$html .= sprintf( '<a class="button" href="%s" target="_blank">%s</a>', self::get_boleto_url( $order->order_key ), __( 'Pay the Boleto &rarr;', self::$plugin_slug ) );

			$message = sprintf( __( '%sAttention!%s Not registered the payment the docket for this product yet.', self::$plugin_slug ), '<strong>', '</strong>' ) . '<br />';
			$message .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', self::$plugin_slug ) . '<br />';
			$message .= __( 'If you prefer, print and pay at any bank branch or home lottery.', self::$plugin_slug ) . '<br />';
			$message .= __( 'Ignore this message if the payment has already been made​​.', self::$plugin_slug ) . '<br />';

			$html .= apply_filters( 'wcboleto_pending_payment_message', $message, $order );

			$html .= '</div>';

			echo $html;
		}
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @since  1.2.0
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Boleto Gateway depends on the last version of %s to work!', self::$plugin_slug ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', self::$plugin_slug ) . '</a>' ) . '</p></div>';
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

/**
 * Plugin admin.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once 'includes/class-wc-boleto-admin.php';

	add_action( 'plugins_loaded', array( 'WC_Boleto_Admin', 'get_instance' ) );
}

endif;

/**
 * Assets URL.
 *
 * @return string
 */
function wcboleto_assets_url() {
	return plugin_dir_url( __FILE__ ) . 'assets/';
}
