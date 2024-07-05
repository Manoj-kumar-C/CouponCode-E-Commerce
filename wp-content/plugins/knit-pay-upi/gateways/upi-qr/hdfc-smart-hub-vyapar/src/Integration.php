<?php

namespace KnitPayUPI\Gateways\UpiQR\HdfcSmartHubVyapar;

use KnitPay\Utils;
use KnitPay\Gateways\UpiQR\Integration as UPI_Integration;
use Pronamic\WordPress\Html\Element;
use Exception;
use Zxing\QrReader;

/**
 * Title: HDFC SmartHub Vyapar Integration
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.4.0.0
 * @since   1.4.0.0
 */
class Integration extends UPI_Integration {
	private $config;

	/**
	 * Construct HDFC SmartHub Vyapar integration.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'          => 'hdfc-smart-hub-vyapar',
				'name'        => 'HDFC SmartHub Vyapar (UPI/QR)',
				'product_url' => 'https://play.google.com/store/apps/details?id=com.hdfc.smarthub',
				'provider'    => 'hdfc-smart-hub-vyapar',
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
			'title'    => 'HDFC SmartHub Vyapar Terms and Conditions',
			'callback' => function () {
				echo 'Use this integration only if you have read and agree with the Terms and Conditions of HDFC SmartHub Vyapar.';
			},
		];
		
		return $fields;
	}
	
	public function get_setup_settings_fields( $fields ) {
		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
			$fields[] = [
				'section'  => 'general',
				'type'     => 'custom',
				'title'    => 'Incompatible',
				'callback' => function () {
					echo '<h1>' . __( 'Your current PHP version is incompatible with HDFC SmartHub integration. It requires PHP version 8.1 or above.' ) . '</h1>';
				},
			];
			return $fields;
		}

		// TODO: ask pin if not in login logout fields.
		// Login PIN.
		$fields[] = [
			'section'  => 'general',
			'filter'   => FILTER_VALIDATE_INT,
			'meta_key' => '_pronamic_gateway_upi_qr_hdfc_smarthub_pin',
			'title'    => __( 'PIN', 'knit-pay-upi' ),
			'type'     => 'password',
			'classes'  => [ 'regular-text', 'code' ],
			'required' => true,
		];
		
		$fields = $this->get_login_logout_fields( $fields );

		if ( isset( $this->config ) && '' !== $this->config->tid ) {
			$fields = parent::get_setup_settings_fields( $fields );

			unset( $fields['qr_code_scanner'] );
			unset( $fields['payment_instruction'] );
			unset( $fields['mobile_payment_instruction'] );
			unset( $fields['payment_success_status'] );
			unset( $fields['transaction_id_field'] );
			unset( $fields['hide_mobile_qr'] );
			unset( $fields['hide_pay_button'] );
		}
		
		return $fields;
	}
	
	private function get_login_logout_fields( $fields ) {      
		// Callback URL.
		$config_id = Utils::get_gateway_config_id();
		if ( ! empty( $config_id ) ) {
			$this->config = $this->get_config( $config_id );
		}

		if ( ! isset( $this->config ) || '' !== $this->config->tid ) {
			/*
			 try {
				$api             = new API( $this->config );
				$group_info_list = $api->get_group_info_list();
				$account_array   = [];
				foreach ( $group_info_list as $key => $value ) {
					$account_array[ $value->userGroupNamespace->groupValue ] = $value->merchantName . " ({$value->userGroupNamespace->groupValue})";
				}
				$fields[] = [
					'section'  => 'general',
					'meta_key' => '_pronamic_gateway_upi_qr_hdfc_smarthub_group_value',
					'title'    => __( 'Group Value', 'knit-pay-upi' ),
					'type'     => 'select',
					'options'  => $account_array,
				];
			} catch ( Exception $e ) {

			} */

