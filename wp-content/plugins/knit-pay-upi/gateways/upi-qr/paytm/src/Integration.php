<?php

namespace KnitPayUPI\Gateways\UpiQR\Paytm;

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use KnitPay\Gateways\UpiQR\Integration as UPI_Integration;

/**
 * Title: PhonePe for Business Integration
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.0.0
 * @since   1.0.0
 */
class Integration extends UPI_Integration {
	/**
	 * Construct UPI QR integration.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'          => 'paytm-business',
				'name'        => 'Paytm for Business (UPI/QR)',
				'product_url' => 'https://play.google.com/store/apps/details?id=com.paytm.business',
				'provider'    => 'paytm-business',
			]
		);

		parent::__construct( $args );
	}
	
	public function get_config( $post_id ) {
		$config = new Config();
		$config->copy_properties( parent::get_config( $post_id ) );
		
		$config->mid             = $this->get_meta( $post_id, 'upi_qr_paytm_mid' );
		$config->hide_mobile_qr  = false;
		$config->hide_pay_button = true;

		return $config;
	}

	/**
	 * Get settings fields.
	 *
	 * @return array
	 */
	public function get_about_settings_fields( $fields ) {
		$fields = $this->get_pro_about_settings_fields( $fields );

		// Terms and Conditions.
		$fields[] = [
			'section'  => 'general',
			'type'     => 'custom',
			'title'    => 'Paytm for Business Terms and Conditions',
			'callback' => function () {
				echo 'Use this integration only if you have read and agree with the Terms and Conditions of Paytm for Business.';
			},
		];
		
		return $fields;
	}

	public function get_setup_settings_fields( $fields ) {
		// MID.
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_upi_qr_paytm_mid',
			'title'    => __( 'Paytm MID', 'knit-pay-lang' ),
			'type'     => 'text',
			'classes'  => [ 'regular-text', 'code' ],
			'required' => true,
		];
		
		$fields = parent::get_setup_settings_fields( $fields );
		
		unset( $fields['payment_instruction'] );
		unset( $fields['mobile_payment_instruction'] );
		unset( $fields['payment_success_status'] );
		unset( $fields['transaction_id_field'] );
		unset( $fields['hide_mobile_qr'] );
		unset( $fields['hide_pay_button'] );
		
		return $fields;
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $config_id ) {
		return new Gateway( $this->get_config( $config_id ) );
	}
}
