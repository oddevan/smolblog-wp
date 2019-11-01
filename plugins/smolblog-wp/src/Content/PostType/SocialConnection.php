<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom Post Type for storing social connection (Twitter, Tumblr, etc.) information
 *
 * @since 0.1.0
 * @package Smolblog\WP
 */

namespace Smolblog\WP\Content\PostType;

use WebDevStudios\OopsWP\Structure\Content\PostType;

/**
 * Custom post type for social connections.
 *
 * @since 0.1.0
 */
class SocialConnection extends PostType {
	/**
	 * Permalink slug for this post type
	 *
	 * @var string $slug Permalink prefix
	 * @since 0.1.0
	 */
	protected $slug = 'sb_socialconnection';

	/**
	 * Override the superclass method and provide the labels array
	 * for registering the Connection post type
	 *
	 * @return Array labels for post type.
	 * @author me@eph.me
	 * @since 0.1.0
	 */
	protected function get_labels() : array {
		return [
			'name'                  => _x( 'Social Connections', 'Post Type General Name', 'smolblog' ),
			'singular_name'         => _x( 'Social Connection', 'Post Type Singular Name', 'smolblog' ),
			'menu_name'             => __( 'Social Connections', 'smolblog' ),
			'name_admin_bar'        => __( 'Social Connection', 'smolblog' ),
			'archives'              => __( 'Connection Archives', 'smolblog' ),
			'attributes'            => __( 'Connection Attributes', 'smolblog' ),
			'parent_item_colon'     => __( 'Parent Item:', 'smolblog' ),
			'all_items'             => __( 'All Social Connections', 'smolblog' ),
			'add_new_item'          => __( 'Add New Connection', 'smolblog' ),
			'add_new'               => __( 'Add New', 'smolblog' ),
			'new_item'              => __( 'New Connection', 'smolblog' ),
			'edit_item'             => __( 'Edit Connection', 'smolblog' ),
			'update_item'           => __( 'Update Connection', 'smolblog' ),
			'view_item'             => __( 'View Connection', 'smolblog' ),
			'view_items'            => __( 'View Connections', 'smolblog' ),
			'search_items'          => __( 'Search Social Connection', 'smolblog' ),
			'not_found'             => __( 'Not found', 'smolblog' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'smolblog' ),
			'featured_image'        => __( 'Featured Image', 'smolblog' ),
			'set_featured_image'    => __( 'Set featured image', 'smolblog' ),
			'remove_featured_image' => __( 'Remove featured image', 'smolblog' ),
			'use_featured_image'    => __( 'Use as featured image', 'smolblog' ),
			'insert_into_item'      => __( 'Insert into item', 'smolblog' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'smolblog' ),
			'items_list'            => __( 'Connections list', 'smolblog' ),
			'items_list_navigation' => __( 'Connections list navigation', 'smolblog' ),
			'filter_items_list'     => __( 'Filter Connections list', 'smolblog' ),
		];
	}

	/**
	 * Override the superclass method and provide the args array
	 * for registering the Connection post type
	 *
	 * @return Array information for post type.
	 * @author me@eph.me
	 * @since 0.1.0
	 */
	protected function get_args() : array {
		return [
			'label'             => __( 'Social Connection', 'smolblog' ),
			'description'       => __( 'Connection information for importing/exporting information to/from an external website', 'smolblog' ),
			'supports'          => [ 'title', 'custom-fields' ],
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'menu_position'     => 5,
			'menu_icon'         => 'dashicons-share-alt',
			'show_in_admin_bar' => false,
			'can_export'        => false,
			'has_archive'       => false,
			'rewrite'           => false,
			'capability_type'   => 'post',
			'show_in_rest'      => true,
		];
	}
}
