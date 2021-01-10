<?php

namespace AgentFire\Plugin\Test;

/**
 * Class Markers
 * @package AgentFire\Plugin
 */
class Markers { 
	function __construct() {

	}

	// Register Custom Post Type
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
			'taxonomies'            => array( 'post_tag' ),
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

	}
}