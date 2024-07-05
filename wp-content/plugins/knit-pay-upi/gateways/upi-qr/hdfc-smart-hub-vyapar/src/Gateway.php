<?php
namespace KnitPayUPI\Gateways\UpiQR\HdfcSmartHubVyapar;

use KnitPay\Gateways\UpiQR\Gateway as UPI_Gateway;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;
use Exception;

/**
 * Title: HDFC SmartHub Vyapar Gateway
 * Copyright: 2020-2024 Knit Pay
 *
 * @author Knit Pay
 * @version 1.4.0.0
 * @since 1.4.0.0
 */
class Gateway extends UPI_Gateway {
	private $api;

	/**
	 * Constructs and initializes an HDFC SmartHub Vyapar gateway
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
		if ( '' === $this->config->tid ) {
			throw new Exception( 'HDFC SmartHub Vyapar is not connected' );
		}
		
		try {
			$knit_pay_upi_data         = $this->knit_pay_upi_data( $payment );
			$knit_pay_upi_data['data'] =
			wp_parse_args(
				$knit_pay_upi_data['data'],
				[
					'transaction_id' => $payment->get_transaction_id(),
					'tid'            => $this->config->tid,
				]
			);
			
			$result = $this->api->encrypt_key_rapid( $knit_pay_upi_data, true );
			
			$payment->set_meta( 'hdfc_smarthub_key', $result->data->hdfc_smarthub_data->encryption_key );
			$payment->set_meta( 'kpp_charge_id', $result->id );
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		// Refresh Session
		$this->refresh_session();

		$data    = [
			'terminalId'  => $this->config->tid,
			'amount'      => $payment->get_total_amount()->number_format( null, '.', '' ),
			'description' => $payment->get_description(),
			'appTxnid'    => $payment->get_transaction_id(),
			'pgId'        => 1,
		];
		$qr_data = $this->api->init_qr_pay( $data, $payment->get_meta( 'hdfc_smarthub_key' ) );

		$payment->set_transaction_id( $qr_data->data['62']['05'] );
		$payment->set_meta( 'qr_text', $qr_data->qr_text );
		$payment->set_meta( 'hdfc_smarthub_key', $qr_data->encyption_key_data );
	}
	
	public function get_upi_qr_text( $payment ) {
		return $payment->get_meta( 'qr_text' );
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

		if ( '' === $this->config->session_id ) {
			throw new Exception( 'HDFC SmartHub Vyapar is not connected' );
		}

		$check_status_count = isset( $_POST['check_status_count'] ) ? sanitize_text_field( $_POST['check_status_count'] ) : '0';
		$check_status_count = intval( $check_status_count );
		$check_status_count = 3;// TODO remove it.
		if ( $check_status_count % 3 === 0 ) {
			$transaction_data = $this->api->get_transaction_details( $payment->get_date(), $payment->get_meta( 'hdfc_smarthub_key' ) );
		} else {
			$transaction_data = $this->api->get_mini_statement( $payment->get_meta( 'hdfc_smarthub_key' ) );
		}

		if ( 'Success' !== $transaction_data->status ) {
			return;
		}
		
		$found_transaction = (object) [];
		foreach ( $transaction_data->transactionParams as $transaction ) {
			if ( $payment->get_transaction_id() === $transaction->txnid ) {
				$found_transaction = $transaction;
			}
		}
		
		if ( property_exists( $found_transaction, 'status' ) ) {
			if ( $found_transaction->amount !== $payment->get_total_amount()->number_format( null, '.', '' ) ) {
				$payment->set_status( PaymentStatus::FAILURE );
				return;
			}
			
			if ( Statuses::COMPLETED !== $found_transaction->status ) {
				$this->expire_old_upi_payment( $payment );
			}
			
			$payment->set_status( Statuses::transform( $found_transaction->status ) );
			
			if ( Core_Statuses::OPEN !== $payment->get_status() ) {
				$payment->delete_meta( 'hdfc_smarthub_key' );
				$payment->add_note( '<strong>HDFC SmartHub Response:</strong><br><pre>' . print_r( $found_transaction, true ) . '</pre><br>' );
			}
		} else {
			$this->expire_old_upi_payment( $payment );
		}
	
		return;
		
		
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

	private function refresh_session() {
		try {
			$session_data = $this->api->validate_user();
		} catch ( Exception $e ) {

			throw $e;
		}
		
		if ( 'Success' === $session_data->status ) {
			if ( 'OTP Sent' === trim( $session_data->respMessage ) ) {
				Integration::clear_config( $this->config->config_id );
				throw new Exception( 'HDFC SmartHub Vyapar is not connected' );
			}
			
			update_post_meta( $this->config->config_id, '_pronamic_gateway_upi_qr_hdfc_smarthub_session_id', $session_data->sessionId );
			
			$this->api->verify_pin();
		}
	}
}
