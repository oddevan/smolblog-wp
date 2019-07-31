<?php
/**
 * Main class for the Smolblog plugin
 *
 * @package Smolblog\WP
 * @since 2019-05-29
 */

namespace Smolblog\WP;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Main class for the Smolblog plugin
 *
 * @author Evan Hildreth
 */
class Branding implements Hookable {
	/**
	 * All the hooks my object sets up, right in one place!
	 */
	public function register_hooks() {
		// Put your hooks here!
		add_filter( 'login_headertext', [ $this, 'change_login_logo' ] );
	}

	/**
	 * Change the WordPress logo on sign-in to a Smolblog logo
	 *
	 * @param string $login_header_text current text to override.
	 * @return string new login text.
	 */
	public function change_login_logo( $login_header_text ) {
		return '<span style="display:block;background:black;text:white;font-family:sans-serif">Smolblog</span>';
	}
}
