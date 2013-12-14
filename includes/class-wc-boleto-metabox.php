<?php
/**
 * Boleto Metabox.
 *
 * @since 1.0.0
 */
class WC_Boleto_Metabox {

	public function __construct() {

		// Add metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

		// Save Metabox.
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Register boleto metabox.
	 *
	 * @return void
	 */
	public function register_metabox() {
		add_meta_box(
			'wcboleto',
			__( 'Boleto', 'wcboleto' ),
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Boleto metabox content.
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

		$html = '<p>' . __( 'This purchase was not paid with Boleto.', 'wcboleto' ) . '</p>';
		$html .= '<style>#wcboleto.postbox {display: none;}</style>';

		if ( 'boleto' == $order->payment_method ) {
			$boleto_data = get_post_meta( $post->ID, 'wc_boleto_data', true );

			if ( isset( $boleto_data['data_vencimento'] ) ) {
				$html = '<p><strong>' . __( 'Expiration date:', 'wcboleto' ) . '</strong> ' . $boleto_data['data_vencimento'] . '</p>';
				$html .= '<p><strong>' . __( 'URL:', 'wcboleto' ) . '</strong> <a target="_blank" href="' . add_query_arg( 'ref', $order->order_key, get_permalink( get_page_by_path( 'boleto' ) ) ) . '">' . __( 'View boleto', 'wcboleto' ) . '</a></p>';

				$html .= '<p style="border-top: 1px solid #ccc;"></p>';

				$html .= '<label for="wcboleto_expiration_date">' . __( 'Set new expiration data:', 'wcboleto' ) . '</label><br />';
				$html .= '<input type="text" id="wcboleto_expiration_date" name="wcboleto_expiration_date" style="width: 100%;" />';
				$html .= '<span class="description">' . __( 'Configuring a new expiration date the boleto is resent to the client.', 'wcboleto' ) . '</span>';
			}
		}

		echo $html;
	}

	/**
	 * Save metabox data.
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
		if ( 'shop_order' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
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
			$order->add_order_note( sprintf( __( 'Expiration date updated to: %s', 'wcboleto' ), $boleto_data['data_vencimento'] ) );

			// Send email notification.
			$this->email_notification( $order, $boleto_data['data_vencimento'] );
		}
	}

	/**
	 * New expiration date email notification.
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

		$subject = sprintf( __( 'New expiration date for the boleto your order %s', 'wcboleto' ), $order->get_order_number() );

		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		// Body message.
		$main_message = '<p>' . sprintf( __( 'The expiration date of your boleto was updated to: %s', 'wcboleto' ), '<code>' . $expiration_date . '</code>' ) . '</p>';
		$main_message .= '<p>' . sprintf( '<a class="button" href="%s" target="_blank">%s</a>', add_query_arg( 'ref', $order->order_key, get_permalink( get_page_by_path( 'boleto' ) ) ), __( 'Pay the Boleto &rarr;', 'wcboleto' ) ) . '</p>';

		// Sets message template.
		$message = $mailer->wrap_message( __( 'New expiration date for your boleto', 'wcboleto' ), $main_message );

		// Send email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}
}
