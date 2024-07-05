<?php

namespace KnitPayUPI\Gateways\UpiQR\HdfcSmartHubVyapar;

use Pronamic\WordPress\Pay\Core\GatewayConfig;
use KnitPay\Gateways\UpiQR\Config as Config_UPIQR;

/**
 * Title: HDFC SmartHub Vyapar Config
 * Copyright: 2020-2024 Knit Pay
 *
 * @author  Knit Pay
 * @version 1.4.0.0
 * @since   1.4.0.0
 */
class Config extends Config_UPIQR {
	public $device_id;
	public $session_id;
	public $pin;
	public $tid;
	public $phone_number;
	public $app_version;
	public $encryption_key;
	
	public $config_id;
	
}
