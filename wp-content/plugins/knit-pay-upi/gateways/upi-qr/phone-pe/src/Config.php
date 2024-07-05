<?php

namespace KnitPayUPI\Gateways\UpiQR\PhonePe;

use Pronamic\WordPress\Pay\Core\GatewayConfig;
use KnitPay\Gateways\UpiQR\Config as Config_UPIQR;

/**
 * Title: PhonePe for Business Config
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.2.0.0
 * @since   1.2.0.0
 */
class Config extends Config_UPIQR {
	public $refresh_token;
	public $token;
	public $expires_at;
	public $device_fingerprint;
	public $fingerprint;
	public $phone_number;
	public $user_group_id;
	public $group_value;
	
	public $config_id;
	
}
