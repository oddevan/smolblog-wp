<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Endpoint for the Twitter OAuth Callback
 *
 * @since 0.1.0
 * @package Smolblog\WP
 */

namespace Smolblog\WP\Content\Endpoint;

use WebDevStudios\OopsWP\Structure\Content\ApiEndpoint;

/**
 * Class to register our custom post types
 *
 * @since 0.1.0
 */
class TwitterCallback extends ApiEndpoint {
	/**
	 * Namespace for this endpoint
	 *
	 * @since 2019-05-01
	 * @var   string
	 */
	protected $namespace = 'smolblog/v1';

	/**
	 * Route for this endpoint
	 *
	 * @since 2019-05-01
	 * @var   string
	 */
	protected $route = '/twitter/callback';

	protected function get_args() : array {
		return [
			'methods' => [ 'POST' ],
		];
	}

	/**
	 * Render the metabox to the screen
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 *
	 * @param WP_REST_Request $request Current post object.
	 * @return void used as control structure only.
	 */
	public function run( WP_REST_Request $request = null ) {
		if ( ! $request ) {
			return;
		}
		echo '<pre>' . print_r( $request, true ) . '</pre>';
	}
}
