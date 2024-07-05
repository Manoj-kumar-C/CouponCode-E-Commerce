<?php
namespace KnitPayUPI\Gateways\UpiQR\PhonePe;

use KnitPay\Gateways\UpiQR\Gateway as UPI_Gateway;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Exception;

/**
 * Title: PhonePe Business Gateway
 * Copyright: 2020-2024 Knit Pay
 *
 * @author Knit Pay
 * @version 1.2.0.0
 * @since 1.2.0.0
 */
class Gateway extends UPI_Gateway {
	private $api;

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
		
		$this->api = new API( $config );
		
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

		// Check connection.
		if ( '' === $this->config->refresh_token ) {
			throw new Exception( 'PhonePe for Business is not connected' );
		}
		$this->refresh_token();

		try {
			$knit_pay_upi_data         = $this->knit_pay_upi_data( $payment );
			$knit_pay_upi_data['data'] =
			wp_parse_args(
				$knit_pay_upi_data['data'],
				[
					'phonepe_type'     => 'status',
					'transaction_id'   => $payment->get_transaction_id(),
					'current_user_mid' => $this->config->group_value,
				]
			);

			$result = $this->api->get_rapid_api_cs( $knit_pay_upi_data, true );

			$payment->set_meta( 'phonepe_status_cs', $result->data->phonepe_data );
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

		if ( '' === $this->config->refresh_token ) {
			throw new Exception( 'PhonePe for Business is not connected' );
		}

		if ( time() > $this->config->expires_at ) {
			$this->refresh_token();
		}

		$transaction_data = $this->api->get_transaction_details( $payment->get_transaction_id(), $payment->get_meta( 'phonepe_status_cs' ) );

		if ( property_exists( $transaction_data, 'paymentState' ) ) {
			if ( $transaction_data->amount !== $payment->get_total_amount()->get_minor_units()->to_int() ) {
				$payment->set_status( PaymentStatus::FAILURE );
				return;
			}
			
			if ( Statuses::COMPLETED === $transaction_data->paymentState ) {
				$payment->set_transaction_id( $transaction_data->transactionId );
			} else {
				$this->expire_old_upi_payment( $payment );
			}
			
			$payment->set_status( Statuses::transform( $transaction_data->paymentState ) );
			$payment->add_note( '<strong>PhonePe Response:</strong><br><pre>' . print_r( $transaction_data, true ) . '</pre><br>' );
		} else {
			$this->expire_old_upi_payment( $payment );
		}
	}

	private function refresh_token() {
		try {
			$token_data = $this->api->refresh_token();
		} catch ( Exception $e ) {
			if ( false !== strpos( $e->getMessage(), 'Authorization failed' ) ) {
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_device_fingerprint' );
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_fingerprint' );
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_refresh_token' );
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_token' );
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_expires_at' );
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_user_group_id' );
				delete_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_group_value' );
			}

			// TODO send warning email to WordPress Admin.

			throw $e;
		}

		if ( $this->config->refresh_token !== $token_data->refreshToken ) {
			update_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_refresh_token', $token_data->refreshToken );
			update_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_token', $token_data->token );
			update_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_phonepe_expires_at', $token_data->expiresAt - 900 );
		}
	}
}
