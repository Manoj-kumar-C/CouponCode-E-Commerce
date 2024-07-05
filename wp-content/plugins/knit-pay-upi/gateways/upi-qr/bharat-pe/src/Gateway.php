<?php
namespace KnitPayUPI\Gateways\UpiQR\BharatPe;

use KnitPay\Gateways\UpiQR\Gateway as UPI_Gateway;
use Pronamic\WordPress\Http\Facades\Http;
use Pronamic\WordPress\Pay\Payments\FailureReason;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;
use Exception;


/**
 * Title: BharatPe Gateway
 * Copyright: 2020-2024 Knit Pay
 *
 * @author Knit Pay
 * @version 1.3.0.0
 * @since 1.3.0.0
 */
class Gateway extends UPI_Gateway {
	private $client;

	/**
	 * Constructs and initializes an BharatPe gateway
	 *
	 * @param Config $config
	 *            Config.
	 */
	public function __construct( Config $config ) {     
		// Supported features.
		$this->supports = [
			'payment_status_request',
		];
		
		// $this->config = $config;
		
		parent::__construct( $config );

		$this->payment_expiry_seconds = 600;

		$this->client = new Client( $config );
	}
	
	/**
	 * Start.
	 *
	 * @see UPI_Gateway::start()
	 *
	 * @param Payment $payment
	 *            Payment.
	 */
	public function start( Payment $payment ) {
		parent::start( $payment );

		// Check connection.
		$this->client->get_transaction_details( get_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_bharatpe_connection_data', true ) );

		try {
			$knit_pay_upi_data         = $this->knit_pay_upi_data( $payment );
			$knit_pay_upi_data['data'] = wp_parse_args(
				$knit_pay_upi_data['data'],
				[
					'mid'            => $this->config->merchant_id,
					'transaction_id' => $payment->get_transaction_id(),
				]
			);

			\KnitPayPro::check_knit_pay_pro_setup();

			$response = Http::post(
				\KNIT_PAY_UPI_RAPIDAPI_BASE_URL . 'payments/upi/request',
				[
					'body'    => wp_json_encode(
						$knit_pay_upi_data
					),
					'headers' => [
						'X-RapidAPI-Host' => \KNIT_PAY_UPI_RAPIDAPI_HOST,
						'X-RapidAPI-Key'  => get_option( 'knit_pay_pro_setup_rapidapi_key' ),
					],
				]
			);
			
			$result = $response->json();
			
			if ( '403' === (string) $response->status() ) {
				$api_link = ' https://rapidapi.com/knitpay/api/knit-pay-upi/pricing';
				throw new Exception( 'RapidAPI Error: ' . $result->message . $api_link );
			} elseif ( '200' !== (string) $response->status() ) {
				throw new Exception( 'RapidAPI Error: ' . $result->message );
			}

			$payment->set_meta( 'bharat_pe_data', $result->data->bharat_pe_data );
			$payment->set_meta( 'kpp_charge_id', $result->id );
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment
	 *            Payment.
	 */
	public function update_status( Payment $payment ) {
		if ( PaymentStatus::SUCCESS === $payment->get_status() ) {
			return;
		}

		if ( array_key_exists( 'status', $_REQUEST ) ) {
			$payment_status = sanitize_text_field( $_REQUEST['status'] );
			if ( 'Expired' === $payment_status || 'Cancelled' === $payment_status ) {
				$payment->set_status( $payment_status );
				return;
			}
		}

		if ( isset( $_POST['knit_pay_utr'] ) ) {
			$knit_pay_utr = sanitize_text_field( $_POST['knit_pay_utr'] );
			$payment->set_transaction_id( $knit_pay_utr );

			$old_payment = get_pronamic_payment_by_transaction_id( $knit_pay_utr );

			if ( null !== $old_payment ) {
				$failure_reason = new FailureReason();
				$failure_reason->set_message( 'Trying to use same UTR twice. UTR: ' . $knit_pay_utr );
				$failure_reason->set_code( 'FRAUD' );
				$payment->set_failure_reason( $failure_reason );
				
				$payment->set_status( Core_Statuses::FAILURE );
				return;
			}
		}

		$transactions = $this->client->get_transaction_details( $payment->get_meta( 'bharat_pe_data' ) );

		$found_transaction = (object) [];
		foreach ( $transactions as $transaction ) {
			if ( $payment->get_transaction_id() === $transaction->bankReferenceNo ) {
				$found_transaction = $transaction;
			}
		}

		if ( property_exists( $found_transaction, 'status' ) && 'SUCCESS' === $found_transaction->status ) {
			if ( floatval( $found_transaction->amount ) !== floatval( $payment->get_total_amount()->number_format( null, '.', '' ) ) ) {
				$payment->set_status( Core_Statuses::FAILURE );
				return;
			}

			$payment->set_status( Core_Statuses::SUCCESS );
			$payment->add_note( '<strong>BharatPe Response:</strong><br><pre>' . print_r( $found_transaction, true ) . '</pre><br>' );
		} else {
			$this->expire_old_upi_payment( $payment );
		}
	}
}
