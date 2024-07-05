<?php

namespace KnitPayUPI\Gateways\UpiQR\Paytm;

use Exception;

/**
 * Title: PhonePe for Business API
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.0.0
 * @since   1.0.0
 */
class API {
	const CONNECTION_TIMEOUT = 10;

	public static function get_order_status( $data ) {
		$response = wp_remote_get(
			'https://securegw.paytm.in/order/status?' . $data,
			[
				'timeout' => self::CONNECTION_TIMEOUT,
			]
		);
		$result   = wp_remote_retrieve_body( $response );

		$result = json_decode( $result );
		if ( ! isset( $result ) || ! property_exists( $result, 'STATUS' ) ) {
			throw new Exception( 'Something went wrong. Please try again later.' );
		}
		
		return $result;
	}
}
