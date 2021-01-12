<?php

declare( strict_types=1 );

namespace AgentFire\Plugin;

use AgentFire\Plugin\Test\Template;
use AgentFire\Plugin\Test\Markers;
/**
 * Class Test
 * @package AgentFire\Plugin
 */
class Test {

	function __construct() {
		$this->init();
	}

	/** 
	* Initialization
	*
	*/
	function init() {
		//add bootstrap and select2
		add_action( 'wp_enqueue_scripts', array('\AgentFire\Plugin\Test','load_scripts_and_styles'));
		//add shortcode 
		add_shortcode( 'agentfire_test', array('\AgentFire\Plugin\Test','display_shortcode'));
		add_action( 'init', array('\AgentFire\Plugin\Test','add_markers_cpt'), 0 );
		self::add_acf_options();
		\AgentFire\Plugin\Test\RestAPI::register_endpoints();
	}

	/** 
	* Load all scripts and styles
	*/
	public static function load_scripts_and_styles() {
		wp_register_script( 'bootstrap', AGENTFIRE_TEST_URI.'/bower_components/bootstrap/dist/js/bootstrap.min.js', array( 'jquery' ));
		wp_enqueue_script('bootstrap');
		wp_register_style( 'bootstrap', AGENTFIRE_TEST_URI.'/bower_components/bootstrap/dist/css/bootstrap.min.css');
		wp_enqueue_style( 'bootstrap' );

		wp_register_script( 'select2', AGENTFIRE_TEST_URI.'/bower_components/select2/dist/js/select2.min.js', array( 'jquery' ));
		wp_enqueue_script( 'select2');
		wp_register_style( 'select2', AGENTFIRE_TEST_URI.'/bower_components/select2/dist/css/select2.min.css');
		wp_enqueue_style( 'select2' );


		wp_register_script( 'mapbox-js', 'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js');
		wp_enqueue_script( 'mapbox-js');
		wp_register_style( 'mapbox-css', 'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css');
		wp_enqueue_style( 'mapbox-css' );

		wp_enqueue_script('agentfire-test', AGENTFIRE_TEST_URI . 'dist/js/main.js', ['jquery'], NULL, TRUE);

		wp_localize_script('agentfire-test', 'AgentFireTest', [
			'nonce' => wp_create_nonce('add_marker:'.get_current_user_id()),
			'current_user' => Test\Auth::get_current_user_name()
		]);
		
	}

	/** 
	* Show shortcode content 
	*/
	public static function display_shortcode() { 
		$tags = Markers::get_marker_tags();
		$all_markers = json_encode(Markers::get_all_markers_as_geojson());
		$test = Template::getInstance()->display( 'main.twig', 
			[
				'mapbox_api_key' 	=> get_field( 'mapbox_api_key','option' ),
				'tags'				=> $tags, 
				'geo_data'			=> $all_markers
			]);
	}

	/** 
	* Add acf fields, and option pages
	*/
	public static function add_acf_options() {

		if( function_exists('acf_add_options_page') ) {

			acf_add_options_page(array(
				'page_title' 	=> 'AgentFire Test Settings',
				'menu_title'	=> 'AF Settings',
				'menu_slug' 	=> 'af-general-settings',
				'capability'	=> 'edit_posts',
				'redirect'		=> false
			));

		}

		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array(
				'key' => 'group_5ffb0256bbd9e',
				'title' => 'Agent Fire Settings',
				'fields' => array(
					array(
						'key' => 'field_5ffb0260ea6be',
						'label' => 'MapBox API Key',
						'name' => 'mapbox_api_key',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'af-general-settings',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));

		endif;

		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array(
				'key' => 'group_5ffb3660d17e1',
				'title' => 'Marker Options',
				'fields' => array(
					array(
						'key' => 'field_5ffb366f9e153',
						'label' => 'Latitude',
						'name' => 'lat',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5ffb36849e154',
						'label' => 'Longtitude',
						'name' => 'lon',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'markers',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));

		endif;

	}

	/** 
	* Add Markers and Tags
	*/
	public static function add_markers_cpt() {

		$labels = array(
			'name'                  => _x( 'Markers', 'Post Type General Name', 'af-test' ),
			'singular_name'         => _x( 'Marker', 'Post Type Singular Name', 'af-test' ),
			'menu_name'             => __( 'Markers', 'af-test' ),
			'name_admin_bar'        => __( 'Marker', 'af-test' ),
			'archives'              => __( 'Item Archives', 'af-test' ),
			'attributes'            => __( 'Item Attributes', 'af-test' ),
			'parent_item_colon'     => __( 'Parent Item:', 'af-test' ),
			'all_items'             => __( 'All Items', 'af-test' ),
			'add_new_item'          => __( 'Add New Item', 'af-test' ),
			'add_new'               => __( 'Add New', 'af-test' ),
			'new_item'              => __( 'New Item', 'af-test' ),
			'edit_item'             => __( 'Edit Item', 'af-test' ),
			'update_item'           => __( 'Update Item', 'af-test' ),
			'view_item'             => __( 'View Item', 'af-test' ),
			'view_items'            => __( 'View Items', 'af-test' ),
			'search_items'          => __( 'Search Item', 'af-test' ),
			'not_found'             => __( 'Not found', 'af-test' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'af-test' ),
			'featured_image'        => __( 'Featured Image', 'af-test' ),
			'set_featured_image'    => __( 'Set featured image', 'af-test' ),
			'remove_featured_image' => __( 'Remove featured image', 'af-test' ),
			'use_featured_image'    => __( 'Use as featured image', 'af-test' ),
			'insert_into_item'      => __( 'Insert into item', 'af-test' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'af-test' ),
			'items_list'            => __( 'Items list', 'af-test' ),
			'items_list_navigation' => __( 'Items list navigation', 'af-test' ),
			'filter_items_list'     => __( 'Filter items list', 'af-test' ),
		);
		$args = array(
			'label'                 => __( 'Marker', 'af-test' ),
			'description'           => __( 'Markers', 'af-test' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'taxonomies'            => array( 'marker_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-location',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'markers', $args );

		// Register Custom Taxonomy

		$labels = array(
			'name'                       => _x( 'Tags', 'Taxonomy General Name', 'text_domain' ),
			'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'text_domain' ),
			'menu_name'                  => __( 'Tags', 'text_domain' ),
			'all_items'                  => __( 'All Tags', 'text_domain' ),
			'parent_item'                => __( 'Parent Tag', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Tag:', 'text_domain' ),
			'new_item_name'              => __( 'New Tag Name', 'text_domain' ),
			'add_new_item'               => __( 'Add New Tag', 'text_domain' ),
			'edit_item'                  => __( 'Edit Tag', 'text_domain' ),
			'update_item'                => __( 'Update Tag', 'text_domain' ),
			'view_item'                  => __( 'View Tag', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate Tags with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove Tags', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular Tags', 'text_domain' ),
			'search_items'               => __( 'Search Tags', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No Tags', 'text_domain' ),
			'items_list'                 => __( 'Tags list', 'text_domain' ),
			'items_list_navigation'      => __( 'Tags list navigation', 'text_domain' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'marker_tag', array( 'markers' ), $args );
	}
}