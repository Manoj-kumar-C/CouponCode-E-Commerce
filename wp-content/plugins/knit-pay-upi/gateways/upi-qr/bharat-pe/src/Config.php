<?php

namespace KnitPayUPI\Gateways\UpiQR\BharatPe;

use KnitPay\Gateways\UpiQR\Config as Config_UPIQR;

/**
 * Title: BharatPe Config
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.3.0.0
 * @since   1.3.0.0
 */
class Config extends Config_UPIQR {
	public $merchant_id;
	public $token;
	public $config_id;
}
