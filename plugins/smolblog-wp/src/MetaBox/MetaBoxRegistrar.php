<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package Smolblog\WP
 */

namespace Smolblog\WP\MetaBox;

use WebDevStudios\OopsWP\Structure\Service;
use WebDevStudios\OopsWP\Structure\MetaBox\MetaBoxInterface;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class MetaBoxRegistrar extends Service {

	/**
	 * List of MetaBox classes that should be registered
	 * by this service
	 *
	 * @var Array $post_types array of PostType classes
	 * @since 0.1.0
	 */
	protected $metaboxes = [
		SocialConnection::class,
	];

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'add_meta_boxes', [ $this, 'register_metaboxes' ] );
	}

	/**
	 * Iterate through $metaboxes and register them.
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_metaboxes() {
		foreach ( $this->metaboxes as $metabox_class ) {
			$metabox = new $metabox_class();
			$this->register_content( $metabox );
		}
	}

	/**
	 * Register the given instantiated content class. This function
	 * largely exists as a check to make sure we are passing the correct
	 * object class.
	 *
	 * @param MetaBoxInterface $content_type Content type (post type, taxonomy) to register.
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	private function register_content( MetaBoxInterface $content_type ) {
		$content_type->register();
	}
}
