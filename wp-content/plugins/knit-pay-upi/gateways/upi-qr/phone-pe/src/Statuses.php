<?php

namespace KnitPayUPI\Gateways\UpiQR\PhonePe;

use Pronamic\WordPress\Pay\Payments\PaymentStatus as Core_Statuses;

/**
 * Title: PhonePe for Business Statuses
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.2.0.0
 * @since   1.2.0.0
 */
class Statuses {
	const COMPLETED = 'COMPLETED';
	
	const FAILED = 'FAILED';

	/**
	 * Transform an PhonePe status to an Knit Pay status
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public static function transform( $status ) {
		switch ( $status ) {
			case self::COMPLETED:
				return Core_Statuses::SUCCESS;
				
			case self::FAILED:
				return Core_Statuses::FAILURE;

			default:
				return Core_Statuses::OPEN;
		}
	}
}
