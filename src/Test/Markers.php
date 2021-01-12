<?php

namespace AgentFire\Plugin\Test;

/**
 * Class Markers
 * @package AgentFire\Plugin
 */
class Markers { 

	/**
	* Get all markers tags
	* @return array of objects WP_Term
	*/
	public static function get_marker_tags() {
		$terms = get_terms( [
			'taxonomy' => 'post_tag',
			'hide_empty' => false,
		] );

		return $terms;
	}

	/**
	* Add marker
	* 	@param string $marker_name
	* 	@param string $lat
	* 	@param string $lng
	* 	@param string $tags
	* 	@param string $user
	* 
	* @return true or WP_Error
	*/
	public static function add_marker( $marker_name, $lat, $lng, $tags, $user ) {
		$postarr = array(
			"post_author" => $user,
			"post_title"  => $marker_name,
			"post_status" => "publish",
			"post_type"	  => "markers",
		);
		$marker_id = wp_insert_post( $postarr, $wp_error = true);
		if (is_wp_error( $marker_id )) {
			return $marker_id;
		}

		//set marker map position
		update_field('lat', $lat, $marker_id);
		update_field('lon', $lng, $marker_id);
		// update marker tags
		wp_set_object_terms( $marker_id, $tags, 'marker_tag');

		return true;
	}

	/**
	* Add marker
	* 	@param string $marker_name
	* 	@param string $lat
	* 	@param string $lng
	* 	@param string $tags
	* 	@param string $user
	* 
	* @return true or WP_Error
	*/
	public static function get_all_markers_as_geojson() {

		$data = array();
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'markers',
		);
		$query = new \WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();

			$name = get_the_title(); 
			$lat = get_field('lat');
			$lng = get_field('lon');

			//different color for marker author and others
			if( is_user_logged_in() && is_author(get_current_user_id()) ) {
				$marker_color = "#ff8888";
			} else {
				$marker_color = "#ff88ff";
			}

			$properties = array(
				"title"=> $name,
		        "marker-color" => $marker_color,
		    );

		    $date = get_the_date('Y-m-d H:i:s');
		    $properties['description'] = $date;

			$tags = get_the_terms( get_the_ID(), 'marker_tag');
			if ((!is_wp_error( $tags )) and ($tags !== false)) { 
				$tag_names  = array();
				foreach ($tags as $tag) {
					$properties[$tag->slug] = true;
					$tag_names[] = $tag->name; 
				}
				$properties['description'] .=  "<br><i>".implode( ', ', $tag_names ).'</i>';
			}

			$data[] = array(
				"type"=> "Feature",
				"geometry"=> array (
					"coordinates"=> array ((double)$lng, (double)$lat),
					"type" => "Point"
				),
				"properties" => $properties,
			);
		};
		$result = array(
			"type"=> 'FeatureCollection',
			"features" => $data
		);

		return $result;
	}
}