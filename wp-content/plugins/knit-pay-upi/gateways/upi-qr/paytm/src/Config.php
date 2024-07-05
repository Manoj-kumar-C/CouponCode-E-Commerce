<?php

namespace KnitPayUPI\Gateways\UpiQR\Paytm;

use Pronamic\WordPress\Pay\Core\GatewayConfig;
use KnitPay\Gateways\UpiQR\Config as Config_UPIQR;

/**
 * Title: PhonePe for Business Config
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.0.0
 * @since   1.0.0
 */
class Config extends Config_UPIQR {
	public $mid;
}
