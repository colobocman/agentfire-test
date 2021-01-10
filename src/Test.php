<?php

declare( strict_types=1 );

namespace AgentFire\Plugin;

use AgentFire\Plugin\Test\Template;
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
		add_shortcode( 'agentfire_test', array('\AgentFire\Plugin\Test','display_shortcode') );

	}

	public static function load_scripts_and_styles() {
		wp_register_script( 'bootstrap', AGENTFIRE_TEST_URI.'/bower_components/bootstrap/dist/js/bootstrap.min.js');
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


}


