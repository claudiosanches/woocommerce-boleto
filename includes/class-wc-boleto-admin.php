<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Boleto Admin.
 */
class WC_Boleto_Admin {

	/**
	 * Initialize the admin.
	 */
	public function __construct() {
		// Add metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

		// Save Metabox.
		add_action( 'save_post', array( $this, 'save' ) );

		// Update.
		add_action( 'admin_init', array( $this, 'update' ), 5 );

		// Load scripts in gateway settings page.
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Register boleto metabox.
	 */
	public function register_metabox() {
		add_meta_box(
			'woocommerce-boleto',
			__( 'Banking Ticket', 'woocommerce-boleto' ),
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Banking Ticket metabox content.
	 *
	 * @param  object $post order_shop data.
	 *
	 * @return string       Metabox HTML.
	 */
	public function metabox_content( $post ) {
		// Get order data.
		$order = new WC_Order( $post->ID );

		// Use nonce for verification.
		wp_nonce_field( basename( __FILE__ ), 'wcboleto_metabox_nonce' );

		if ( 'boleto' == $order->payment_method ) {
			$boleto_data = get_post_meta( $post->ID, 'wc_boleto_data', true );

			// Save the ticket data if don't have.
			if ( ! isset( $boleto_data['data_vencimento'] ) ) {
				$settings                   = get_option( 'woocommerce_boleto_settings', array() );
				$boleto_time                = isset( $settings['boleto_time'] ) ? absint( $settings['boleto_time'] ) : 5;
				$data                       = array();
				$data['nosso_numero']       = apply_filters( 'wcboleto_our_number', $order->id );
				$data['numero_documento']   = apply_filters( 'wcboleto_document_number', $order->id );
				$data['data_vencimento']    = date( 'd/m/Y', time() + ( $boleto_time * 86400 ) );
				$data['data_documento']     = date( 'd/m/Y' );
				$data['data_processamento'] = date( 'd/m/Y' );

				update_post_meta( $post->ID, 'wc_boleto_data', $data );

				$boleto_data['data_vencimento'] = $data['data_vencimento'];
			}

			$html = '<p><strong>' . __( 'Expiration date:', 'woocommerce-boleto' ) . '</strong> ' . $boleto_data['data_vencimento'] . '</p>';
			$html .= '<p><strong>' . __( 'URL:', 'woocommerce-boleto' ) . '</strong> <a target="_blank" href="' . esc_url( wc_boleto_get_boleto_url( $order->order_key ) ) . '">' . __( 'View boleto', 'woocommerce-boleto' ) . '</a></p>';

			$html .= '<p style="border-top: 1px solid #ccc;"></p>';

			$html .= '<label for="wcboleto_expiration_date">' . __( 'Set new expiration data:', 'woocommerce-boleto' ) . '</label><br />';
			$html .= '<input type="text" id="wcboleto_expiration_date" name="wcboleto_expiration_date" style="width: 100%;" />';
			$html .= '<span class="description">' . __( 'Configuring a new expiration date the boleto is resent to the client.', 'woocommerce-boleto' ) . '</span>';

		} else {
			$html = '<p>' . __( 'This purchase was not paid with Ticket.', 'woocommerce-boleto' ) . '</p>';
			$html .= '<style>#woocommerce-boleto.postbox {display: none;}</style>';
		}

		echo $html;
	}

	/**
	 * Save metabox data.
	 *
	 * @param int $post_id Current post type ID.
	 */
	public function save( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['wcboleto_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['wcboleto_metabox_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Verify if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check permissions.
		if ( 'shop_order' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['wcboleto_expiration_date'] ) && ! empty( $_POST['wcboleto_expiration_date'] ) ) {
			// Gets ticket data.
			$boleto_data = get_post_meta( $post_id, 'wc_boleto_data', true );
			$boleto_data['data_vencimento'] = sanitize_text_field( $_POST['wcboleto_expiration_date'] );

			// Update ticket data.
			update_post_meta( $post_id, 'wc_boleto_data', $boleto_data );

			// Gets order data.
			$order = new WC_Order( $post_id );

			// Add order note.
			$order->add_order_note( sprintf( __( 'Expiration date updated to: %s', 'woocommerce-boleto' ), $boleto_data['data_vencimento'] ) );

			// Send email notification.
			$this->email_notification( $order, $boleto_data['data_vencimento'] );
		}
	}

	/**
	 * New expiration date email notification.
	 *
	 * @param object $order           Order data.
	 * @param string $expiration_date Ticket expiration date.
	 */
	protected function email_notification( $order, $expiration_date ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$mailer = WC()->mailer();
		} else {
			global $woocommerce;
			$mailer = $woocommerce->mailer();
		}

		$subject = sprintf( __( 'New expiration date for the boleto your order %s', 'woocommerce-boleto' ), $order->get_order_number() );

		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		// Body message.
		$main_message = '<p>' . sprintf( __( 'The expiration date of your boleto was updated to: %s', 'woocommerce-boleto' ), '<code>' . $expiration_date . '</code>' ) . '</p>';
		$main_message .= '<p>' . sprintf( '<a class="button" href="%s" target="_blank">%s</a>', esc_url( wc_boleto_get_boleto_url( $order->order_key ) ), __( 'Pay the Ticket &rarr;', 'woocommerce-boleto' ) ) . '</p>';

		// Sets message template.
		$message = $mailer->wrap_message( __( 'New expiration date for your boleto', 'woocommerce-boleto' ), $main_message );

		// Send email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}

	/**
	 * Admin scripts.
	 *
	 * @param string $hook Page slug.
	 */
	public function scripts( $hook ) {
		if ( in_array( $hook, array( 'woocommerce_page_wc-settings', 'woocommerce_page_woocommerce_settings' ) ) && ( isset( $_GET['section'] ) && 'wc_boleto_gateway' == strtolower( $_GET['section'] ) ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'wc-boleto-admin', plugins_url( 'assets/js/admin' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Boleto::VERSION, true );
		}
	}

	/**
	 * Performance an update to all options.
	 */
	public function update() {
		$db_version = get_option( 'woocommerce_boleto_db_version' );
		$version    = WC_Boleto::VERSION;

		// Update to 1.2.2.
		if ( version_compare( $db_version, '1.2.2', '<' ) ) {
			// Delete boleto page.
			$boleto_post = get_page_by_path( 'boleto' );
			if ( $boleto_post ) {
				wp_delete_post( $boleto_post->ID, true );
			}

			// Flush urls.
			WC_Boleto::activate();
		}

		// Update the db version.
		if ( $db_version != $version ) {
			update_option( 'woocommerce_boleto_db_version', $version );
		}
	}
}

new WC_Boleto_Admin();
