<?php

/**
 * Area Test
 *
 * @link https://agentfire.com
 * @since 1.0.0
 * @package AgentFire\Plugin
 *
 * @wordpress-plugin
 * Plugin Name: AgentFire Test
 * Description: Area Test plugin
 * Plugin URI: https://agentfire.com
 * Version: 1.0.0
 * Author: Roman Prokopyshyn
 * License: Proprietary
 * Network: false
 */

namespace AgentFire\Plugin;

define( 'AGENTFIRE_TEST_PATH', plugin_dir_path( __FILE__ ) );

define( 'AGENTFIRE_TEST_URI', plugin_dir_url( __FILE__ ) );


require AGENTFIRE_TEST_PATH . 'vendor/autoload.php';

add_action( 'plugins_loaded', function() {
	new Test();
} );
