<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Boleto Gateway Class.
 *
 * Built the Boleto method.
 */
class WC_Boleto_Gateway extends WC_Payment_Gateway {

	/**
	 * Initialize the gateway actions.
	 */
	public function __construct() {
		$this->id                 = 'boleto';
		$this->icon               = apply_filters( 'wcboleto_icon', plugins_url( 'assets/images/boleto.png', plugin_dir_path( __FILE__ ) ) );
		$this->has_fields         = false;
		$this->method_title       = __( 'Banking Ticket', 'woocommerce-boleto' );
		$this->method_description = __( 'Enables payments via Banking Ticket.', 'woocommerce-boleto' );

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
		include 'views/html-admin-page.php';
	}

	/**
	 * Gateway options.
	 */
	public function init_form_fields() {
		$shop_name = get_bloginfo( 'name' );

		$first = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-boleto' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Banking Ticket', 'woocommerce-boleto' ),
				'default' => 'yes'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-boleto' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => __( 'Banking Ticket', 'woocommerce-boleto' )
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-boleto' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => __( 'Pay with Banking Ticket', 'woocommerce-boleto' )
			),
			'boleto_details' => array(
				'title' => __( 'Ticket Details', 'woocommerce-boleto' ),
				'type'  => 'title'
			),
			'boleto_time' => array(
				'title'       => __( 'Deadline to pay the Ticket', 'woocommerce-boleto' ),
				'type'        => 'text',
				'description' => __( 'Number of days to pay.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => 5
			),
			'boleto_logo' => array(
				'title'       => __( 'Ticket Logo', 'woocommerce-boleto' ),
				'type'        => 'text',
				'description' => __( 'Logo with 147px x 46px.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => plugins_url( 'assets/images/logo_empresa.png', plugin_dir_path( __FILE__ ) )
			),
			'bank_details' => array(
				'title' => __( 'Bank Details', 'woocommerce-boleto' ),
				'type'  => 'title'
			),
			'bank' => array(
				'title'       => __( 'Bank', 'woocommerce-boleto' ),
				'type'        => 'select',
				'desc_tip'    => true,
				'description' => __( 'Choose the bank for Ticket.', 'woocommerce-boleto' ),
				'default'     => '0',
				'options'     => array(
					'0'          => '--',
					'bb'         => __( 'Banco do Brasil', 'woocommerce-boleto' ),
					'bradesco'   => __( 'Bradesco', 'woocommerce-boleto' ),
					'cef'        => __( 'Caixa Economica Federal - SR (SICOB)', 'woocommerce-boleto' ),
					'cef_sigcb'  => __( 'Caixa Economica Federal - SIGCB', 'woocommerce-boleto' ),
					'cef_sinco'  => __( 'Caixa Economica Federal - SINCO', 'woocommerce-boleto' ),
					'hsbc'       => __( 'HSBC', 'woocommerce-boleto' ),
					'itau'       => __( 'Itau', 'woocommerce-boleto' ),
					'nossacaixa' => __( 'Nossa Caixa', 'woocommerce-boleto' ),
					'real'       => __( 'Real', 'woocommerce-boleto' ),
					'santander'  => __( 'Santander', 'woocommerce-boleto' ),
					'unibanco'   => __( 'Unibanco', 'woocommerce-boleto' ),
					'bancoob'    => __( 'Bancoob', 'woocommerce-boleto')
				)
			)
		);

		$last = array(
			'extra_details' => array(
				'title' => __( 'Optional Data', 'woocommerce-boleto' ),
				'type'  => 'title'
			),
			'quantidade' => array(
				'title'       => __( 'Quantity', 'woocommerce-boleto' ),
				'type'        => 'text'
			),
			'valor_unitario' => array(
				'title'       => __( 'Unitary value', 'woocommerce-boleto' ),
				'type'        => 'text'
			),
			'aceite' => array(
				'title'       => __( 'Acceptance', 'woocommerce-boleto' ),
				'type'        => 'text'
			),
			'especie' => array(
				'title'       => __( 'Currency', 'woocommerce-boleto' ),
				'type'        => 'text',
				'default'     => 'R$'
			),
			'especie_doc' => array(
				'title'       => __( 'Kind of document', 'woocommerce-boleto' ),
				'type'        => 'text'
			),
			'especie' => array(
				'title'       => __( 'Currency', 'woocommerce-boleto' ),
				'type'        => 'text',
				'default'     => 'R$'
			),
			'demonstrative' => array(
				'title' => __( 'Demonstrative', 'woocommerce-boleto' ),
				'type'  => 'title'
			),
			'demonstrativo1' => array(
				'title'       => __( 'Line 1', 'woocommerce-boleto' ),
				'type'        => 'text',
				'description' => __( 'Use [number] to show the Order ID.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => sprintf( __( 'Payment for purchase in %s', 'woocommerce-boleto' ), $shop_name )
			),
			'demonstrativo2' => array(
				'title'       => __( 'Line 2', 'woocommerce-boleto' ),
				'type'        => 'text',
				'description' => __( 'Use [number] to show the Order ID.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => __( 'Payment referred to the order [number]', 'woocommerce-boleto' )
			),
			'demonstrativo3' => array(
				'title'       => __( 'Line 3', 'woocommerce-boleto' ),
				'type'        => 'text',
				'description' => __( 'Use [number] to show the Order ID.', 'woocommerce-boleto' ),
				'desc_tip'    => true,
				'default'     => $shop_name . ' - ' . home_url()
			),
			'instructions' => array(
				'title' => __( 'Instructions', 'woocommerce-boleto' ),
				'type'  => 'title'
			),
			'instrucoes1' => array(
				'title'       => __( 'Line 1', 'woocommerce-boleto' ),
				'type'        => 'text',
				'default'     => __( '- Mr. Cash, charge a fine of 2% after maturity', 'woocommerce-boleto' )
			),
			'instrucoes2' => array(
				'title'       => __( 'Line 2', 'woocommerce-boleto' ),
				'type'        => 'text',
				'default'     => __( '- Receive up to 10 days past due', 'woocommerce-boleto' )
			),
			'instrucoes3' => array(
				'title'       => __( 'Line 3', 'woocommerce-boleto' ),
				'type'        => 'text',
				'default'     => sprintf( __( '- For questions please contact us: %s', 'woocommerce-boleto' ), get_option( 'woocommerce_email_from_address' ) )
			),
			'instrucoes4' => array(
				'title'       => __( 'Line 4', 'woocommerce-boleto' ),
				'type'        => 'text',
				'default'     => ''
			),
			'shop_details' => array(
				'title' => __( 'Shop Details', 'woocommerce-boleto' ),
				'type'  => 'title'
			),
			'cpf_cnpj' => array(
				'title'       => __( 'CPF/CNPJ', 'woocommerce-boleto' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Document number.', 'woocommerce-boleto' ),
			),
			'endereco' => array(
				'title'       => __( 'Address', 'woocommerce-boleto' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Shop Address.', 'woocommerce-boleto' ),
			),
			'cidade_uf' => array(
				'title'       => __( 'City/State', 'woocommerce-boleto' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Example <code>S&atilde;o Paulo/SP</code>.', 'woocommerce-boleto' ),
			),
			'cedente' => array(
				'title' => __( 'Corporate Name', 'woocommerce-boleto' ),
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
			case 'bb' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
					),
					'convenio' => array(
						'title'       => __( 'Agreement number', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agreements with 6, 7 or 8 digits.', 'woocommerce-boleto' )
					),
					'contrato' => array(
						'title' => __( 'Contract number', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title' => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'variacao_carteira' => array(
						'title'       => __( 'Wallet variation (optional)', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Wallet variation with dash.', 'woocommerce-boleto' )
					),
					'formatacao_convenio' => array(
						'title'       => __( 'Agreement format', 'woocommerce-boleto' ),
						'type'        => 'select',
						'default'     => '6',
						'options'     => array(
							'6' => __( 'Agreement with 6 digits', 'woocommerce-boleto' ),
							'7' => __( 'Agreement with 7 dígitos', 'woocommerce-boleto' ),
							'8' => __( 'Agreement with 8 dígitos', 'woocommerce-boleto' ),
						)
					),
					'formatacao_nosso_numero' => array(
						'title'       => __( 'Our number formatting', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Used only for agreement with 6 digits (enter 1 for Our Number is up to 5 digits or 2 for option up to 17 digits).', 'woocommerce-boleto' )
					)
				);
				break;
			case 'bancoob' :
				$fields = array(
						'agencia' => array(
							'title'       => __( 'Agency', 'woocommerce-boleto' ),
							'type'        => 'text',
							'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
						),
						'conta' => array(
							'title'       => __( 'Account', 'woocommerce-boleto' ),
							'type'        => 'text',
							'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
						),
						'convenio' => array(
							'title'       => __( 'Agreement number', 'woocommerce-boleto' ),
							'type'        => 'text',
							'description' => __( 'Agreements with 6, 7 or 8 digits.', 'woocommerce-boleto' )
						),
						'carteira' => array(
							'title' => __( 'Wallet code', 'woocommerce-boleto' ),
							'type'  => 'text'
						),
					);
				break;
			case 'bradesco' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' ),
					),
					'agencia_dv' => array(
						'title' => __( 'Agency digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' ),
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit (only numbers).', 'woocommerce-boleto' ),
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'   => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'    => 'select',
						'default' => '03',
						'options' => array(
							'03' => '03',
							'06' => '06',
							'09' => '09',
							'25' => '25'
						)
					)
				);
				break;
			case 'cef' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit, use only numbers', 'woocommerce-boleto' )
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'        => 'select',
						'description' => __( 'Confirm this information with your manager.', 'woocommerce-boleto' ),
						'default'     => 'SR',
						'options'     => array(
							'SR' => __( 'Without registry', 'woocommerce-boleto' ),
							'CR' => __( 'With registry', 'woocommerce-boleto' )
						)
					),
					'inicio_nosso_numero' => array(
						'title'       => __( 'Beginning of the Our Number', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Use <code>80, 81 or 82</code> for <strong>Without registry</strong> or <code>90</code> for <strong>With registry</strong>. Confirm this information with your manager.', 'woocommerce-boleto' ),
						'default'     => '80'
					)
				);
				break;
			case 'cef_sigcb' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account with 6 digits, use only numbers.', 'woocommerce-boleto' )
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'        => 'select',
						'description' => __( 'Confirm this information with your manager.', 'woocommerce-boleto' ),
						'default'     => 'SR',
						'options'     => array(
							'SR' => __( 'Without registry', 'woocommerce-boleto' ),
							'CR' => __( 'With registry', 'woocommerce-boleto' )
						)
					)
				);
				break;
			case 'cef_sinco' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' ),
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit, use only numbers', 'woocommerce-boleto' )
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'        => 'select',
						'description' => __( 'Confirm this information with your manager.', 'woocommerce-boleto' ),
						'default'     => 'SR',
						'options'     => array(
							'SR' => __( 'Without registry', 'woocommerce-boleto' ),
							'CR' => __( 'With registry', 'woocommerce-boleto' )
						)
					),
				);
				break;
			case 'hsbc' :
				$fields = array(
					'codigo_cedente' => array(
						'title'       => __( 'Transferor code', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor code with only 7 digits.', 'woocommerce-boleto' )
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'        => 'select',
						'description' => __( 'Accepts only CNR.', 'woocommerce-boleto' ),
						'default'     => 'CNR',
						'options'     => array(
							'CNR' => 'CNR'
						)
					)
				);
				break;
			case 'itau' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number.', 'woocommerce-boleto' ),
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'   => __( 'Wallet code', 'woocommerce-boleto' ),
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
			case 'nossacaixa' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta_cedente' => array(
						'title'       => __( 'Transferor account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Transferor account without digit and with only 6 numbers.', 'woocommerce-boleto' )
					),
					'conta_cedente_dv' => array(
						'title' => __( 'Transferor account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title'   => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'    => 'select',
						'default' => '1',
						'options' => array(
							'1' => __( 'Simple Billing (1)', 'woocommerce-boleto' ),
							'5' => __( 'Direct Billing (5)', 'woocommerce-boleto' )
						)
					),
					'modalidade_conta' => array(
						'title'       => __( 'Account modality', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account modality with two positions (example: 04).', 'woocommerce-boleto' )
					)
				);
				break;
			case 'real' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
					),
					'carteira' => array(
						'title' => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'  => 'text'
					)
				);
				break;
			case 'santander' :
				$fields = array(
					'codigo_cliente' => array(
						'title'       => __( 'Customer code', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Customer code (PSK) with only 7 digits.', 'woocommerce-boleto' )
					),
					'ponto_venda' => array(
						'title'       => __( 'Sale point (Agency)', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number.', 'woocommerce-boleto' )
					),
					'carteira' => array(
						'title'       => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Simple collection - Without registration.', 'woocommerce-boleto' )
					),
					'carteira_descricao' => array(
						'title'   => __( 'Wallet description', 'woocommerce-boleto' ),
						'type'    => 'text',
						'default' => 'COBRANÇA SIMPLES - CSR'
					)
				);
				break;
			case 'unibanco' :
				$fields = array(
					'agencia' => array(
						'title'       => __( 'Agency', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Agency number without digit.', 'woocommerce-boleto' )
					),
					'conta' => array(
						'title'       => __( 'Account', 'woocommerce-boleto' ),
						'type'        => 'text',
						'description' => __( 'Account number without digit.', 'woocommerce-boleto' )
					),
					'conta_dv' => array(
						'title' => __( 'Account digit', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'codigo_cliente' => array(
						'title' => __( 'Customer code', 'woocommerce-boleto' ),
						'type'  => 'text'
					),
					'carteira' => array(
						'title' => __( 'Wallet code', 'woocommerce-boleto' ),
						'type'  => 'text'
					)
				);
				break;

			default :
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

		// Mark as on-hold (we're awaiting the ticket).
		$order->update_status( 'on-hold', __( 'Awaiting boleto payment', 'woocommerce-boleto' ) );

		// Generates ticket data.
		$this->generate_boleto_data( $order );

		// Reduce stock levels.
		$order->reduce_order_stock();

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			WC()->cart->empty_cart();

			$url = $order->get_checkout_order_received_url();
		} else {
			global $woocommerce;

			$woocommerce->cart->empty_cart();

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
		$html .= sprintf( '<a class="button" href="%s" target="_blank" style="display: block !important; visibility: visible !important;">%s</a>', esc_url( wc_boleto_get_boleto_url( $_GET['key'] ) ), __( 'Pay the Ticket &rarr;', 'woocommerce-boleto' ) );

		$message = sprintf( __( '%sAttention!%s You will not get the ticket by Correios.', 'woocommerce-boleto' ), '<strong>', '</strong>' ) . '<br />';
		$message .= __( 'Please click the following button and pay the Ticket in your Internet Banking.', 'woocommerce-boleto' ) . '<br />';
		$message .= __( 'If you prefer, print and pay at any bank branch or lottery retailer.', 'woocommerce-boleto' ) . '<br />';

		$html .= apply_filters( 'wcboleto_thankyou_page_message', $message );

		$html .= '<strong style="display: block; margin-top: 15px; font-size: 0.8em">' . sprintf( __( 'Validity of the Ticket: %s.', 'woocommerce-boleto' ), date( 'd/m/Y', time() + ( absint( $this->boleto_time ) * 86400 ) ) ) . '</strong>';

		$html .= '</div>';

		echo $html;
	}

	/**
	 * Generate ticket data.
	 *
	 * @param  object $order Order object.
	 */
	public function generate_boleto_data( $order ) {
		// Ticket data.
		$data                       = array();
		$data['nosso_numero']       = apply_filters( 'wcboleto_our_number', $order->id );
		$data['numero_documento']   = apply_filters( 'wcboleto_document_number', $order->id );
		$data['data_vencimento']    = date( 'd/m/Y', time() + ( absint( $this->boleto_time ) * 86400 ) );
		$data['data_documento']     = date( 'd/m/Y' );
		$data['data_processamento'] = date( 'd/m/Y' );

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

		$html = '<h2>' . __( 'Payment', 'woocommerce-boleto' ) . '</h2>';

		$html .= '<p class="order_details">';

		$message = sprintf( __( '%sAttention!%s You will not get the ticket by Correios.', 'woocommerce-boleto' ), '<strong>', '</strong>' ) . '<br />';
		$message .= __( 'Please click the following button and pay the Ticket in your Internet Banking.', 'woocommerce-boleto' ) . '<br />';
		$message .= __( 'If you prefer, print and pay at any bank branch or lottery retailer.', 'woocommerce-boleto' ) . '<br />';

		$html .= apply_filters( 'wcboleto_email_instructions', $message );

		$html .= '<br />' . sprintf( '<a class="button" href="%s" target="_blank">%s</a>', esc_url( wc_boleto_get_boleto_url( $order->order_key ) ), __( 'Pay the Ticket &rarr;', 'woocommerce-boleto' ) ) . '<br />';

		$html .= '<strong style="font-size: 0.8em">' . sprintf( __( 'Validity of the Ticket: %s.', 'woocommerce-boleto' ), date( 'd/m/Y', time() + ( absint( $this->boleto_time ) * 86400 ) ) ) . '</strong>';

		$html .= '</p>';

		echo $html;
	}
}
