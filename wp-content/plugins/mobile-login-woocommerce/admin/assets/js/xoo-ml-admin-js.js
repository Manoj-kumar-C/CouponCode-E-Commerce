jQuery(document).ready(function($){

	'use strict';

	//Hide defaul field if set to geoolocation
	$('select[name="xoo-ml-phone-options[r-default-country-code-type]"]').on( 'change', function(){
		var $cc = $('select[name="xoo-ml-phone-options[r-default-country-code]"').parents('.xoo-as-setting');
		$(this).val() === 'custom' ? $cc.show() : $cc.hide();
	} ).trigger('change');


	$('select[name="xoo-ml-phone-options[m-operator]"]').on( 'change', function(){

		var $linksEl = $( '.xoo-ml-opt-links' );

		$linksEl.find( 'li' ).hide();

		$linksEl.find( 'li[data-operator="'+ $(this).val() +'"]' ).show();

		$('.xoo-ml-notice').hide();


		var $smsText 	= $('textarea[name="xoo-ml-phone-options[r-sms-txt]"]'),
			$digits 	= $('input[name="xoo-ml-phone-options[otp-digits]"]');

		if( $(this).val() === 'firebase' ){

			$smsText.add( $digits ).addClass('fb-disabled');

			if( !$smsText.siblings('.fb-disabled-txt').length ){
				$smsText.add( $digits ).after('<span class="fb-disabled-txt">Cannot be edited with Google Firebase operator. See templates tab under firebase console settings.</span>');
			}

			$digits.val(6);
		}
		else{
			$smsText.add( $digits ).removeClass('fb-disabled');
		}

	} ).trigger('change');



	$( 'textarea[name="xoo-ml-services-options[fb-config]"]' ).on( 'change', function(){
		$(this).val( $(this).val().replace( 'const firebaseConfig =', '' ) );
	} );

	$('.xoo-sc-tab-content[data-tab="services"] .xoo-as-field input').each( function( index, el ){
		if( $(el).val().length ){
			$('.xoo-ml-service-info').hide();
			return false;
		}
	} )


});
