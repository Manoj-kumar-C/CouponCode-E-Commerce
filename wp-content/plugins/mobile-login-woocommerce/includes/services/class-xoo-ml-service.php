<?php

class Xoo_Ml_Service{

	public $id, $username, $password, $url;

	public $hasSDK = false; //Needs PHP SDK to run

	public $notsetupError;


	public function sendSMS( $phone, $message ){

	}

	public function http_request( $args ){

		//Basic authorization
		if( isset( $this->username ) ){

			$headers =  isset( $args['headers'] ) ? $args['headers'] : array();

			$headers['Authorization'] = 'Basic ' . base64_encode( $this->username . ':' . $this->password );

			$args['headers'] = $headers;
		}

		$response = wp_remote_post(
			apply_filters( 'xoo_ml_service_http_url', $this->url, $this ),
			apply_filters( 'xoo_ml_service_http_args', $args, $this )
		);
		
		if( is_wp_error( $response ) ){
			return $response;
		}

	}

	public function include_sdk( $location ){
		if( $this->hasSDK && $this->has_sdk_folder() ){
			require_once xoo_ml_services()::$sdkDir . '/'.$location;
		}
	}


	public function get_operator_data( $key = '' ){
		return xoo_ml_services()->get_operator_data( $this->id, $key );
	}


	public function get_incomplete_setup_notice(){

		if( !isset( $this->notsetupError ) ){

			$operatorData 	= xoo_ml_services()->get_operator_data( $this->id );
			$docLink 		= sanitize_text_field( $this->get_operator_data('doc') );
			$title 			= sanitize_text_field( $this->get_operator_data('title') );

			$this->notsetupError = new WP_Error( 'incomplete', 'Your SMS operator '.$title.' setup is incomplete. Please check your settings and follow the <a href="'.$docLink.'" target="__blank">documentation</a>' );

		}

		return $this->notsetupError;
	}


	public function validate_incomplete_settings( $settings = array() ){

		$empty = false;

		foreach ( $settings as $setting => $value) {
			if( !$value ){
				$empty = true;
				break;
			}
		}

		if( $empty ){
			return $this->get_incomplete_setup_notice();
		}
	}

	public function validate_sdk(){
		if( $this->hasSDK && !$this->has_sdk_folder() ){
			return new WP_Error( 'nosdk', 'SDK Missing. Please upload the SDK file in your settings for your SMS operator. <a href="'.esc_attr( $this->get_operator_data( 'doc' ) ).'" target="__blank">Documentation.</a>' );
		}
	}

	public function validate( $settings = array(), $sdk = true ){

		if( !empty( $settings ) ){
			$settingsValidation = $this->validate_incomplete_settings( $settings );
			if( is_wp_error( $settingsValidation ) ){
				return $settingsValidation;
			}
		}

		if( $sdk ){
			$sdkValidation = $this->validate_sdk();
			if( is_wp_error( $sdkValidation ) ){
				return $sdkValidation;
			}
		}

	}

	public function has_sdk_folder(){
		$operatorData 	= xoo_ml_services()->get_operator_data( $this->id );
		return isset( $operatorData['location'] ) && $operatorData['location'];
	}
}