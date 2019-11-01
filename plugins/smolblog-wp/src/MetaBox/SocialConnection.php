<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Metabox for the Social Connections
 *
 * @since 0.1.0
 * @package Smolblog\WP
 */

namespace Smolblog\WP\MetaBox;

use WebDevStudios\OopsWP\Structure\MetaBox\MetaBox;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class SocialConnection extends MetaBox {
	/**
	 * The ID attribute used to identify this meta box.
	 *
	 * @since 2019-05-01
	 * @var   string
	 */
		protected $id = 'sb_socialconnection_connect_metabox';

		/**
		 * The title displayed at the top of the meta box.
		 *
		 * @since 2019-05-01
		 * @var   string
		 */
		protected $title = 'Connect';

		/**
		 * The types of post in which this meta box should be displayed.
		 *
		 * @since 2019-05-01
		 * @var   string|array|WP_Screen Optional.
		 */
		protected $screen = 'sb_socialconnection';

		/**
		 * The context within the screen where the boxes should display.
		 *
		 * Post edit screen contexts include 'normal', 'side', and 'advanced'.
		 * Comments screen contexts include 'normal' and 'side'.
		 * Menus meta boxes all use the 'side' context.
		 *
		 * @since 2019-05-01
		 * @var   string Optional.
		 */
		protected $context = 'normal';

		/**
		 * The priority within the context where the boxes should show.
		 *
		 * Available values are 'high', 'low', and 'default'.
		 *
		 * @since 2019-05-01
		 * @var   string Optional.
		 */
		protected $priority = 'default';

		/**
		 * Data that should be set as the $args property of the meta box array
		 * (which is the second parameter passed to your callback).
		 *
		 * @since 2019-05-01
		 * @var   array Optional.
		 */
		protected $callback_args = null;

		/**
		 * Render the metabox to the screen
		 *
		 * @since 0.1.0
		 * @author Evan Hildreth <me@eph.me>
		 *
		 * @param WP_Post $the_post Current post object.
		 * @return void used as control structure only.
		 */
		public function render( $the_post = false ) {
			if ( ! $the_post ) {
				return;
			}
			echo '<pre>' . print_r( $the_post, true ) . '</pre>';
		}
}
