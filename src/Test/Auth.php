<?php

namespace AgentFire\Plugin\Test;

/**
 * Class Auth - User actions and validaions
 * @package AgentFire\Plugin
 */
class Auth { 

	/** 
	* Load all scripts and styles
	*/
	public static function get_current_user_name () {
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			return $current_user->user_login;
		} else {
			return "";
		}
	}

	/** 	
	* Checks user name and returns user ID if exist
	* 	@param string $username
	* 
	* @return int or WP_Error
	*/
	public static function get_user_ID( $user_name ) {
		$user = get_user_by( 'login', $user_name );
		if (!$user) {
			return new \WP_Error( 'unknown-user', 'Unknown user:'.$user_name );
		} else {
			return $user->ID;
		}

	}

	/** 	
	* Checks nonse with simple current user id solted
	* 	@param string $nonce
	* 	@param string $user
	* 
	* @return bool
	*/
	public static function check_nonce( $nonce, $user) {
		// echo ('nonce action____'.'add_marker:'.$user);
		$check_nonce = wp_verify_nonce( $nonce, 'add_marker:'.$user);
		if( $check_nonce == false ) {
			return true;
		} else {
			return false;
		}
	}
}

