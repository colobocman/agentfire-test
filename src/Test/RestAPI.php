<?php

namespace AgentFire\Plugin\Test;

/**
 * Class RestAPI
 * @package AgentFire\Plugin
 */
class RestAPI { 

	/**
	* Add endpoints for add marker
	*/
	public static function register_endpoints()
	{
		add_action( 'rest_api_init', function () {
			register_rest_route( 'agentfire-test/v1', 'marker',array(
				'methods'  => 'POST',
				'callback' => __CLASS__.'::add_marker',
			));
			register_rest_route( 'agentfire-test/v1', 'markers',array(
				'methods'  => 'GET',
				'callback' => __CLASS__.'::get_markers',
			));
		} );
	}

	/** 	
	* Add marker endpoint handler
	* 	@param WP_REST_Request $request
	* 
	* @return WP_REST_Response 
	*/
	public static function add_marker( \WP_REST_Request $request ) {
		$body = $request->get_body();
		$body_parsed = json_decode( $body, $associative = true );

		// Check  and extraxt data from json
		if ( is_null( $body_parsed )) {
			return self::REST_error_response('Error parsing request body');
		} 

		// User simple validation
		$user = $body_parsed['username'];
		$user_ID = Auth::get_user_ID($user);
		if (is_wp_error( $user_ID)) {
			return self::REST_error_response($user_ID->get_error_message());
		} 

		// Check WP nonce and user auth
		$nonce = $body_parsed['nonce'];
		if ( Auth::check_nonce($nonce, $user_ID) == false ) {
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

	public static function get_markers() {
		$data = Markers::get_all_markers_as_geojson();

		return new \WP_REST_Response( $data );
	}

	/**
	* Generate REST API error response
	* @param string $message - text of error
	* @return WP_REST_Response 
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
	* @param string $message - text of success message
	* @return WP_REST_Response 
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