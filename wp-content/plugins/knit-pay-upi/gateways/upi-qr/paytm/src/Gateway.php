<?php
namespace KnitPayUPI\Gateways\UpiQR\Paytm;

use KnitPay\Gateways\UpiQR\Gateway as UPI_Gateway;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;
use Pronamic\WordPress\Http\Facades\Http;
use Exception;

/**
 * Title: PhonePe for Business Gateway
 * Copyright: 2020-2024 Knit Pay
 *
 * @author Knit Pay
 * @version 1.0.0
 * @since 1.0.0
 */
class Gateway extends UPI_Gateway {

	/**
	 * Constructs and initializes an UPI QR gateway
	 *
	 * @param Config $config
	 *            Config.
	 */
	public function __construct( Config $config ) {     
		// Supported features.
		$this->supports = [
			'payment_status_request',
		];
		
		$this->config = $config;
		
		parent::__construct( $config );
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
		
		try {
			$knit_pay_upi_data         = $this->knit_pay_upi_data( $payment );
			$knit_pay_upi_data['data'] = wp_parse_args(
				$knit_pay_upi_data['data'],
				[
					'mid'            => $this->config->mid,
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

			$payment->set_meta( 'paytm_data', $result->data->paytm_data );
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

		$order_status = API::get_order_status( $payment->get_meta( 'paytm_data' ) );

		if ( property_exists( $order_status, 'STATUS' ) && 'TXN_SUCCESS' === $order_status->STATUS ) {
			if ( $order_status->TXNAMOUNT !== $payment->get_total_amount()->number_format( null, '.', '' ) ) {
				$payment->set_status( PaymentStatus::FAILURE );
				return;
			}
			
			$payment->set_transaction_id( $order_status->BANKTXNID );
			
			$payment->set_status( Core_Statuses::SUCCESS );
			$payment->add_note( '<strong>Paytm Response:</strong><br><pre>' . print_r( $order_status, true ) . '</pre><br>' );
		} else {
			$this->expire_old_upi_payment( $payment );
		}
	}
}
