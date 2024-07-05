<?php

namespace KnitPayUPI\Gateways\UpiQR\BharatPe;

use KnitPay\Gateways\UpiQR\Integration as UPI_Integration;
use KnitPay\Utils;
use Exception;

/**
 * Title: BharatPe Integration
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.3.0.0
 * @since   1.3.0.0
 */
class Integration extends UPI_Integration {
	/**
	 * Construct BharatPe integration.
	 *
	 * @param array $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'id'          => 'bharat-pe',
				'name'        => 'BharatPe (UPI/QR)',
				'product_url' => 'http://go.thearrangers.xyz/bharatpe?utm_source=knit-pay&utm_medium=ecommerce-module&utm_campaign=module-admin&utm_content=product-url',
				'provider'    => 'bharat-pe',
			]
		);

		parent::__construct( $args );
	}
	
	/**
	 * Setup gateway integration.
	 *
	 * @return void
	 */
	public function setup() {
		parent::setup();
		
		// Get new access token if it's about to get expired.
		add_action( 'knit_pay_upi_bharatpe_refresh_connection', [ $this, 'refresh_connection' ], 10, 1 );
	}
	
	public function get_config( $post_id ) {
		$config = new Config();
		$config->copy_properties( parent::get_config( $post_id ) );
		
		$config->merchant_id = $this->get_meta( $post_id, 'upi_qr_bharatpe_merchant_id' );
		$config->token       = $this->get_meta( $post_id, 'upi_qr_bharatpe_token' );
		
		$config->hide_mobile_qr       = false;
		$config->hide_pay_button      = true;
		$config->transaction_id_field = self::SHOW_REQUIRED_FIELD;

		$config->config_id = $post_id;

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
			'title'    => 'BharatPe Terms and Conditions',
			'callback' => function () {
				echo 'Use this integration only if you have read and agree with the Terms and Conditions of BharatPe.';
			},
		];

		// Steps to Integrate.
		$fields[] = [
			'section'  => 'general',
			'type'     => 'custom',
			'title'    => 'Steps to Integrate BharatPe',
			'callback' => function () {

				echo '<ol><li>Token and Merchant ID can be found in the Local Storage, after login at <a target="_blank" href="https://enterprise.bharatpe.in/">https://enterprise.bharatpe.in/</a></li>
                    <ul>
                        <li><a target="_blank" href="https://firefox-source-docs.mozilla.org/devtools-user/storage_inspector/local_storage_session_storage/index.html">Firefox Local Storage</a></li>
                        <li><a target="_blank" href="https://developer.chrome.com/docs/devtools/storage/localstorage">Chrome Local Storage</a></li>
                    </ul>
                    <br><li>Feel free to <a target="_blank" href="https://www.knitpay.org/contact-us/">contact us</a> if you need help in generating retrieving Merchant ID and Token</li>
                  </ol>';
			},
		];
		
		return $fields;
	}

	public function get_setup_settings_fields( $fields ) {
		// Merchant ID.
		// JSON.parse(window.localStorage.getItem("USER_INFO")).merchant_id
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_upi_qr_bharatpe_merchant_id',
			'title'    => __( 'Merchant ID', 'knit-pay-upi' ),
			'type'     => 'text',
			'classes'  => [ 'regular-text', 'code' ],
			'required' => true,
		];
		
		// Token.
		// window.localStorage.getItem("TOKEN")
		$fields[] = [
			'section'  => 'general',
			'meta_key' => '_pronamic_gateway_upi_qr_bharatpe_token',
			'title'    => __( 'Token', 'knit-pay-upi' ),
			'type'     => 'text',
			'classes'  => [ 'regular-text', 'code' ],
			'required' => true,
		];
		
		// WP Cron URL.
		$fields[] = [
			'section'  => 'general',
			'title'    => \__( 'WP Cron URL', 'knit-pay-upi' ),
			'type'     => 'text',
			'classes'  => [ 'large-text', 'code' ],
			'value'    => site_url( 'wp-cron.php' ),
			'readonly' => true,
			'callback' => function () {
				echo 'We recommend you set up WP Cron at least twice every hour to avoid session timeout. Refer to below link for more details.<br>
                <a target="_blank" href="https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/">https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/</a>';
			},
		];

		// Callback URL.
		$config_id = Utils::get_gateway_config_id();
		if ( ! empty( $config_id ) ) {
			$config = $this->get_config( $config_id );
		}
		if ( ! isset( $config ) || '' !== $config->token ) {
			try {
				$api_client = new Client( $config );
				$api_client->get_transaction_details( $this->get_meta( $config_id, 'upi_qr_bharatpe_connection_data' ) );

				$fields[] = [
					'section'  => 'general',
					'type'     => 'custom',
					'title'    => 'Connected',
					'callback' => function () {
						echo '<span class="dashicons dashicons-yes"></span>';
					},
				];
			} catch ( Exception $e ) {
				$fields[] = [
					'section'  => 'general',
					'type'     => 'custom',
					'title'    => 'Connected',
					'callback' => function () {
						echo '<span class="dashicons dashicons-no"></span>';
					},
				];
				
				self::clear_config( $config_id );
				
				$message = '<strong>Knit Pay</strong> - The connection to BharatPe could not be established. Please ensure that the provided merchant ID and token are accurate.';
				wp_admin_notice( $message, [ 'type' => 'error' ] );
				return $fields;
			}
			
			$fields = parent::get_setup_settings_fields( $fields );
		}
		
		unset( $fields['payment_instruction'] );
		unset( $fields['mobile_payment_instruction'] );
		unset( $fields['payment_success_status'] );
		unset( $fields['transaction_id_field'] );
		unset( $fields['hide_mobile_qr'] );
		unset( $fields['hide_pay_button'] );
		
		return $fields;
	}
	
	protected function get_supported_template_list() {
		return [
			'4' => '4',
		];
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
		if ( '' !== $this->get_meta( $config_id, 'upi_qr_bharatpe_connection_data' ) ) {
			return;
		}
		
		$config = $this->get_config( $config_id );
		
		try {
			$api_client               = new Client( $config );
			$bharatpe_connection_data = $api_client->get_bharatpe_connection_data();
		} catch ( Exception $e ) {
			echo $e->getMessage();
			exit;
		}
		
		update_post_meta( $config_id, '_pronamic_gateway_upi_qr_bharatpe_connection_data', $bharatpe_connection_data );

		wp_schedule_event(
			time(),
			'hourly',
			'knit_pay_upi_bharatpe_refresh_connection',
			[ 'config_id' => $config_id ]
		);
	}
	
	public function refresh_connection( $config_id ) {
		try {
			$config     = $this->get_config( $config_id );
			$api_client = new Client( $config );
			$api_client->get_transaction_details( $this->get_meta( $config_id, 'upi_qr_bharatpe_connection_data' ) );
		} catch ( Exception $e ) {
			self::clear_config( $config_id );
		}
	}
	
	private static function clear_config( $config_id ) {
		delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_bharatpe_connection_data' );
		delete_post_meta( $config_id, '_pronamic_gateway_upi_qr_bharatpe_token' );
		
		wp_unschedule_hook( 'knit_pay_upi_bharatpe_refresh_connection' );
	}
}
