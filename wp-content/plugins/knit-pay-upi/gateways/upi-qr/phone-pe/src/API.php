<?php

namespace KnitPayUPI\Gateways\UpiQR\PhonePe;

use Exception;
use Pronamic\WordPress\Http\Facades\Http;

/**
 * Title: PhonePe for Business API
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.2.0.0
 * @since   1.2.0.0
 */
class API {
	const CONNECTION_TIMEOUT = 10;

	private $refresh_token;
	private $token;
	private $device_fingerprint;
	private $fingerprint;
	private $phone_number;
	private $user_group_id;
	private $group_value;

	public function __construct( Config $config ) {
		$this->refresh_token      = $config->refresh_token;
		$this->token              = $config->token;
		$this->device_fingerprint = $config->device_fingerprint;
		$this->fingerprint        = $config->fingerprint;
		$this->phone_number       = $config->phone_number;
		$this->user_group_id      = $config->user_group_id;
		$this->group_value        = $config->group_value;
	}

	public function get_endpoint() {
		return 'https://business-api.phonepe.com';
	}
	
	public function send_otp() {
		$path      = '/apis/merchant-insights/v3/auth/sendOtp';
		$data      = [
			'type'              => 'OTP',
			'phoneNumber'       => $this->phone_number,
			'deviceFingerprint' => $this->device_fingerprint,
		];
		$json_data = json_encode( $data );
		
		$cs_data    = [
			'source'   => 'config',
			'amount'   => '0',
			'currency' => 'INR',
			'data'     => [
				'phonepe_type'       => 'send_otp',
				'phone_number'       => $this->phone_number,
				'device_fingerprint' => $this->device_fingerprint,
			],
		];
		$phonepe_cs = $this->get_rapid_api_cs( $cs_data );
		
		$headers                           = $this->get_request_headers();
		$headers['x-request-sdk-checksum'] = $phonepe_cs;
		
		$response = wp_remote_post(
			$this->get_endpoint() . $path,
			[
				'body'    => $json_data,
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		if ( isset( $result->code ) ) {
			throw new Exception( $result->message );
		}
		
		if ( isset( $result->token ) ) {
			return json_decode(
				wp_json_encode(
					[
						'success' => true,
						'data'    => [
							'token' => $result->token,
						],
					]
				)
			);
		} elseif ( property_exists( $result, 'errors' ) ) {
			throw new Exception( reset( $result->errors ) );
		}
		
		throw new Exception( 'Something went wrong. Please try again later.' );
	}

	public function login( $otp ) {
		$path = '/apis/merchant-insights/v3/auth/login';

		$data      = [
			'type'              => 'OTP',
			'phoneNumber'       => $this->phone_number,
			'deviceFingerprint' => $this->device_fingerprint,
			'otp'               => $otp,
			'token'             => $this->token,
		];
		$json_data = json_encode( $data );

		$cs_data = [
			'source'   => 'config',
			'amount'   => '0',
			'currency' => 'INR',
			'data'     => [
				'phonepe_type'       => 'login',
				'phone_number'       => $this->phone_number,
				'device_fingerprint' => $this->device_fingerprint,
				'otp'                => $otp,
				'token'              => $this->token,
				
			],
		];
		$phonepe_cs = $this->get_rapid_api_cs( $cs_data );

		$headers                           = $this->get_request_headers();
		$headers['x-request-sdk-checksum'] = $phonepe_cs;

		$response = wp_remote_post(
			$this->get_endpoint() . $path,
			[
				'body'    => $json_data,
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		if ( isset( $result->code ) ) {
			throw new Exception( $result->message );
		}

		if ( isset( $result->success ) ) {
			$this->refresh_token = $result->refreshToken;
			$this->token         = $result->token;

			return $this->refresh_token();
		}

		throw new Exception( 'Something went wrong. Please try again later.' );
	}

	public function get_group_info_list() {
		$path = '/apis/merchant-insights/v1/user/merchant/groupInfoList';

		$headers                           = $this->get_request_headers();
		$headers['authorization']          = "Bearer {$this->token}";
		$headers['x-request-sdk-checksum'] = 'Yi1iLVFueXBiZi1iYStKNzA4NGRhUnVoLWZkNVB1U0l1MENPOVY1UEZLNWczSnFTbEl6bjVYZjZ6SkpBS1R6a003MnBpMXFjUlFOSytISWQ2SExxU0dZRjV1OTZqaXdCQ0ZueElRPT0=';

		$response = wp_remote_get(
			$this->get_endpoint() . $path,
			[
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		if ( isset( $result->code ) ) {
			throw new Exception( $result->message );
		}

		if ( isset( $result ) ) {
			return $result;
		}

		throw new Exception( 'Something went wrong. Please try again later.' );
	}

	public function update_session() {
		$path      = '/apis/merchant-insights/v1/user/updateSession';
		$data      = [
			'userGroupId' => $this->user_group_id,
		];
		$json_data = json_encode( $data );

		$cs_data    = [
			'source'   => 'config',
			'amount'   => '0',
			'currency' => 'INR',
			'data'     => [
				'phonepe_type'  => 'update_session',
				'user_group_id' => $this->user_group_id,
			],
		];
		$phonepe_cs = $this->get_rapid_api_cs( $cs_data );

		$headers                           = $this->get_request_headers();
		$headers['authorization']          = "Bearer {$this->token}";
		$headers['x-request-sdk-checksum'] = $phonepe_cs;

		$response = wp_remote_post(
			$this->get_endpoint() . $path,
			[
				'body'    => $json_data,
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		if ( isset( $result->code ) ) {
			throw new Exception( $result->message );
		}

		if ( isset( $result->success ) ) {
			$this->refresh_token = $result->refreshToken;
			$this->token         = $result->token;

			return $this->refresh_token();
		}

		throw new Exception( 'Something went wrong. Please try again later.' );
	}

	public function refresh_token() {
		$path      = '/apis/merchant-insights/v1/auth/refresh';
		$json_data = '{}';

		$headers                           = $this->get_request_headers();
		$headers['x-refresh-token']        = $this->refresh_token;
		$headers['x-auth-token']           = $this->token;
		$headers['x-request-sdk-checksum'] = 'LTE3NWJGVlkwOC03Ui9aSTgtOS1tMTd3NDctZGZDaDdzMFE3UjJsVnZXdlFIcnZkUmNEaE9hM1NQeGNTNlhTb0laY2xOLy9FSytwM2hrbTlsUnJiQm1MN2hpUWRiSHA3NlprM3ZRPT0=';

		$response = wp_remote_post(
			$this->get_endpoint() . $path,
			[
				'body'    => $json_data,
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		if ( isset( $result->code ) ) {
			throw new Exception( $result->message );
		}
		
		if ( isset( $result->token ) ) {
			if ( $result->expiresAt > 1e10 ) {
				$result->expiresAt = intdiv( $result->expiresAt, 1000 );// Convert milliseconds to seconds
			}

			$this->refresh_token = $result->refreshToken;
			$this->token         = $result->token;

			return $result;
		}
		
		throw new Exception( 'Something went wrong. Please try again later.' );
	}
	
	public function get_transaction_details( $transaction_id, $status_cs ) {
		$path      = '/apis/merchant-insights/v3/transactions/details';
		$data      = [
			'currentUserMID'         => $this->group_value,
			'transactionId'          => $transaction_id,
			'transactionTypeFilters' => [],
		];
		$json_data = json_encode( $data );
		
		$headers                           = $this->get_request_headers();
		$headers['authorization']          = "Bearer {$this->token}";
		$headers['x-request-sdk-checksum'] = $status_cs;

		$response = wp_remote_post(
			$this->get_endpoint() . $path,
			[
				'body'    => $json_data,
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );

		if ( ! isset( $result ) ) {
			throw new Exception( 'Transaction data not received.' );
		} elseif ( property_exists( $result, 'errorCode' ) ) {
			throw new Exception( $result->errorCode . ': ' . $result->message );
		} elseif ( property_exists( $result, 'code' ) ) {
			throw new Exception( $result->code . ': ' . $result->message );
		} elseif ( ! property_exists( $result, 'success' ) ) {
			throw new Exception( 'Something went wrong. Please try again later.' );
		}

		if ( $result->success ) {
			return $result->data;
		} else {
			return (object) [];
		}
	}

	private function get_request_headers() {
		return [
			'x-app-id'             => 'bd309814ea4c45078b9b25bd52a576de',
			'x-source-type'        => 'PB_APP',
			'x-device-fingerprint' => $this->device_fingerprint,
			'fingerprint'          => $this->fingerprint,
			'content-type'         => 'application/json',
		];
	}
	
	public function get_rapid_api_cs( $data, $return_charge_id = false ) {
		$data['knitpay_version'] = KNITPAY_VERSION;
		$data['php_version']     = PHP_VERSION;
		$data['website_url']     = home_url( '/' );
		$data['mode']            = 'live';
		$data['gateway']         = 'phonepe-business';
		
		\KnitPayPro::check_knit_pay_pro_setup();

		$response = Http::post(
			\KNIT_PAY_UPI_RAPIDAPI_BASE_URL . 'payments/upi/request',
			[
				'body'    => wp_json_encode( $data ),
				'headers' => [
					'X-RapidAPI-Host' => \KNIT_PAY_UPI_RAPIDAPI_HOST,
					'X-RapidAPI-Key'  => get_option( 'knit_pay_pro_setup_rapidapi_key' ),
				],
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		
		$result = $response->json();
		
		if ( '403' === (string) $response->status() ) {
			$api_link = ' https://rapidapi.com/knitpay/api/knit-pay-upi/pricing';
			throw new Exception( 'RapidAPI Error: ' . $result->message . $api_link );
		} elseif ( '200' !== (string) $response->status() ) {
			throw new Exception( 'RapidAPI Error: ' . $result->message );
		}
		
		return $return_charge_id ? $result : $result->data->phonepe_data;
	}
}
