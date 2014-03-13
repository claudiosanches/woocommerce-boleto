<?php
/**
 * Boleto Admin.
 *
 * @since 1.0.0
 */
class WC_Boleto_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since  1.2.0
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the admin.
	 *
	 * @since 1.2.0
	 */
	private function __construct() {
		$this->plugin_slug = WC_Boleto::get_plugin_slug();

		// Add metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

		// Save Metabox.
		add_action( 'save_post', array( $this, 'save' ) );
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
	 * Register boleto metabox.
	 *
	 * @since  1.2.0
	 *
	 * @return void
	 */
	public function register_metabox() {
		add_meta_box(
			$this->plugin_slug,
			__( 'Boleto', $this->plugin_slug ),
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Boleto metabox content.
	 *
	 * @since  1.2.0
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

			if ( isset( $boleto_data['data_vencimento'] ) ) {
				$html = '<p><strong>' . __( 'Expiration date:', $this->plugin_slug ) . '</strong> ' . $boleto_data['data_vencimento'] . '</p>';
				$html .= '<p><strong>' . __( 'URL:', $this->plugin_slug ) . '</strong> <a target="_blank" href="' . WC_Boleto::get_boleto_url( $order->order_key ) . '">' . __( 'View boleto', $this->plugin_slug ) . '</a></p>';

				$html .= '<p style="border-top: 1px solid #ccc;"></p>';

				$html .= '<label for="wcboleto_expiration_date">' . __( 'Set new expiration data:', $this->plugin_slug ) . '</label><br />';
				$html .= '<input type="text" id="wcboleto_expiration_date" name="wcboleto_expiration_date" style="width: 100%;" />';
				$html .= '<span class="description">' . __( 'Configuring a new expiration date the boleto is resent to the client.', $this->plugin_slug ) . '</span>';
			}
		} else {
			$html = '<p>' . __( 'This purchase was not paid with Boleto.', $this->plugin_slug ) . '</p>';
			$html .= '<style>#wcboleto.postbox {display: none;}</style>';
		}

		echo $html;
	}

	/**
	 * Save metabox data.
	 *
	 * @since  1.2.0
	 *
	 * @param  int $post_id Current post type ID.
	 *
	 * @return void
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
			// Gets boleto data.
			$boleto_data = get_post_meta( $post_id, 'wc_boleto_data', true );
			$boleto_data['data_vencimento'] = sanitize_text_field( $_POST['wcboleto_expiration_date'] );

			// Update boleto data.
			update_post_meta( $post_id, 'wc_boleto_data', $boleto_data );

			// Gets order data.
			$order = new WC_Order( $post_id );

			// Add order note.
			$order->add_order_note( sprintf( __( 'Expiration date updated to: %s', $this->plugin_slug ), $boleto_data['data_vencimento'] ) );

			// Send email notification.
			$this->email_notification( $order, $boleto_data['data_vencimento'] );
		}
	}

	/**
	 * New expiration date email notification.
	 *
	 * @since  1.2.0
	 *
	 * @param  object $order           Order data.
	 * @param  string $expiration_date Boleto expiration date.
	 *
	 * @return void
	 */
	protected function email_notification( $order, $expiration_date ) {
		if ( function_exists( 'WC' ) ) {
			$mailer = WC()->mailer();
		} else {
			global $woocommerce;
			$mailer = $woocommerce->mailer();
		}

		$subject = sprintf( __( 'New expiration date for the boleto your order %s', $this->plugin_slug ), $order->get_order_number() );

		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		// Body message.
		$main_message = '<p>' . sprintf( __( 'The expiration date of your boleto was updated to: %s', $this->plugin_slug ), '<code>' . $expiration_date . '</code>' ) . '</p>';
		$main_message .= '<p>' . sprintf( '<a class="button" href="%s" target="_blank">%s</a>', WC_Boleto::get_boleto_url( $order->order_key ), __( 'Pay the Boleto &rarr;', $this->plugin_slug ) ) . '</p>';

		// Sets message template.
		$message = $mailer->wrap_message( __( 'New expiration date for your boleto', $this->plugin_slug ), $main_message );

		// Send email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}
}
