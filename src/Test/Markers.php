<?php

namespace AgentFire\Plugin\Test;

/**
 * Class Markers
 * @package AgentFire\Plugin
 */
class Markers { 
	function __construct() {
	}

	public static function get_marker_tags() {
		$terms = get_terms( [
			'taxonomy' => 'post_tag',
			'hide_empty' => false,
		] );

		return $terms;
	}

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

		update_field('lat', $lat, $marker_id);
		update_field('lon', $lng, $marker_id);

		wp_set_object_terms( $marker_id, $tags, 'marker_tag');

		return true;
	}

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

			$properties = array(
				"title"=> $name,
			    "marker-symbol" => "star",
		        "marker-color" => "#ff8888",
		    );
			$tags = get_the_terms( get_the_ID(), 'marker_tag');
			if ((!is_wp_error( $tags )) and ($tags !== false)) { 
				foreach ($tags as $tag) {
					$properties[$tag->slug] = true;
				}
			}

			$data[] = array(
				"type"=> "Feature",
				"geometry"=> array (
					"coordinates"=> array ((double)$lat, (double)$lng),
					"type" => "Point"
				),
				"properties" => $properties,
			);
		};
		$result = array(
			"type"=> 'FeatureCollection',
			"features" => $data
		);

		return json_encode($result);
	}
}