<?php

namespace KnitPayUPI\Gateways\UpiQR\HdfcSmartHubVyapar;

use Exception;
use Pronamic\WordPress\Http\Facades\Http;
use Zxing\QrReader;
use Pronamic\WordPress\DateTime\DateTime;

/**
 * Title: HDFC SmartHub Vyapar API
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.4.0.0
 * @since   1.4.0.0
 */
class API {
	const CONNECTION_TIMEOUT = 10;
	
	private $device_id;
	private $session_id;
	private $pin;
	private $tid;
	private $phone_number;
	private $app_version;
	private $config_id;
	private $encyption_key;

	public function __construct( Config $config ) {
		$this->device_id     = $config->device_id;
		$this->session_id    = $config->session_id;
		$this->pin           = $config->pin;
		$this->tid           = $config->tid;
		$this->phone_number  = $config->phone_number;
		$this->app_version   = $config->app_version;
		$this->config_id     = $config->config_id;
		$this->encyption_key = $config->encryption_key;
	}

	private function get_endpoint() {
		return 'https://hdfcmmp.mintoak.com';
	}
	
	public function validate_user() {
		$path = '/OneAppAuth/v3/ValidateUser';
		
		$data = [
			'loginId'        => $this->phone_number,
			'appVersion'     => $this->app_version,
			'devicePlatform' => 'android',
		];
		
		$headers = [ 'deviceid' => $this->device_id ];
		
		$response = $this->hdfc_post( $path, $data, $this->encyption_key, $headers );

		$this->session_id = $response->sessionId;

		return $response;
	}

	public function verify_otp( $otp ) {
		$path = '/OneAppAuth/VerifyOTP';
		
		$data = [
			'loginId' => $this->phone_number,
			'otp'     => $otp,
		];
		
		$response = $this->hdfc_post( $path, $data, $this->encyption_key );
		if ( 'Success' === $response->status ) {
			return $this->verify_pin();
		}
		
		return $response;
	}
	
	public function verify_pin() {
		$path = '/OneAppAuth/VerifyPin';
		
		$data = [
			'loginId'  => $this->phone_number,
			'authType' => 'mPin',
			'mPin'     => $this->pin,
		];
		
		$response = $this->hdfc_post( $path, $data, $this->encyption_key );
		return $response;
	}
	
	public function init_qr_pay( $data, $encyption_key_data = null ) {
		$path = '/HDFC/OneApp/QRPay';

		$qr_image = $this->hdfc_post( $path, $data, $encyption_key_data );

		try {
			$qr_code = new QrReader( $qr_image->data, QrReader::SOURCE_TYPE_BLOB );

			$qr_text = $qr_code->text();
		  
			// Decoding EMV QR string
			$qr_data = $this->decode_emv_qr( $qr_text );
		} catch ( \InvalidArgumentException $e ) {
			throw new \InvalidArgumentException( 'Error occured while reading QR: ' . $e->getMessage() );
		}
		
		return (object) [
			'qr_text'            => $qr_text,
			'data'               => $qr_data,
			'encyption_key_data' => $qr_image->encyption_key_data,
		];
	}
	
	public function get_user_terminal_info() {
		$path = '/HDFC360/user-terminal-info';
		
		$data = (object) [];
		
		$response = $this->hdfc_post( $path, $data, $this->encyption_key );
		
		return $response;
	}
	
	
	public function get_mini_statement( $encyption_key_data = null ) {
		$path = '/HDFC360/OneApp/V2/merchant-txn-detail';
		
		$data = [
			'tidList'     => [ $this->tid ],
			'type'        => 'terminal',
			'serviceType' => 'miniStatement',
			'count'       => '10',
		];
		
		$response = $this->hdfc_post( $path, $data, $encyption_key_data );
		
		return $response;
	}
	
	public function get_transaction_details( $start_date, $encyption_key_data = null ) {
		// Fetching last 3 transactions (failed, pending, void, success.
		$mini_statements = $this->get_mini_statement( $encyption_key_data );
		
		$path = '/HDFC360/OneApp/V2/merchant-txn-detail';

		$start_date = $start_date->get_local_date()->format( 'Y-m-d' );
		$end_data   = ( new DateTime() )->get_local_date()->format( 'Y-m-d' );
		
		$data = [
			'tidList'   => [ $this->tid ],
			'type'      => 'terminal',
			'txnsType'  => 'SaleSuccess',
			'startDate' => $start_date,
			'endDate'   => $end_data,
			'count'     => '100',
		];
		
		$response = $this->hdfc_post( $path, $data, $encyption_key_data );
		
		// Merging all successful transactions with mini statement trasnactions.
		$response->transactionParams = array_merge( $mini_statements->transactionParams, $response->transactionParams );
		
		return $response;
	}
	
