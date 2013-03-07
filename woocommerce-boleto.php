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

define( 'WC_BOLETO_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Create Payment Process page.
 */
function wcboleto_create_page() {
    if ( ! get_page_by_path( 'boleto' ) ) {

        $page = array(
            'post_title'     => __( 'Pay Boleto', 'wcboleto' ),
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
        $page_template = WC_BOLETO_PATH . 'templates/boleto.php';
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

        $html .= sprintf( __( '%sAttention!%s Not registered the payment the docket for this product yet.', 'wcboleto' ), '<strong>', '</strong>' ) . '<br />';
        $html .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', 'wcboleto' ) . '<br />';
        $html .= __( 'If you prefer, print and pay at any bank branch or home lottery.', 'wcboleto' ) . '<br />';
        $html .= __( 'Ignore this message if the payment has already been made​​.', 'wcboleto' ) . '<br />';

        $html .= '</div>';

        echo $html;
    }
}

add_action( 'woocommerce_view_order', 'wcboleto_pending_payment_message' );

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
            $this->icon         = plugins_url( 'assets/images/boleto.png', __FILE__ );
            $this->has_fields   = false;
            $this->method_title = __( 'Boleto', 'wcboleto' );

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user settings variables.
            $this->title       = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->boleto_time = $this->settings['boleto_time'];
            $this->boleto_rate = $this->settings['boleto_rate'];

            // Actions.
            add_action( 'woocommerce_thankyou_boleto', array( $this, 'thankyou_page' ) );
            add_action( 'woocommerce_email_after_order_table', array( $this, 'email_instructions' ), 10, 2 );
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );

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

            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#woocommerce_boleto_bank').change(function() {
                        $('#mainform').submit();
                    });
                });
            </script>

            <?php
        }

        /**
         * Start Gateway Settings Form Fields.
         *
         * @return void
         */
        public function init_form_fields() {

            $general_fields = array(
                'enabled' => array(
                    'title'   => __( 'Enable/Disable', 'wcboleto' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable Boleto standard', 'wcboleto' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title'       => __( 'Title', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'wcboleto' ),
                    'default'     => __( 'Boleto', 'wcboleto' )
                ),
                'description' => array(
                    'title'       => __( 'Description', 'wcboleto' ),
                    'type'        => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'wcboleto' ),
                    'default'     => __( 'Pay with Boleto', 'wcboleto' )
                ),
                'boleto_details' => array(
                    'title' => __( 'Boleto Details', 'wcboleto' ),
                    'type'  => 'title'
                ),
                'boleto_time' => array(
                    'title'       => __( 'Deadline to pay the Boleto', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'Number of days to pay.', 'wcboleto' ),
                    'default'     => 5
                ),
                'boleto_rate' => array(
                    'title'       => __( 'Boleto rate', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'Number with dot, example <code>2.95</code>.', 'wcboleto' ),
                    'default'     => __( '2.95', 'wcboleto' )
                ),
                'boleto_logo' => array(
                    'title'       => __( 'Boleto Logo', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'Logo with 147px x 46px.', 'wcboleto' ),
                    'default'     => plugins_url( 'assets/images/logo_empresa.png', __FILE__ )
                ),
                'bank_details' => array(
                    'title' => __( 'Bank Details', 'wcboleto' ),
                    'type'  => 'title'
                ),
                'bank' => array(
                    'title'       => __( 'Bank', 'wcboleto' ),
                    'type'        => 'select',
                    'description' => __( 'Choose the bank for Boleto.', 'wcboleto' ),
                    'default'     => __( 'Pay with Boleto', 'wcboleto' ),
                    'options'     => array(
                        '0'          => '--',
                        'banespa'    => 'Banespa',
                        'bb'         => 'Banco do Brasil',
                        'bradesco'   => 'Bradesco',
                        'cef'        => 'Caixa Economica Federal - SR (SICOB)',
                        'cef_sigcb'  => 'Caixa Economica Federal - SIGCB',
                        'cef_sinco'  => 'Caixa Economica Federal - SINCO',
                        'hsbc'       => 'HSBC',
                        'itau'       => 'Itau',
                        'nossacaixa' => 'Nossa Caixa',
                        'real'       => 'Real',
                        'santander'  => 'Santander',
                        'unibanco'   => 'Unibanco',
                    )
                )
            );

            $shop_fields = array(
                'shop_details' => array(
                    'title' => __( 'Shop Details', 'wcboleto' ),
                    'type'  => 'title'
                ),
                'cpf_cnpj' => array(
                    'title'       => __( 'CPF/CNPJ', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'Document number.', 'wcboleto' ),
                ),
                'endereco' => array(
                    'title'       => __( 'Address', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'Shop Address.', 'wcboleto' ),
                ),
                'cidade_uf' => array(
                    'title'       => __( 'City/State', 'wcboleto' ),
                    'type'        => 'text',
                    'description' => __( 'Example <code>S&atilde;o Paulo/SP</code>.', 'wcboleto' ),
                ),
                'cedente' => array(
                    'title' => __( 'Corporate Name', 'wcboleto' ),
                    'type'  => 'text',
                ),
            );

            $this->form_fields = array_merge( $general_fields, $this->bank_fields(), $shop_fields );
        }

        protected function bank_fields() {

            switch ( $this->get_option( 'bank' ) ) {
                case 'itau':
                    $fields = array(
                        'agencia' => array(
                            'title' => __( 'Agency', 'wcboleto' ),
                            'type' => 'text',
                            'description' => __( 'Agency number.', 'wcboleto' ),
                        ),
                        'conta' => array(
                            'title' => __( 'Account', 'wcboleto' ),
                            'type' => 'text',
                            'description' => __( 'Account number.', 'wcboleto' ),
                        ),
                        'conta_dv' => array(
                            'title' => __( 'Account Digit', 'wcboleto' ),
                            'type' => 'text',
                            'description' => __( 'Account Digit.', 'wcboleto' ),
                        ),
                        'carteira' => array(
                            'title' => __( 'Wallet code', 'wcboleto' ),
                            'type' => 'text',
                            'description' => __( 'Insert the code (175, 174, 104, 109, 178, or 157).', 'wcboleto' ),
                        ),
                    );
                    break;

                default:
                    $fields = array();
                    break;
            }

            return $fields;
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

            // Generates boleto data.
            $this->generate_boleto_data( $order );

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

                $html = '<div class="woocommerce-message">';
                $html .= sprintf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'ref', $_GET['key'], get_permalink( get_page_by_path( 'boleto' ) ) ), __( 'Pay the Boleto &rarr;', 'wcboleto' ) );

                $html .= sprintf( __( '%sAttention!%s You will not get the ticket by Correios.', 'wcboleto' ), '<strong>', '</strong>' ) . '<br />';
                $html .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', 'wcboleto' ) . '<br />';
                $html .= __( 'If you prefer, print and pay at any bank branch or home lottery.', 'wcboleto' ) . '<br />';

                $html .= '<strong style="display: block; margin-top: 15px; font-size: 0.8em">' . sprintf( __( 'Validity of the Boleto: %s.', 'wcboleto' ), date( 'd/m/Y', time() + ( $this->boleto_time * 86400 ) ) ) . '</strong>';

                $html .= '</div>';

                echo $html;
            }
        }

        public function generate_boleto_data( $order ) {
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

            update_post_meta( $order->id, 'wc_boleto_data', $data );
        }

        /**
         * Add content to the WC emails.
         */
        function email_instructions( $order, $sent_to_admin ) {

            if ( $sent_to_admin ) {
                return;
            }

            if ( $order->status !== 'on-hold' ) {
                return;
            }

            if ( $order->payment_method !== 'boleto' ) {
                return;
            }

            $html = '<h2>' . __( 'Payment', 'wcboleto' ) . '</h2>';

            $html .= '<p class="order_details">';

            $html .= sprintf( __( '%sAttention!%s You will not get the ticket by Correios.', 'wcboleto' ), '<strong>', '</strong>' ) . '<br />';
            $html .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', 'wcboleto' ) . '<br />';
            $html .= __( 'If you prefer, print and pay at any bank branch or home lottery.', 'wcboleto' ) . '<br />';

            $html .= '<br />' . sprintf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'ref', $order->order_custom_fields['_order_key'][0], get_permalink( get_page_by_path( 'boleto' ) ) ), __( 'Pay the Boleto &rarr;', 'wcboleto' ) ) . '<br />';

            $html .= '<strong style="font-size: 0.8em">' . sprintf( __( 'Validity of the Boleto: %s.', 'wcboleto' ), date( 'd/m/Y', time() + ( $this->boleto_time * 86400 ) ) ) . '</strong>';

            $html .= '</p>';

            echo $html;
        }

    } // class WC_Boleto_Gateway.
} // function wcboleto_gateway_load.
