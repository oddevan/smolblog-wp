<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package Smolblog\WP
 */

namespace Smolblog\WP\Content;

use WebDevStudios\OopsWP\Structure\Service;
use WebDevStudios\OopsWP\Structure\Content\ContentTypeInterface;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class ContentRegistrar extends Service {

	/**
	 * List of PostType classes that should be registered
	 * by this service
	 *
	 * @var Array $post_types array of PostType classes
	 * @since 0.1.0
	 */
	protected $post_types = [
		PostType\SocialConnection::class,
	];

	/**
	 * List of Taxonomy classes that should be registered
	 * by this service
	 *
	 * @var Array $taxonomies array of Taxonomy classes
	 * @since 0.1.0
	 */
	protected $taxonomies = [];

	/**
	 * List of ApiEndpoint classes that should be registered
	 * by this service
	 *
	 * @var Array $endpoints array of ApiEndpoint classes
	 * @since 0.1.0
	 */
	protected $endpoints = [
		Endpoint\TwitterCallback::class,
	];

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register_post_types' ] );
		add_action( 'init', [ $this, 'register_taxonomies' ] );
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	/**
	 * Iterate through $post_types and register them.
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_post_types() {
		foreach ( $this->post_types as $post_type_class ) {
			$post_type = new $post_type_class();
			$this->register_content( $post_type );
		}
	}

	/**
	 * Iterate through $taxonomies and register them.
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_taxonomies() {
		foreach ( $this->taxonomies as $taxonomy_class ) {
			$taxonomy = new $taxonomy_class();
			$this->register_content( $taxonomy );
		}
	}

	/**
	 * Iterate through $endpoints and register them.
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_endpoints() {
		foreach ( $this->endpoints as $endpoint_class ) {
			$endpoint = new $endpoint_class();
			$this->register_content( $endpoint );
		}
	}

	/**
	 * Register the given instantiated content class. This function
	 * largely exists as a check to make sure we are passing the correct
	 * object class.
	 *
	 * @param ContentTypeInterface $content_type Content type (post type, taxonomy) to register.
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	private function register_content( ContentTypeInterface $content_type ) {
		$content_type->register();
	}
}
