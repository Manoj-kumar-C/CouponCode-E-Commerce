<?php

namespace KnitPayUPI\Gateways\UpiQR\PhonePe;

use KnitPay\Utils;
use KnitPay\Gateways\UpiQR\Integration as UPI_Integration;
use Pronamic\WordPress\Html\Element;
use Exception;

/**
 * Title: PhonePe Business Integration
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.2.0.0
 * @since   1.2.0.0
 */
class Integration extends UPI_Integration {
	private $config;

	/**
	 * Construct UPI QR integration.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'          => 'phonepe-business',
				'name'        => 'PhonePe Business (UPI/QR)',
				'product_url' => 'https://play.google.com/store/apps/details?id=com.phonepe.app.business',
				'provider'    => 'phonepe-business',
			]
		);
		
		parent::__construct( $args );
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
			'title'    => 'PhonePe Business Terms and Conditions',
			'callback' => function () {
				echo 'Use this integration only if you have read and agree with the Terms and Conditions of PhonePe Business.';
			},
		];
		
		return $fields;
	}
	
	public function get_setup_settings_fields( $fields ) {
		$fields = $this->get_login_logout_fields( $fields );

		if ( isset( $this->config ) && '' !== $this->config->refresh_token ) {
			$fields = parent::get_setup_settings_fields( $fields );

			unset( $fields['payment_instruction'] );
			unset( $fields['mobile_payment_instruction'] );
			unset( $fields['payment_success_status'] );
			unset( $fields['transaction_id_field'] );
		}
		
		return $fields;
	}
	
	private function get_login_logout_fields( $fields ) {      
		// Callback URL.
		$config_id = Utils::get_gateway_config_id();
		if ( ! empty( $config_id ) ) {
			$this->config = $this->get_config( $config_id );
		}
		
		if ( ! isset( $this->config ) || '' !== $this->config->refresh_token ) {
			try {
				$api             = new API( $this->config );
				$group_info_list = $api->get_group_info_list();
				$account_array   = [];
				foreach ( $group_info_list as $key => $value ) {
					$account_array[ $value->userGroupNamespace->groupValue ] = $value->merchantName . " ({$value->userGroupNamespace->groupValue})";
				}
				$fields[] = [
					'section'  => 'general',
					'meta_key' => '_pronamic_gateway_upi_qr_phonepe_group_value',
					'title'    => __( 'Group Value', 'knit-pay-upi' ),
					'type'     => 'select',
					'options'  => $account_array,
				];
			} catch ( Exception $e ) {

			}

			// TODO Show logout button
			return $fields;
		}
		
		// Registered Phone.
		$fields[] = [
			'section'  => 'general',
			'title'    => __( 'Supervisor Phone Number', 'knit-pay-upi' ),
			'type'     => 'description',
			'callback' => [ $this, 'field_send_phone_otp' ],
			'tooltip'  => __( 'Phone number of PhonePe Business Supervisor', 'knit-pay-upi' ),
		];
		
		// Submit OTP.
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_upi_qr_phonepe_otp',
			'title'    => __( 'OTP', 'knit-pay-upi' ),
			'type'     => 'description',
			'callback' => [ $this, 'field_submit_otp' ],
		];
		
		// Load admin.js Javascript
		$fields[] = [
			'section'  => 'general',
			'type'     => 'custom',
			'callback' => function () {
				echo '<script src="' . plugins_url( '', __FILE__ ) . '/js/admin.js"></script>';},
		];
		
		return $fields;
		
	}
	
	public function get_config( $post_id ) {
		$config = new Config();
		$config->copy_properties( parent::get_config( $post_id ) );
		
		$config->refresh_token      = $this->get_meta( $post_id, 'upi_qr_phonepe_refresh_token' );
		$config->token              = $this->get_meta( $post_id, 'upi_qr_phonepe_token' );
		$config->expires_at         = $this->get_meta( $post_id, 'upi_qr_phonepe_expires_at' );
		$config->device_fingerprint = $this->get_meta( $post_id, 'upi_qr_phonepe_device_fingerprint' );
		$config->fingerprint        = $this->get_meta( $post_id, 'upi_qr_phonepe_fingerprint' );
		$config->phone_number       = $this->get_meta( $post_id, 'upi_qr_phonepe_phone_number' );
		$config->user_group_id      = $this->get_meta( $post_id, 'upi_qr_phonepe_user_group_id' );
		$config->group_value        = $this->get_meta( $post_id, 'upi_qr_phonepe_group_value' );
		
		$config->config_id = $post_id;
		
		return $config;
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
	
	/**
	 * Save post.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_post( $config_id ) {
		parent::save_post( $config_id );
		
		$config = $this->get_config( $config_id );
		
		if ( '' === $config->device_fingerprint ) {
			$mom = self::generate_random_string( 16 );
			$mon = self::generate_random_string( 64 );
			
			$db  = self::generate_random_number( 13 );
			$nom = self::generate_random_number( 19 );
			
			$datahash  = hash( 'sha256', $nom );
			$aa        = substr( "$datahash", 0, 32 );
			$datahassh = hash( 'sha256', $db );
			$aa2       = substr( "$datahassh", 0, 32 );
			
			$device_fingerprint = $mom . 'c2RtNjM2-cWNvbQ-';
			$fingerprint        = "$aa2.$aa.Xiaomi.$mon";
			
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_device_fingerprint', $device_fingerprint );
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_fingerprint', $fingerprint );
		}

		$new_phone_number = isset( $_POST['_pronamic_gateway_upi_qr_phonepe_phone_number'] ) ? sanitize_text_field( $_POST['_pronamic_gateway_upi_qr_phonepe_phone_number'] ) : '';
		if ( $new_phone_number !== $config->phone_number && '' !== $new_phone_number ) {
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_phone_number', $new_phone_number );
		}
		
		$otp = isset( $_POST['_pronamic_gateway_upi_qr_phonepe_otp'] ) ? sanitize_text_field( $_POST['_pronamic_gateway_upi_qr_phonepe_otp'] ) : '';
		if ( '' !== $otp ) {
			try {
				$api        = new API( $config );
				$token_data = $api->login( $otp );
			} catch ( Exception $e ) {
				echo $e->getMessage();
				exit;
			}
			
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_refresh_token', $token_data->refreshToken );
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_token', $token_data->token );
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_expires_at', $token_data->expiresAt - 900 );
			delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_otp' );

			// Setup First Group/Account during login.
			$this->update_group( $config_id );
			return;
		}
		
		if ( '' === $config->refresh_token ) {
			$config = $this->get_config( $config_id );
			try {
				$api             = new API( $config );
				$send_otp_result = $api->send_otp();
			} catch ( Exception $e ) {
				echo $e->getMessage();
				exit;
			}
			if ( $send_otp_result->success ) {
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_token', $send_otp_result->data->token );
			}
			return;
		}

		// Update Group/Account if changed by admin.
		$this->update_group( $config_id );
	}

	// Update PhonePe Group/Account
	private function update_group( $config_id ) {
		$config = $this->get_config( $config_id );

		$new_group_value = isset( $_POST['_pronamic_gateway_upi_qr_phonepe_group_value'] ) ? sanitize_text_field( $_POST['_pronamic_gateway_upi_qr_phonepe_group_value'] ) : '';
		if ( '' !== $config->group_value && $new_group_value === $config->group_value ) {
			return;
		}

		try {
			$api             = new API( $config );
			$group_info_list = $api->get_group_info_list();
		} catch ( Exception $e ) {
			echo $e->getMessage();
			exit;
		}
		foreach ( $group_info_list as $value ) {
			if ( '' === $config->group_value || $value->userGroupNamespace->groupValue === $new_group_value ) {
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_user_group_id', $value->userGroupNamespace->userRole->userGroupId );
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_group_value', $value->userGroupNamespace->groupValue );
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_payee_name', $value->merchantName );

				// Update session if group is changed.
				$this->update_session( $config_id );
			}
		}
	}

	private function update_session( $config_id ) {
		$config = $this->get_config( $config_id );

		try {
			$new_api            = new API( $config );
			$session_token_data = $new_api->update_session();
		} catch ( Exception $e ) {
			echo $e->getMessage();
			exit;
		}

		update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_refresh_token', $session_token_data->refreshToken );
		update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_token', $session_token_data->token );
		update_post_meta( $config_id, '_pronamic_gateway_upi_qr_phonepe_expires_at', $session_token_data->expiresAt - 900 );
		return;
	}

	/**
	 * Field Enabled Payment Methods.
	 *
	 * @param array<string, mixed> $field Field.
	 * @return void
	 */
	public function field_send_phone_otp( $field ) {
		$classes = [
			'pronamic-pay-form-control',
		];
		
		$config_id = (int) \get_the_ID();
		
		$attributes['id']    = '_pronamic_gateway_upi_qr_phonepe_phone_number';
		$attributes['name']  = $attributes['id'];
		$attributes['type']  = 'tel';
		$attributes['class'] = implode( ' ', $classes );
		$attributes['value'] = $this->get_meta( $config_id, 'upi_qr_phonepe_phone_number' );
		
		$element = new Element( 'input', $attributes );
		
		$element->output();
		
		echo '<a id="phonepe-send-phone-otp" class="button button-primary"
		                  role="button" style="font-size: 21px;float: right;margin-right: 50px;">Click to Send OTP</a>';
		
		printf( '<br>Enter 10 digit Phone number registered at PhonePe Business.' );
		printf( '<br>This number must be registered at the PhonePe Business Mobile App.' );
	}
	
	/**
	 * Field Enabled Payment Methods.
	 *
	 * @param array<string, mixed> $field Field.
	 * @return void
	 */
	public function field_submit_otp( $field ) {
		$classes = [
			'pronamic-pay-form-control',
		];
		
		$attributes['id']    = '_pronamic_gateway_upi_qr_phonepe_otp';
		$attributes['name']  = $attributes['id'];
		$attributes['type']  = 'number';
		$attributes['class'] = implode( ' ', $classes );
		
		$element = new Element( 'input', $attributes );
		
		$element->output();
		
		echo '<a id="phonepe-submit-otp" class="button button-primary"
		                  role="button" style="font-size: 21px;float: right;margin-right: 50px;">Submit OTP</a>';
		
		printf( '<br>Enter OTP received on supervisor phone number' );
	}
	
	private static function generate_random_string( $length = 16 ) {
		$characters    = '0123456789abcdef';
		$random_string = '';
		
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
		}
		
		return $random_string;
	}
	
	private static function generate_random_number( $length ) {
		$str = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$str .= mt_rand( 0, 9 );
		}
		return $str;
	}
}
