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
	protected $slug = 'smolblog_socialconnection';

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
			'supports'    => [ 'custom-fields' ],
			'has_archive' => true,
			'taxonomies'  => [ 'category' ],
		];
	}
}
