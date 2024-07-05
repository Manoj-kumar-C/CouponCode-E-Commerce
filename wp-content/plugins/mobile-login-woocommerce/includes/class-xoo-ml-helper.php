<?php

class Xoo_Ml_Helper extends Xoo_Helper{

	protected static $_instance = null;

	public $mergeCC;

	public static function get_instance( $slug, $path ){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $slug, $path );
		}
		return self::$_instance;
	}

	public function get_phone_option( $subkey = '' ){
		return $this->get_option( 'xoo-ml-phone-options', $subkey );
	}

	public function get_service_option( $subkey = '' ){
		return $this->get_option( 'xoo-ml-services-options', $subkey );
	}

	public function mergeCC(){
		if( !$this->mergeCC ){
			$this->mergeCC = $this->get_phone_option('m-cc-merge') === "yes" && $this->canMergeCC(); 
		}
		return $this->mergeCC;
	}

	public function canMergeCC(){
		return $this->get_phone_option('r-enable-cc-field') === "yes" && $this->get_phone_option('m-show-country-code-as') === 'selectbox';
	}

}

function xoo_ml_helper(){
	return Xoo_Ml_Helper::get_instance( 'mobile-login-woocommerce', XOO_ML_PATH );
}
xoo_ml_helper();

?>