	private function hdfc_post( $path, $data, $encyption_key_data = null, $headers = [] ) {
		if ( isset( $encyption_key_data ) && property_exists( $encyption_key_data, 'encryption_key' ) ) {
			$encryption_key           = $encyption_key_data->encryption_key;
			$encrypted_encryption_key = $encyption_key_data->encrypted_encryption_key;
		} else {
			$encyption_key_response = $this->encrypt_key_rapid();
			
			$encyption_key_data = $encyption_key_response->encryption_key;
			
			$encryption_key           = $encyption_key_data->encryption_key;
			$encrypted_encryption_key = $encyption_key_data->encrypted_encryption_key;
		}
		$encryption_key = base64_decode( $encryption_key );
		$aesiv          = random_bytes( 16 );
		
		$payload   = $this->encrypt( wp_json_encode( $data ), $encryption_key, $aesiv );
		$body_data = [
			'KEY'     => $encrypted_encryption_key,
			'IV'      => base64_encode( $aesiv ),
			'PAYLOAD' => $payload,
		];
		
		$headers = wp_parse_args(
			$headers,
			$this->get_request_headers()
		);

		$response = wp_remote_post(
			$this->get_endpoint() . $path,
			[
				'body'    => wp_json_encode( $body_data ),
				'headers' => $headers,
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );
		
		if ( ! isset( $result ) ) {
			throw new Exception( 'Something went wrong. Please try again later.' );
		}
		
		$result_data = json_decode( $result );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			// If not json, then try to decrypt.
			$decrypted_data = $this->decrypt( $result, $encryption_key, $aesiv );
			
			if ( $decrypted_data ) {
				$result_data = json_decode( $decrypted_data );
			} else {
				return (object) [
					'data'               => $result, 
					'encyption_key_data' => $encyption_key_data,
				];
			}
		}

		if ( ! isset( $result_data ) || ! isset( $result_data->status ) ) {
			throw new Exception( 'Something went wrong. Please try again later.' );
		} elseif ( 'Failed' === $result_data->status ) {
			if ( isset( $result_data->respMsg ) ) {
				$error = $result_data->respMsg;
			} elseif ( isset( $result_data->respMessage ) ) {
				$error = $result_data->respMessage;
			} else {
				$error = $result_data->message;
			}
			
			throw new Exception( $error );
		} elseif ( 'Success' !== $result_data->status ) {
			throw new Exception( $result_data->error, $result_data->status );
		} else {
			$result_data->encyption_key_data = $encyption_key_data;
			return $result_data;
		}
	}

	private function get_request_headers() {
		return [
			'sessionid'    => $this->session_id,
			'content-type' => 'application/json',
		];
	}
	
	public function encrypt_key_rapid( $data = [], $return_charge_id = false ) {
		$data = wp_parse_args(
			$data,
			[
				'gateway'         => 'hdfc-smart-hub-vyapar',
				'knitpay_version' => KNITPAY_VERSION,
				'php_version'     => PHP_VERSION,
				'website_url'     => home_url( '/' ),
				'source'          => 'config',
				'amount'          => '0',
				'currency'        => 'INR',
				'mode'            => 'live',
				'data'            => [],
			]
		);
		
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
		
		if ( version_compare( $result->data->hdfc_smarthub_data->app_version, $this->app_version, '>' ) ) {
			$this->app_version = $result->data->hdfc_smarthub_data->app_version;
			update_post_meta( $this->config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_app_version', $this->app_version );
		}

		$this->encyption_key = $result->data->hdfc_smarthub_data->encryption_key;
		update_post_meta( $this->config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_encryption_key', $this->encyption_key );
				
		return $return_charge_id ? $result : $result->data->hdfc_smarthub_data;
	}
	
	// TODO Move to Utils
	public function decode_emv_qr( $emvQR ) {
		$index = 0;
		
		function parseTLV( $data, &$index ) {
			$parsed = [];
			while ( $index < strlen( $data ) ) {
				$id    = substr( $data, $index, 2 );
				$len   = intval( substr( $data, $index + 2, 2 ) );
				$value = substr( $data, $index + 4, $len );
				
				if ( in_array( $id, [ '26', '27', '29', '31', '62' ] ) ) {  // Nested structures
					$nestedIndex   = 0;
					$parsed[ $id ] = parseTLV( $value, $nestedIndex );
				} else {
					$parsed[ $id ] = $value;
				}
				$index += 4 + $len;
			}
			return $parsed;
		}
		
		return parseTLV( $emvQR, $index );
	}
	
	private function encrypt( $data, $key, $iv ) {
		$tag = '';
		
		$encrypted = openssl_encrypt( $data, 'aes-128-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, '', 16 );
		
		return base64_encode( $encrypted . $tag );
	}
	
	private function decrypt( $data, $key, $iv ) {
		$data = base64_decode( $data );
		$tag  = substr( $data, strlen( $data ) - 16 );
		$data = substr( $data, 0, strlen( $data ) - 16 );
		
		try {
			return openssl_decrypt( $data, 'aes-128-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag );
		} catch ( \Exception $e ) {
			throw new Exception( 'Response Decryption Failed.' );
		}
	}
}
