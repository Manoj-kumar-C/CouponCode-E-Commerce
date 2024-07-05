<?php

namespace KnitPayUPI\Gateways\UpiQR\BharatPe;

use Pronamic\WordPress\Http\Facades\Http;
use Exception;

/**
 * Title: BharatPe API Client
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.3.0.0
 * @since   1.3.0.0
 */
class Client {
	const CONNECTION_TIMEOUT = 10;

	private $token;
	private $merchant_id;

	public function __construct( Config $config ) {
		$this->token       = $config->token;
		$this->merchant_id = $config->merchant_id;
	}

	public function get_transaction_details( $bharat_pe_data ) {
		$response = wp_remote_get(
			add_query_arg(
				json_decode( $bharat_pe_data, true ),
				'https://payments-tesseract.bharatpe.in/api/v1/merchant/transactions'
			),
			[
				'headers' => [
					'token' => $this->token,
				],
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		
		if ( ! isset( $result ) ) {
			throw new Exception( 'Something went wrong. Please try again later.' );
		}
		
		if ( true === $result->status ) {
			return $result->data->transactions;
		} else {
			throw new Exception( $result->responseMessage, $result->responseCode );
		}
	}

	public function get_bharatpe_connection_data() {
		$data = [
			'mode'            => 'live',
			'gateway'         => 'bharat-pe',
			'source'          => 'config',
			'amount'          => '0',
			'currency'        => 'INR',
			'knitpay_version' => KNITPAY_VERSION,
			'php_version'     => PHP_VERSION,
			'website_url'     => home_url( '/' ),
			'data'            => [
				'mid'            => $this->merchant_id,
				'transaction_id' => '',
			],
		];
		
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
		
		return $result->data->bharat_pe_data;
	}
}
