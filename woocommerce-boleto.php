<?php
/**
 * Plugin Name: WooCommerce Boleto
 * Plugin URI: https://github.com/wpbrasil/woocommerce-boleto
 * Description: WooCommerce Boleto is a brazilian payment gateway for WooCommerce
 * Author: claudiosanches
 * Author URI: https://github.com/wpbrasil/
 * Version: 0.1
 * License: GPLv2 or later
 * Text Domain: wcboleto
 * Domain Path: /languages/
 */

/**
 * WooCommerce fallback notice.
 */
function wcboleto_woocommerce_fallback_notice() {
    $message = '<div class="error">';
        $message .= '<p>' . __( 'WooCommerce Boleto Gateway depends on the last version of <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!' , 'wcboleto' ) . '</p>';
    $message .= '</div>';

    echo $message;
}

/**
 * Load functions.
 */
add_action( 'plugins_loaded', 'wcboleto_gateway_load', 0 );

function wcboleto_gateway_load() {

    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        add_action( 'admin_notices', 'wcboleto_woocommerce_fallback_notice' );

        return;
    }

    /**
     * Load textdomain.
     */
    load_plugin_textdomain( 'wcboleto', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * Add the gateway to WooCommerce.
     *
     * @access public
     * @param array $methods
     * @return array
     */
    add_filter( 'woocommerce_payment_gateways', 'wcboleto_add_gateway' );

    function wcboleto_add_gateway( $methods ) {
        $methods[] = 'WC_Boleto_Gateway';
        return $methods;
    }

    /**
     * WC Boleto Gateway Class.
     *
     * Built the Boleto method.
     */
    class WC_Boleto_Gateway extends WC_Payment_Gateway {

        /**
         * Gateway's Constructor.
         *
         * @return void
         */
        public function __construct() {
            global $woocommerce;

            $this->id           = 'boleto';
            $this->icon         = plugins_url( 'images/boleto.png', __FILE__ );
            $this->has_fields   = false;
            $this->method_title = __( 'Boleto', 'wcboleto' );

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user settings variables.
            $this->title         = $this->settings['title'];
            $this->description   = $this->settings['description'];
            $this->bank          = $this->settings['bank'];
            $this->agency        = $this->settings['agency'];
            $this->account       = $this->settings['account'];
            $this->account_digit = $this->settings['account_digit'];

            // Actions.
            add_action( 'woocommerce_thankyou_boleto', array( $this, 'thankyou_page' ) );

            if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '<' ) ) {
                add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
            } else {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
            }

            // Valid for use.
            $this->enabled = ( 'yes' == $this->settings['enabled'] ) && $this->is_valid_for_use();
        }

        /**
         * Checking if this gateway is enabled and available in the user's country.
         *
         * @return bool
         */
        public function is_valid_for_use() {
            if ( ! in_array( get_woocommerce_currency(), array( 'BRL' ) ) ) {
                return false;
            }

            return true;
        }

        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis.
         *
         * @since 1.0.0
         */
        public function admin_options() {
            ?>

            <h3><?php _e( 'Boleto standard', 'wcboleto' ); ?></h3>
            <p><?php _e( 'Enables payments via Boleto.', 'wcboleto' ); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>

            <?php
        }

        /**
         * Start Gateway Settings Form Fields.
         *
         * @return void
         */
        public function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'wcboleto' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable Boleto standard', 'wcboleto' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Title', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'wcboleto' ),
                    'default' => __( 'Boleto', 'wcboleto' )
                ),
                'description' => array(
                    'title' => __( 'Description', 'wcboleto' ),
                    'type' => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'wcboleto' ),
                    'default' => __( 'Pay with Boleto', 'wcboleto' )
                ),
                'details' => array(
                    'title' => __( 'Boleto Details', 'wcboleto' ),
                    'type' => 'title'
                ),
                'bank' => array(
                    'title' => __( 'Bank', 'wcboleto' ),
                    'type' => 'select',
                    'description' => __( 'Choose the bank for Boleto.', 'wcboleto' ),
                    'default' => __( 'Pay with Boleto', 'wcboleto' ),
                    'options' => array(
                        'itau' => __( 'Itau', 'wcboleto' ),
                    )
                ),
                'agency' => array(
                    'title' => __( 'Agency', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Agency number.', 'wcboleto' ),
                ),
                'account' => array(
                    'title' => __( 'Account', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Account number.', 'wcboleto' ),
                ),
                'account_digit' => array(
                    'title' => __( 'Account Digit', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Account Digit.', 'wcboleto' ),
                ),
            );
        }

        /**
         * Process the payment and return the result.
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment( $order_id ) {
            global $woocommerce;

            $order = new WC_Order( $order_id );

            // Mark as on-hold (we're awaiting the boleto).
            $order->update_status( 'on-hold', __( 'Awaiting boleto payment', 'wcboleto' ) );

            // Reduce stock levels.
            $order->reduce_order_stock();

            // Remove cart.
            $woocommerce->cart->empty_cart();

            // Return thankyou redirect.
            return array(
                'result'    => 'success',
                'redirect'  => add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order_id, get_permalink( woocommerce_get_page_id( 'thanks' ) ) ) )
            );
        }

        /**
         * Output for the order received page.
         *
         * @access public
         * @return void
         */
        function thankyou_page() {
            if ( $description = $this->get_description() ) {
                // TODO: MENSAGEM DE IMPRIMIR O BOLETO AQUI!
                echo wpautop( wptexturize( $description ) );
            }
        }


    } // class WC_Boleto_Gateway.
} // function wcboleto_gateway_load.