			// TODO show change TID option
			// TODO Show logout button
			return $fields;
		}
		
		// Registered Phone.
		$fields[] = [
			'section'  => 'general',
			'title'    => __( 'Cashier Phone Number', 'knit-pay-upi' ),
			'type'     => 'description',
			'callback' => [ $this, 'field_send_phone_otp' ],
			'tooltip'  => __( 'Phone number of HDFC Cashier', 'knit-pay-upi' ),
		];
		
		// Submit OTP.
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_upi_qr_hdfc_smarthub_otp',
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

		$config->hide_mobile_qr  = false;
		$config->hide_pay_button = true;

		$config->device_id      = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_device_id' );
		$config->session_id     = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_session_id' );
		$config->pin            = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_pin' );
		$config->tid            = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_tid' );
		$config->phone_number   = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_phone_number' );
		$config->app_version    = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_app_version' );
		$config->encryption_key = $this->get_meta( $post_id, 'upi_qr_hdfc_smarthub_encryption_key' );

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
		
		if ( '' === $config->device_id ) {
			$device_id = self::generate_random_string( 16 );
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_device_id', $device_id );
		}
		
		// Fetch Encrytion Key and App Version.
		if ( '' === $config->encryption_key ) {
			try {
				$api = new API( $config );
				$api->encrypt_key_rapid();
			} catch ( Exception $e ) {
				echo $e->getMessage();
				exit;
			}
		}

		$new_phone_number = isset( $_POST['_pronamic_gateway_upi_qr_hdfc_smarthub_phone_number'] ) ? sanitize_text_field( $_POST['_pronamic_gateway_upi_qr_hdfc_smarthub_phone_number'] ) : '';
		if ( $new_phone_number !== $config->phone_number && '' !== $new_phone_number ) {
			update_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_phone_number', $new_phone_number );
		}
		
		$otp = isset( $_POST['_pronamic_gateway_upi_qr_hdfc_smarthub_otp'] ) ? sanitize_text_field( $_POST['_pronamic_gateway_upi_qr_hdfc_smarthub_otp'] ) : '';
		if ( '' !== $otp ) {
			try {
				$api                = new API( $config );
				$verify_data        = $api->verify_otp( $otp );
				$user_terminal_info = $api->get_user_terminal_info();
			} catch ( Exception $e ) {
				echo $e->getMessage();
				exit;
			}
			
			if ( 'Success' === $user_terminal_info->status ) {
				$qrcode  = new QrReader( reset( reset( $user_terminal_info->terminalInfo ) )->payments->qr->digitalStaticQRPath );
				$qr_data = $api->decode_emv_qr( $qrcode->text() );
				
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_vpa', $qr_data['26']['01'] );
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_payee_name', $qr_data['59'] );
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_merchant_category_code', $qr_data['52'] );
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_tid', key( reset( $user_terminal_info->terminalInfo ) ) );
				delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_otp' );
			}
			return;
		}
		
		if ( '' === $config->session_id ) {
			$config = $this->get_config( $config_id );
			try {
				$api             = new API( $config );
				$send_otp_result = $api->validate_user();
			} catch ( Exception $e ) {
				echo $e->getMessage();
				exit;
			}

			if ( 'Success' === $send_otp_result->status ) {
				update_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_session_id', $send_otp_result->sessionId );
			}
			return;
		}
	}
	
	public static function clear_config( $config_id ) {
		delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_tid' );
		// delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_device_id' );
		delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_session_id' );
		
		// TODO send warning email to WordPress Admin.
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
		
		$attributes['id']       = '_pronamic_gateway_upi_qr_hdfc_smarthub_phone_number';
		$attributes['name']     = $attributes['id'];
		$attributes['type']     = 'tel';
		$attributes['class']    = implode( ' ', $classes );
		$attributes['value']    = $this->get_meta( $config_id, 'upi_qr_hdfc_smarthub_phone_number' );
		$attributes['required'] = true;
		
		$element = new Element( 'input', $attributes );
		
		$element->output();
		
		echo '<a id="hdfc-smarthub-send-phone-otp" class="button button-primary"
		                  role="button" style="font-size: 21px;float: right;margin-right: 50px;">Click to Send OTP</a>';
		
		printf( '<br>Enter 10 digit Phone number registered at HDFC SmartHub Vyapar.' );
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
		
		$attributes['id']    = '_pronamic_gateway_upi_qr_hdfc_smarthub_otp';
		$attributes['name']  = $attributes['id'];
		$attributes['type']  = 'number';
		$attributes['class'] = implode( ' ', $classes );
		
		$element = new Element( 'input', $attributes );
		
		$element->output();
		
		echo '<a id="hdfc-smarthub-submit-otp" class="button button-primary"
		                  role="button" style="font-size: 21px;float: right;margin-right: 50px;">Submit OTP</a>';
		
		printf( '<br>Enter OTP received on supervisor phone number' );
	}
	
	public static function generate_random_string( $length = 16 ) {
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
