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

	function init() {

		//add bootstrap and select2
		add_action( 'wp_enqueue_scripts', array('\AgentFire\Plugin\Test','load_scripts_and_styles'));

		//add shortcode 
		add_shortcode( 'agentfire_test', array('\AgentFire\Plugin\Test','display_shortcode'));

		add_action( 'init', array('\AgentFire\Plugin\Test\Markers','add_markers_cpt'), 0 );

		self::add_acf_options();

	}

	public static function load_scripts_and_styles() {
		wp_register_script( 'bootstrap', AGENTFIRE_TEST_URI.'/bower_components/bootstrap/dist/js/bootstrap.min.js', array( 'jquery' ));
		wp_enqueue_script('bootstrap');
		wp_register_style( 'bootstrap', AGENTFIRE_TEST_URI.'/bower_components/bootstrap/dist/css/bootstrap.min.css');
		wp_enqueue_style( 'bootstrap' );

		wp_register_script( 'select2', AGENTFIRE_TEST_URI.'/bower_components/select2/dist/js/select2.min.js', array( 'jquery' ));
		wp_enqueue_script( 'select2');
		wp_register_style( 'select2', AGENTFIRE_TEST_URI.'/bower_components/select2/dist/css/select2.min.css');
		wp_enqueue_style( 'select2' );


	}

	public static function display_shortcode() { 
		$test = Template::getInstance()->display( 'main.twig' );
	}

	public static function register_endpoints()
	{


	}

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

}
