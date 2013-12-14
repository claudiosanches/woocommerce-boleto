<?php
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
		$this->id           = 'boleto';
		$this->icon         = apply_filters( 'wcboleto_icon', plugins_url( 'assets/images/boleto.png', plugin_dir_path( __FILE__ ) ) );
		$this->has_fields   = false;
		$this->method_title = __( 'Boleto', 'wcboleto' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user settings variables.
		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->boleto_time = $this->get_option( 'boleto_time' );

		// Actions.
		add_action( 'woocommerce_thankyou_boleto', array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_instructions' ), 10, 2 );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// Display admin notices.
		$this->admin_notices();
	}

	/**
	 * Backwards compatibility with version prior to 2.1.
	 *
	 * @return object Returns the main instance of WooCommerce class.
	 */
	protected function woocommerce_instance() {
		if ( function_exists( 'WC' ) ) {
			return WC();
		} else {
			global $woocommerce;
			return $woocommerce;
		}
	}

	/**
	 * Displays notifications when the admin has something wrong with the configuration.
	 *
	 * @return void
	 */
	protected function admin_notices() {
		if ( is_admin() ) {
			// Checks that the currency is supported
			if ( ! $this->using_supported_currency() ) {
				add_action( 'admin_notices', array( $this, 'currency_not_supported_message' ) );
			}
		}
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	protected function using_supported_currency() {
		return ( 'BRL' == get_woocommerce_currency() );
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available() {
		// Test if is valid for use.
		$available = ( 'yes' == $this->get_option( 'enabled' ) ) && $this->using_supported_currency();

		return $available;
	}

	/**
	 * Admin Panel Options.
	 *
	 * @return string Admin form.
	 */
	public function admin_options() {
		echo '<h3>' . __( 'Boleto standard', 'wcboleto' ) . '</h3>';
		echo '<p>' . __( 'Enables payments via Boleto.', 'wcboleto' ) . '</p>';

		// Generate the HTML For the settings form.
		echo '<table class="form-table">';
		$this->generate_settings_html();
		echo '</table>';
		echo '<script type="text/javascript" src="' . plugins_url( 'assets/js/admin.js', plugin_dir_path( __FILE__ ) ) . '"></script>';
	}

	/**
	 * Start Gateway Settings Form Fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$shop_name = get_bloginfo( 'name' );

		$first = array(
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
				'desc_tip'    => true,
				'default'     => __( 'Boleto', 'wcboleto' )
			),
			'description' => array(
				'title'       => __( 'Description', 'wcboleto' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'wcboleto' ),
				'desc_tip'    => true,
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
				'desc_tip'    => true,
				'default'     => 5
			),
			'boleto_logo' => array(
				'title'       => __( 'Boleto Logo', 'wcboleto' ),
				'type'        => 'text',
				'description' => __( 'Logo with 147px x 46px.', 'wcboleto' ),
				'desc_tip'    => true,
				'default'     => plugins_url( 'assets/images/logo_empresa.png', plugin_dir_path( __FILE__ ) )
			),
			'bank_details' => array(
				'title' => __( 'Bank Details', 'wcboleto' ),
				'type'  => 'title'
			),
			'bank' => array(
				'title'       => __( 'Bank', 'wcboleto' ),
				'type'        => 'select',
				'desc_tip'    => true,
				'description' => __( 'Choose the bank for Boleto.', 'wcboleto' ),
				'default'     => __( 'Pay with Boleto', 'wcboleto' ),
				'options'     => array(
					'0'          => '--',
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

		$last = array(
			'extra_details' => array(
				'title' => __( 'Optional Data', 'wcboleto' ),
				'type'  => 'title'
			),
			'quantidade' => array(
				'title'       => __( 'Quantity', 'wcboleto' ),
				'type'        => 'text'
			),
			'valor_unitario' => array(
				'title'       => __( 'Unitary value', 'wcboleto' ),
				'type'        => 'text'
			),
			'aceite' => array(
				'title'       => __( 'Acceptance', 'wcboleto' ),
				'type'        => 'text'
			),
			'especie' => array(
				'title'       => __( 'Currency', 'wcboleto' ),
				'type'        => 'text',
				'default'     => 'R$'
			),
			'especie_doc' => array(
				'title'       => __( 'Kind of document', 'wcboleto' ),
				'type'        => 'text'
			),
			'especie' => array(
				'title'       => __( 'Currency', 'wcboleto' ),
				'type'        => 'text',
				'default'     => 'R$'
			),
			'demonstrative' => array(
				'title' => __( 'Demonstrative', 'wcboleto' ),
				'type'  => 'title'
			),
			'demonstrativo1' => array(
				'title'       => __( 'Line 1', 'wcboleto' ),
				'type'        => 'text',
				'description' => __( 'Use [number] to show the Order ID.', 'wcboleto' ),
				'desc_tip'    => true,
				'default'     => sprintf( __( 'Payment for purchase in %s', 'wcboleto' ), $shop_name )
			),
			'demonstrativo2' => array(
				'title'       => __( 'Line 2', 'wcboleto' ),
				'type'        => 'text',
				'description' => __( 'Use [number] to show the Order ID.', 'wcboleto' ),
				'desc_tip'    => true,
				'default'     => __( 'Payment referred to the order [number]', 'wcboleto' )
			),
			'demonstrativo3' => array(
				'title'       => __( 'Line 3', 'wcboleto' ),
				'type'        => 'text',
				'description' => __( 'Use [number] to show the Order ID.', 'wcboleto' ),
				'desc_tip'    => true,
				'default'     => $shop_name . ' - ' . home_url()
			),
			'instructions' => array(
				'title' => __( 'Instructions', 'wcboleto' ),
				'type'  => 'title'
			),
			'instrucoes1' => array(
				'title'       => __( 'Line 1', 'wcboleto' ),
				'type'        => 'text',
				'default'     => __( '- Mr. Cash, charge a fine of 2% after maturity', 'wcboleto' )
			),
			'instrucoes2' => array(
				'title'       => __( 'Line 2', 'wcboleto' ),
				'type'        => 'text',
				'default'     => __( '- Receive up to 10 days past due', 'wcboleto' )
			),
			'instrucoes3' => array(
				'title'       => __( 'Line 3', 'wcboleto' ),
				'type'        => 'text',
				'default'     => sprintf( __( '- For questions please contact us: %s', 'wcboleto' ), get_option( 'woocommerce_email_from_address' ) )
			),
			'instrucoes4' => array(
				'title'       => __( 'Line 4', 'wcboleto' ),
				'type'        => 'text',
				'default'     => ''
			),
			'shop_details' => array(
				'title' => __( 'Shop Details', 'wcboleto' ),
				'type'  => 'title'
			),
			'cpf_cnpj' => array(
				'title'       => __( 'CPF/CNPJ', 'wcboleto' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Document number.', 'wcboleto' ),
			),
			'endereco' => array(
				'title'       => __( 'Address', 'wcboleto' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Shop Address.', 'wcboleto' ),
			),
			'cidade_uf' => array(
				'title'       => __( 'City/State', 'wcboleto' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Example <code>S&atilde;o Paulo/SP</code>.', 'wcboleto' ),
			),
			'cedente' => array(
				'title' => __( 'Corporate Name', 'wcboleto' ),
				'type'  => 'text',
			),
		);

		$this->form_fields = array_merge( $first, $this->get_bank_fields(), $last );
	}

	/**
	 * Gets bank fields.
	 *
	 * @return array Current bank fields.
	 */
	protected function get_bank_fields() {

		switch ( $this->get_option( 'bank' ) ) {
			case 'bb':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' )
					),
					'convenio' => array(
						'title'       => __( 'Agreement number', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agreements with 6, 7 or 8 digits.', 'wcboleto' )
					),
					'contrato' => array(
						'title' => __( 'Contract number', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title' => __( 'Wallet code', 'wcboleto' ),
						'type'  => 'text'
					),
					'variacao_carteira' => array(
						'title'       => __( 'Wallet variation (optional)', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Wallet variation with dash.', 'wcboleto' )
					),
					'formatacao_convenio' => array(
						'title'       => __( 'Agreement format', 'wcboleto' ),
						'type'        => 'select',
						'default'     => '6',
						'options'     => array(
							'6' => __( 'Agreement with 6 digits', 'wcboleto' ),
							'7' => __( 'Agreement with 7 dígitos', 'wcboleto' ),
							'8' => __( 'Agreement with 8 dígitos', 'wcboleto' ),
						)
					),
					'formatacao_nosso_numero' => array(
						'title'       => __( 'Our number formatting', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Used only for agreement with 6 digits (enter 1 for Our Number is up to 5 digits or 2 for option up to 17 digits).', 'wcboleto' )
					)
				);
				break;
			case 'bradesco':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' ),
					),
					'agencia_dv' => array(
						'title' => __( 'Agency digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' ),
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit (only numbers).', 'wcboleto' ),
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'   => __( 'Wallet code', 'wcboleto' ),
						'type'    => 'select',
						'default' => '03',
						'options' => array(
							'03' => '03',
							'06' => '06',
							'09' => '09'
						)
					)
				);
				break;
			case 'cef':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit, use only numbers', 'wcboleto' )
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'wcboleto' ),
						'type'        => 'select',
						'description' => __( 'Confirm this information with your manager.', 'wcboleto' ),
						'default'     => 'SR',
						'options'     => array(
							'SR' => __( 'Without registry', 'wcboleto' ),
							'CR' => __( 'With registry', 'wcboleto' )
						)
					),
					'inicio_nosso_numero' => array(
						'title'       => __( 'Beginning of the Our Number', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Use <code>80, 81 or 82</code> for <strong>Without registry</strong> or <code>90</code> for <strong>With registry</strong>. Confirm this information with your manager.', 'wcboleto' ),
						'default'     => '80'
					)
				);
				break;
			case 'cef_sigcb':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account with 6 digits, use only numbers.', 'wcboleto' )
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'wcboleto' ),
						'type'        => 'select',
						'description' => __( 'Confirm this information with your manager.', 'wcboleto' ),
						'default'     => 'SR',
						'options'     => array(
							'SR' => __( 'Without registry', 'wcboleto' ),
							'CR' => __( 'With registry', 'wcboleto' )
						)
					)
				);
				break;
			case 'cef_sinco':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' ),
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit, use only numbers', 'wcboleto' )
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'wcboleto' ),
						'type'        => 'select',
						'description' => __( 'Confirm this information with your manager.', 'wcboleto' ),
						'default'     => 'SR',
						'options'     => array(
							'SR' => __( 'Without registry', 'wcboleto' ),
							'CR' => __( 'With registry', 'wcboleto' )
						)
					),
				);
				break;
			case 'hsbc':
				$fields = array(
					'codigo_cedente' => array(
						'title'       => __( 'Transferor code', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor code with only 7 digits.', 'wcboleto' )
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'wcboleto' ),
						'type'        => 'select',
						'description' => __( 'Accepts only CNR.', 'wcboleto' ),
						'default'     => 'CNR',
						'options'     => array(
							'CNR' => 'CNR'
						)
					)
				);
				break;
			case 'itau':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number.', 'wcboleto' ),
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'   => __( 'Wallet code', 'wcboleto' ),
						'type'    => 'select',
						'default' => '104',
						'options' => array(
							'104' => '104',
							'109' => '109',
							'157' => '157',
							'174' => '174',
							'175' => '175',
							'178' => '178'
						)
					)
				);
				break;
			case 'nossacaixa':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit and with only 6 numbers.', 'wcboleto' )
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'   => __( 'Wallet code', 'wcboleto' ),
						'type'    => 'select',
						'default' => '1',
						'options' => array(
							'1' => __( 'Simple Billing (1)', 'wcboleto' ),
							'5' => __( 'Direct Billing (5)', 'wcboleto' )
						)
					),
					'modalidade_conta' => array(
						'title'       => __( 'Account modality', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account modality with two positions (example: 04).', 'wcboleto' )
					)
				);
				break;
			case 'real':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' )
					),
					'carteira' => array(
						'title' => __( 'Wallet code', 'wcboleto' ),
						'type'  => 'text'
					)
				);
				break;
			case 'santander':
				$fields = array(
					'codigo_cliente' => array(
						'title'       => __( 'Customer code', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Customer code (PSK) with only 7 digits.', 'wcboleto' )
					),
					'ponto_venda' => array(
						'title'       => __( 'Sale point (Agency)', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number.', 'wcboleto' )
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Simple collection - Without registration.', 'wcboleto' )
					),
					'carteira_descricao' => array(
						'title'   => __( 'Wallet description', 'wcboleto' ),
						'type'    => 'text',
						'default' => 'COBRANÇA SIMPLES - CSR'
					)
				);
				break;
			case 'unibanco':
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'wcboleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'wcboleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'wcboleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'wcboleto' ),
						'type'  => 'text'
					),
					'codigo_cliente' => array(
						'title' => __( 'Customer code', 'wcboleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title' => __( 'Wallet code', 'wcboleto' ),
						'type'  => 'text'
					)
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
	 * @param int    $order_id Order ID.
	 *
	 * @return array           Redirect.
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the boleto).
		$order->update_status( 'on-hold', __( 'Awaiting boleto payment', 'wcboleto' ) );

		// Generates boleto data.
		$this->generate_boleto_data( $order );

		// Reduce stock levels.
		$order->reduce_order_stock();

		// Remove cart.
		$this->woocommerce_instance()->cart->empty_cart();

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
			$url = $order->get_checkout_order_received_url();
		} else {
			$url = add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order_id, get_permalink( woocommerce_get_page_id( 'thanks' ) ) ) );
		}

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $url
		);
	}

	/**
	 * Output for the order received page.
	 *
	 * @return string Thank You message.
	 */
	public function thankyou_page() {
		$html = '<div class="woocommerce-message">';
		$html .= sprintf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'ref', $_GET['key'], get_permalink( get_page_by_path( 'boleto' ) ) ), __( 'Pay the Boleto &rarr;', 'wcboleto' ) );

		$message = sprintf( __( '%sAttention!%s You will not get the ticket by Correios.', 'wcboleto' ), '<strong>', '</strong>' ) . '<br />';
		$message .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', 'wcboleto' ) . '<br />';
		$message .= __( 'If you prefer, print and pay at any bank branch or home lottery.', 'wcboleto' ) . '<br />';

		$html .= apply_filters( 'wcboleto_thankyou_page_message', $message );

		$html .= '<strong style="display: block; margin-top: 15px; font-size: 0.8em">' . sprintf( __( 'Validity of the Boleto: %s.', 'wcboleto' ), date( 'd/m/Y', time() + ( $this->boleto_time * 86400 ) ) ) . '</strong>';

		$html .= '</div>';

		echo $html;
	}

	/**
	 * Generate boleto data.
	 *
	 * @param  object $order Order object.
	 *
	 * @return void
	 */
	public function generate_boleto_data( $order ) {
		// Boleto data.
		$data['nosso_numero']       = apply_filters( 'wcboleto_our_number', $order->id );
		$data['numero_documento']   = apply_filters( 'wcboleto_document_number', $order->id );
		$data['data_vencimento']    = date( 'd/m/Y', time() + ( $this->boleto_time * 86400 ) );
		$data['data_documento']     = date( 'd/m/Y' );
		$data['data_processamento'] = date( 'd/m/Y' );
		$data['valor_boleto']       = number_format( $order->order_total, 2, ',', '' );

		update_post_meta( $order->id, 'wc_boleto_data', $data );
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param  object $order         Order object.
	 * @param  bool   $sent_to_admin Send to admin.
	 *
	 * @return string                Billet instructions.
	 */
	function email_instructions( $order, $sent_to_admin ) {
		if ( $sent_to_admin || 'on-hold' !== $order->status || 'boleto' !== $order->payment_method ) {
			return;
		}

		$html = '<h2>' . __( 'Payment', 'wcboleto' ) . '</h2>';

		$html .= '<p class="order_details">';

		$message = sprintf( __( '%sAttention!%s You will not get the ticket by Correios.', 'wcboleto' ), '<strong>', '</strong>' ) . '<br />';
		$message .= __( 'Please click the following button and pay the Boleto in your Internet Banking.', 'wcboleto' ) . '<br />';
		$message .= __( 'If you prefer, print and pay at any bank branch or home lottery.', 'wcboleto' ) . '<br />';

		$html .= apply_filters( 'wcboleto_email_instructions', $message );

		$html .= '<br />' . sprintf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'ref', $order->order_key, get_permalink( get_page_by_path( 'boleto' ) ) ), __( 'Pay the Boleto &rarr;', 'wcboleto' ) ) . '<br />';

		$html .= '<strong style="font-size: 0.8em">' . sprintf( __( 'Validity of the Boleto: %s.', 'wcboleto' ), date( 'd/m/Y', time() + ( $this->boleto_time * 86400 ) ) ) . '</strong>';

		$html .= '</p>';

		echo $html;
	}

	/**
	 * Gets the admin url.
	 *
	 * @return string
	 */
	protected function admin_url() {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
			return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_boleto_gateway' );
		}

		return admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Boleto_Gateway' );
	}

	/**
	 * Adds error message when an unsupported currency is used.
	 *
	 * @return string
	 */
	public function currency_not_supported_message() {
		echo '<div class="error"><p><strong>' . __( 'Boleto Disabled', 'wcboleto' ) . '</strong>: ' . sprintf( __( 'Currency <code>%s</code> is not supported. Works only with <code>BRL</code> (Brazilian Real).', 'wcboleto' ), get_woocommerce_currency() ) . '</p></div>';
	}

}
