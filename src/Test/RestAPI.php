<?php

namespace AgentFire\Plugin\Test;

/**
 * Class RestAPI
 * @package AgentFire\Plugin
 */
class RestAPI { 

	public static function register_endpoints()
	{
		add_action( 'rest_api_init', function () {
			register_rest_route( 'agentfire-test/v1', 'marker',array(
				'methods'  => 'POST',
				'callback' => __CLASS__.'::add_marker',
				// 'permission_callback' => function() {
				// 	return current_user_can('edit_posts');
				// }
			));
		} );
	}


	/**
	* Add marker REST API endpoind handler
	* @WP_REST_Request $request - request data
	*/
	public static function add_marker( \WP_REST_Request $request ) {
		$body = $request->get_body();
		$body_parsed = json_decode( $body, $associative = true );

		if ( is_null( $body_parsed )) {
			return self::REST_error_response('Error parsing request body');
		} 

		$user = $body_parsed['username'];
		$user_ID = self::get_user_ID($user);
		if (is_wp_error( $user_ID)) {
			return self::REST_error_response($user_ID->get_error_message());
		} 

		$nonce = $body_parsed['nonce'];
		if ( self::check_nonce($nonce, $user_ID) == false ) {
			return self::REST_error_response("Nonce isn\'t valid");
		}

		$marker_name = $body_parsed['name'];
		$lat = $body_parsed['lat'];
		$lng = $body_parsed['lng'];
		$tags = $body_parsed['tags'];
		$user = $user_ID;


		$marker_added = Markers::add_marker($marker_name, $lat, $lng, $tags, $user);
		if (is_wp_error( $marker_added)) {
			return self::REST_error_response($marker_added->get_error_message());
		} else {
			return self::REST_success_response('Marker added!');
		}
	}

	private static function get_user_ID( $user_name ) {
		$user = get_user_by( 'login', $user_name );
		if (!$user) {
			return new \WP_Error( 'unknown-user', 'Unknown user:'.$user_name );
		} else {
			return $user->ID;
		}

	}

	private static function check_nonce( $nonce, $user) {
		// echo ('nonce action____'.'add_marker:'.$user);
		$check_nonce = wp_verify_nonce( $nonce, 'add_marker:'.$user);
		if( $check_nonce == false ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Generate REST API error response
	* @string $message - text of error
	*/
	public static function REST_error_response($message) 
	{
		$response = new \WP_REST_Response(
			array(
				'status' => 400,
				'message' => $message
			)
		);
		return $response;
	}

	/**
	* Generate REST API success response
	* @string $message - text of success message
	*/
	public static function REST_success_response($message) 
	{
		$response = new \WP_REST_Response(
			array(
				'status' => 200,
				'message' => $message
			)
		);
		return $response;
	}
}