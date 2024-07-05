<?php

$country_phone_codes = (array) xoo_ml_country_codes_list();

$phone_codes = array();

foreach ( $country_phone_codes as $cc => $pc ) {
	if( !$pc || is_array( $pc ) ) continue;
	$phone_codes[ $pc ] = $cc . $pc;
}


$settings = array(

	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_main',
		'id'			=> 'm-operator',
		'title' 		=> 'SMS Service Operator',
		'default' 		=> 'twilio',
		'args'			=> array(
			'options' => array(
				'twilio' 	=> 'Twilio',
				'firebase' 	=> 'Google Firebase',
				'aws'  		=> 'Amazon',
				'blksms' 	=> 'Bulk SMS',
				'bulksms' 	=> 'Bulk(s) SMS',
				'netgsm' 	=> 'Net GSM (Turkey)',
				'oursms' 	=> 'OurSMS',
				'smsalert' 	=> 'SMS Alert',
				'unifonic' 	=> 'Unifonic',
				'msg91' 	=> 'MSG91',
				'textlocal' => 'TextLocal'
			)	
		)
	),


	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_main',
		'id' 			=> 'm-del-0',
		'title' 		=> 'Remove 0 from phone number',
		'default' 		=> 'yes',
		'desc' 			=> 'Removes 0 from the starting'
	),



	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_cc',
		'id' 			=> 'r-enable-cc-field',
		'title' 		=> 'Enable Country Code Field',
		'default' 		=> 'yes',
		'desc' 			=> 'A valid phone number needs a country code. If disabled, the default one selected below is set as country code.'
	),


	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_cc',
		'id'			=> 'r-countries',
		'title' 		=> 'Allowed Countries',
		'default' 		=> 'geolocation',
		'desc' 			=> 'Leave empty to allow all countries',
		'args'			=> array(
			'options' => $phone_codes,
			'select2box' 	=> true,
			'multiple' 		=> true
		)
	),


	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_cc',
		'id'			=> 'r-default-country-code-type',
		'title' 		=> 'Default Country Code',
		'default' 		=> 'geolocation',
		'desc' 			=> 'Geolocation is auto detect user location.',
		'args'			=> array(
			'options' => array(
				'geolocation'  	=> 'Geolocation',
				'custom'   		=> 'Custom',
			)	
		)
	),


	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_cc',
		'id'			=> 'r-default-country-code',
		'title' 		=> 'Default Country Code',
		'default' 		=> '+1',
		'args'			=> array(
			'options' 		=> $phone_codes,
			'select2box' 	=> true,
		)
	),

	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_cc',
		'id'			=> 'm-show-country-code-as',
		'title' 		=> 'Display Country Code Field as',
		'default' 		=> 'selectbox',
		'desc' 			=> '',
		'args'			=> array(
			'options' => array(
				'selectbox' => 'Select Box',
				'input'   	=> 'Input Text',
			)	
		)
	),


	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_cc',
		'id' 			=> 'm-cc-merge',
		'title' 		=> 'Merge with Phone Number',
		'default' 		=> 'no',
		'desc' 			=> 'Works with "select box" field style'
	),


	array(
		'callback' 		=> 'number',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'otp-digits',
		'title' 		=> 'OTP Digits',
		'default' 		=> '4',
	),


	array(
		'callback' 		=> 'number',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'otp-resend-limit',
		'title' 		=> 'Resend OTP Limit',
		'default' 		=> '8',
		'desc' 			=> 'Maximum number of times OTP can be resent'
	),

	array(
		'callback' 		=> 'number',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'otp-resend-wait',
		'title' 		=> 'Resend OTP Wait Time',
		'default' 		=> '30',
		'desc'			=> 'Waiting time to resend a new OTP (In seconds) '
	),


	array(
		'callback' 		=> 'number',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'otp-incorrect-limit',
		'title' 		=> 'Incorrect OTP Limit',
		'default' 		=> '10',
		'desc' 			=> 'Maximum number of tries allowed before user gets banned'
	),


	array(
		'callback' 		=> 'number',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'otp-ban-time',
		'title' 		=> 'Ban Time',
		'default' 		=> '600',
		'desc' 			=> 'Time in seconds'
	),


	array(
		'callback' 		=> 'number',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'otp-expiry',
		'title' 		=> 'OTP Expiry',
		'default' 		=> '120',
		'desc' 			=> 'In Seconds. OTP expiration time'
	),	


	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_otp',
		'id'			=> 'm-otp-form-type',
		'title' 		=> 'OTP Form Type',
		'default' 		=> 'inline_input',
		'args'			=> array(
			'options' => array(
				'inline_input' 		=> 'Inline Verify Button',
				'external_form'   	=> 'External Form',
			)	
		),
	),

	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_reg',
		'id' 			=> 'r-enable-phone',
		'title' 		=> 'Enable Phone Verification',
		'default' 		=> 'yes',
		'desc' 			=> ''
	),


	

	array(
		'callback' 		=> 'select',
		'section_id' 	=> 'ph_reg',
		'id' 			=> 'r-phone-field',
		'title' 		=> 'Phone Field',
		'default' 		=> 'required',
		'args'			=> array(
			'options' => array(
				'show_optional' 	=> 'Show as optional',
				'required' 			=> 'Required',
			)
		)
	),


	array(
		'callback' 		=> 'textarea',
		'section_id' 	=> 'ph_reg',
		'id' 			=> 'r-sms-txt',
		'title' 		=> 'SMS Text',
		'desc' 			=> 'Shortcodes: [otp]',
		'default' 		=> __("[otp] is your One Time Verification(OTP) to confirm your phone no at xootix.",'mobile-login-woocommerce')
	),


	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_reg',
		'id' 			=> 'r-auto-submit',
		'title' 		=> 'Auto submit form',
		'desc' 			=> 'Auto submit registration form on otp verification.',
		'default' 		=> 'yes'
	),

	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_login',
		'id' 			=> 'l-enable-login-with-otp',
		'title' 		=> 'Enable Login with OTP',
		'default' 		=> 'yes',
		'desc' 			=> ''
	),


	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_login',
		'id' 			=> 'm-email-otp',
		'title' 		=> 'Login with email OTP',
		'default' 		=> 'yes',
		'desc' 			=> 'Allow users to send OTP to email for login',
		'pro' 			=> 'yes'
	),


	array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'ph_login',
		'id' 			=> 'l-login-display',
		'title' 		=> 'Display OTP login form first',
		'default' 		=> 'yes',
		'desc' 			=> ''
	),

	array(
		'callback' 		=> 'text',
		'section_id' 	=> 'ph_login',
		'id' 			=> 'l-redirect',
		'title' 		=> 'Login with OTP Redirect',
		'desc' 			=> 'Leave empty to redirect on the same page',
		'default' 		=> '',
	),

);


