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
            $this->title               = $this->get_option( 'title' );
            $this->description         = $this->get_option( 'description' );
            $this->boleto_time         = $this->get_option( 'boleto_time' );
            $this->boleto_rate         = $this->get_option( 'boleto_rate' );
            $this->bank                = $this->get_option( 'bank' );
            $this->bank_agency         = $this->get_option( 'bank_agency' );
            $this->bank_account        = $this->get_option( 'bank_account' );
            $this->bank_account_digit  = $this->get_option( 'bank_account_digit' );
            $this->bank_wallet_code    = $this->get_option( 'bank_wallet_code' );
            $this->shop_cpf_cnpj       = $this->get_option( 'shop_cpf_cnpj' );
            $this->shop_address        = $this->get_option( 'shop_address' );
            $this->shop_city_state     = $this->get_option( 'shop_city_state' );
            $this->shop_corporate_name = $this->get_option( 'shop_corporate_name' );

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
                'boleto_details' => array(
                    'title' => __( 'Boleto Details', 'wcboleto' ),
                    'type' => 'title'
                ),
                'boleto_time' => array(
                    'title' => __( 'Deadline to pay the Boleto', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Number of days to pay.', 'wcboleto' ),
                    'default' => __( '5', 'wcboleto' )
                ),
                'boleto_rate' => array(
                    'title' => __( 'Boleto rate', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Number with dot, example <code>2.95</code>.', 'wcboleto' ),
                    'default' => __( '2.95', 'wcboleto' )
                ),
                'bank_details' => array(
                    'title' => __( 'Bank Details', 'wcboleto' ),
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
                'bank_agency' => array(
                    'title' => __( 'Agency', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Agency number.', 'wcboleto' ),
                ),
                'bank_account' => array(
                    'title' => __( 'Account', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Account number.', 'wcboleto' ),
                ),
                'bank_account_digit' => array(
                    'title' => __( 'Account Digit', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Account Digit.', 'wcboleto' ),
                ),
                'bank_wallet_code' => array(
                    'title' => __( 'Wallet code', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Insert the code.', 'wcboleto' ),
                ),
                'shop_details' => array(
                    'title' => __( 'Shop Details', 'wcboleto' ),
                    'type' => 'title'
                ),
                'shop_cpf_cnpj' => array(
                    'title' => __( 'CPF/CNPJ', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Document number.', 'wcboleto' ),
                ),
                'shop_address' => array(
                    'title' => __( 'Address', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Shop Address.', 'wcboleto' ),
                ),
                'shop_city_state' => array(
                    'title' => __( 'City/State', 'wcboleto' ),
                    'type' => 'text',
                    'description' => __( 'Example <code>S&atilde;o Paulo/SP</code>.', 'wcboleto' ),
                ),
                'shop_corporate_name' => array(
                    'title' => __( 'Corporate Name', 'wcboleto' ),
                    'type' => 'text',
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
        public function thankyou_page() {
            if ( $this->get_description() ) {

                // Generates boleto data.
                $this->generate_boleto_data( $_GET['order'] );

                // echo wpautop( wptexturize( $this->get_description() ) );

                printf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'key', $_GET['key'], get_permalink( woocommerce_get_page_id( 'thanks' ) ) ), __( 'Pagar Boleto &rarr;', 'wcboleto' ) );
            }
        }

        public function generate_boleto_data( $order_id ) {
            $id = (int) $order_id;
            $order = new WC_Order( $id );

            if ( $order->id ) {

                $shop_name = get_bloginfo( 'name' );
                $rate = str_replace( ',', '.', $this->boleto_rate );

                // Boleto data.
                $data['nosso_numero']       = $order->id; // max 8 digits
                $data['numero_documento']   = $order->id;
                $data['data_vencimento']    = date( 'd/m/Y', time() + ( $this->boleto_time * 86400 ) );
                $data['data_documento']     = date( 'd/m/Y' );
                $data['data_processamento'] = date( 'd/m/Y' );
                $data['valor_boleto']       = number_format( $order->order_total + $rate, 2, ',', '' );

                // Client data.
                $data['sacado']    = $order->billing_first_name . ' ' . $order->billing_last_name;
                $data['endereco1'] = ! empty( $order->billing_address_2 ) ? $order->billing_address_1 . ', ' . $order->billing_address_2 : $order->billing_address_1;
                $data['endereco2'] = sprintf( __( '%s - %s - CEP: %s', 'wcboleto' ), $order->billing_city, $order->billing_state, $order->billing_postcode );

                // Client info.
                $data['demonstrativo1'] = sprintf( __( 'Pagamento de Compra em %s', 'wcboleto' ), $shop_name );
                $data['demonstrativo2'] = sprintf( __( 'Mensalidade referente ao pedido #%s %sTaxa banc&aacute;ria - R$ %s', 'wcboleto' ), $order->id, '<br />', number_format( $rate, 2, ',', '' ) );
                $data['demonstrativo3'] = $shop_name . ' - ' . get_home_url();
                $data['instrucoes1']    = __( '- Sr. Caixa, cobrar multa de 2% ap&oacute;s o vencimento', 'wcboleto' );
                $data['instrucoes2']    = __( '- Receber at&eacute; 10 dias ap&oacute;s o vencimento', 'wcboleto' );
                $data['instrucoes3']    = sprintf( __( '- Em caso de d&uacute;vidas entre em contato conosco: %s', 'wcboleto' ), get_option( 'woocommerce_email_from_address' ) );
                $data['instrucoes4']    = '';

                // Bank data.
                $data['agencia']  = $this->bank_agency;
                $data['conta']    = $this->bank_account;
                $data['conta_dv'] = $this->bank_account_digit;
                $data['carteira'] = $this->bank_wallet_code;

                // Shop data.
                $data['identificacao'] = $shop_name;
                $data['cpf_cnpj']      = $this->shop_cpf_cnpj;
                $data['endereco']      = $this->shop_address;
                $data['cidade_uf']     = $this->shop_city_state;
                $data['cedente']       = $this->shop_corporate_name;

                update_post_meta( $order->id, 'wc_boleto_data', $data );

                echo '<pre>' . print_r( $data, true ) . '</pre>';
            }
        }


    } // class WC_Boleto_Gateway.
} // function wcboleto_gateway_load.
