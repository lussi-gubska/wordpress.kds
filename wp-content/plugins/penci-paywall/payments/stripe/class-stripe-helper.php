<?php

namespace PenciPaywall\Payments\Stripe;

class Stripe_Helper {
    
    public function __construct() {
       /** Do nothing */
    }
    
    /**
	 * Get localized error message.
	 *
	 * @return string
	 */
	public function get_error_message( $error ) {
        if ( 'resource_missing' === $error->code ) {
            return esc_html__( 'Please try to re-input your card or use another card', 'penci-paywall' );
        }

        $messages = $this->get_messages();

		$message = esc_html__( 'Unable to process your payment, please try again later', 'penci-paywall' );
		if ( 'card_error' === $error->type ) {
			$message = isset( $messages[ $error->code ] ) ? $messages[ $error->code ] : $error->message;
		} else {
			$message = isset( $messages[ $error->type ] ) ? $messages[ $error->type ] : $error->message;
		}

		return $message;
	}

	/**
	 * Get array error message.
	 *
	 * @return string
	 */
	public function get_messages() {
		return [
			'invalid_number'			=> esc_html__( 'The card number is not a valid credit card number.', 'penci-paywall' ),
			'invalid_expiry_month'     	=> esc_html__( 'The card\'s expiration month is invalid.', 'penci-paywall' ),
			'invalid_expiry_year'      	=> esc_html__( 'The card\'s expiration year is invalid.', 'penci-paywall' ),
			'invalid_cvc'              	=> esc_html__( 'The card\'s security code is invalid.', 'penci-paywall' ),
			'incorrect_number'         	=> esc_html__( 'The card number is incorrect.', 'penci-paywall' ),
			'incomplete_number'        	=> esc_html__( 'The card number is incomplete.', 'penci-paywall' ),
			'incomplete_cvc'           	=> esc_html__( 'The card\'s security code is incomplete.', 'penci-paywall' ),
			'incomplete_expiry'        	=> esc_html__( 'The card\'s expiration date is incomplete.', 'penci-paywall' ),
			'expired_card'             	=> esc_html__( 'The card has expired.', 'penci-paywall' ),
			'incorrect_cvc'            	=> esc_html__( 'The card\'s security code is incorrect.', 'penci-paywall' ),
			'incorrect_zip'            	=> esc_html__( 'The card\'s zip code failed validation.', 'penci-paywall' ),
			'invalid_expiry_year_past'	=> esc_html__( 'The card\'s expiration year is in the past', 'penci-paywall' ),
			'card_declined'            	=> esc_html__( 'The card was declined.', 'penci-paywall' ),
			'missing'                  	=> esc_html__( 'There is no card on a customer that is being charged.', 'penci-paywall' ),
			'processing_error'         	=> esc_html__( 'An error occurred while processing the card.', 'penci-paywall' ),
			'invalid_request_error'    	=> esc_html__( 'Unable to process this payment, please try again or use alternative method.', 'penci-paywall' ),
			'invalid_sofort_country'   	=> esc_html__( 'The billing country is not accepted by SOFORT. Please try another country.', 'penci-paywall' ),
			'email_invalid'            	=> esc_html__( 'Invalid email address, please correct and try again.', 'penci-paywall' ),
			'default_card_error'		=> esc_html__( 'We are unable to authenticate your payment method. Please choose a different payment method and try again.', 'penci-paywall' ),
			'parameter_invalid_empty'	=> esc_html__( 'Please make sure you have inputted billing detail required fields.', 'penci-paywall' ),
		];
	}

	/**
	 * Zero decimal currencies.
	 *
	 * @return string
	 */
	public function zero_decimal( $currency ) {
        $currencies = [
			'bif', // Burundian Franc
			'clp', // Chilean Peso
			'djf', // Djiboutian Franc
			'gnf', // Guinean Franc
			'jpy', // Japanese Yen
			'kmf', // Comorian Franc
			'krw', // South Korean Won
			'mga', // Malagasy Ariary
			'pyg', // Paraguayan Guaraní
			'rwf', // Rwandan Franc
			'ugx', // Ugandan Shilling
			'vnd', // Vietnamese Đồng
			'vuv', // Vanuatu Vatu
			'xaf', // Central African Cfa Franc
			'xof', // West African Cfa Franc
			'xpf', // Cfp Franc
		];
		
		return in_array( strtolower( $currency ), $currencies );
	}

	/**
	 * Sanitize statement descriptor text.
	 * 
	 * @return string
	 */
	public static function statement_descriptor( $statement_descriptor = '' ) {
		$disallowed_characters = array( '<', '>', '"', "'" );
		$statement_descriptor = str_replace( $disallowed_characters, '', $statement_descriptor );
		$statement_descriptor = substr( trim( $statement_descriptor ), 0, 22 );

		return $statement_descriptor;
	}

}