if( class_exists( 'woocommerce' ) ){
	$settings[] = array(
		'callback' 		=> 'checkbox',
		'section_id' 	=> 'woocommerce',
		'id' 			=> 'wc-en-chk',
		'title' 		=> 'Enable OTP verification',
		'default' 		=> 'yes',
		'pro' 			=> 'yes'
	);


	$settings[] = array(
		'callback' 		=> 'number',
		'section_id' 	=> 'woocommerce',
		'id'			=> 'wc-chk-priority',
		'title' 		=> 'Phone Field Location',
		'default' 		=> 200,
		'desc' 			=> 'Where should field appear in checkout form, woocommerce default billing phone value is 100',
		'pro' 			=> 'yes'
	);


	$settings[] = array(
		'callback' 		=> 'select',
		'section_id' 	=> 'woocommerce',
		'id'			=> 'wc-chk-bphone',
		'title' 		=> 'Woocommerce Billing Phone field',
		'default' 		=> 'disable_merge',
		'args'			=> array(
			'options' => array(
				'disable' 			=> 'Disable',
				'disable_merge'  	=> 'Disable & merge with OTP field',
				'nothing' 			=> 'Do nothing'
			)	
		),
		'desc' 			=> 'option to treat default woocommerce billing phone field',
		'pro' 			=> 'yes'
	);


	$settings[] = array(
		'callback' 		=> 'select',
		'section_id' 	=> 'woocommerce',
		'id'			=> 'wc-chk-otp-form-type',
		'title' 		=> 'OTP Form Type',
		'default' 		=> 'inline_input',
		'args'			=> array(
			'options' => array(
				'inline_input' 		=> 'Inline Input',
				'external_form'   	=> 'External Form',
			)	
		),
		'pro' 			=> 'yes'
	);

}

$popupLinks = array();
if( function_exists('xoo_el') ){
	$popupLinks[admin_url('admin.php?page=xoo-el-fields')] = 'Fields';
	$popupLinks[admin_url('admin.php?page=easy-login-woocommerce-settings')] = 'Settings';
}
else{
	$popupLinks['https://wordpress.org/plugins/easy-login-woocommerce/'] = 'Download';
	$popupLinks['https://demo.xootix.com/mobile-login-for-woocommerce/#login'] = 'Demo';
}


$settings[] = array(
	'callback' 		=> 'links',
	'section_id' 	=> 'ph_popup',
	'id'			=> 'fake',
	'title' 		=> 'Login/Signup popup',
	'args'			=> array(
		'options' => $popupLinks
	)
); 




$settings[] = array(
	'callback' 		=> 'select',
	'section_id' 	=> 'ph_popup',
	'id'			=> 'el-otp-form-type',
	'title' 		=> 'OTP Form Type',
	'default' 		=> 'external_form',
	'args'			=> array(
		'options' => array(
			'inline_input' 		=> 'Inline Input',
			'external_form'   	=> 'External Form',
		)	
	)
); 



return apply_filters( 'xoo_ml_admin_settings', $settings, 'phone' );


?>