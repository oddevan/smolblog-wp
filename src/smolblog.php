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
class Smolblog implements Hookable {
	/**
	 * All the hooks my object sets up, right in one place!
	 */
	public function register_hooks() {
			// Put your hooks here!
			add_action( 'admin_menu', [ $this, 'add_smolblog_dashboard_page' ] );
	}

	/**
	 * My init callback.
	 */
	public function add_smolblog_dashboard_page() {
		add_menu_page(
			'Smolblog Dashboard',
			'Smolblog',
			'manage_options',
			'smolblog',
			[ $this, 'smolblog_dashboard' ],
			'dashicons-controls-repeat',
			3
		);
	}

	/**
	 * Output the Smolblog dashboard page
	 */
	public function smolblog_dashboard() {
		echo '<h1>Smolblog</h1>';

		if ( get_option( 'smolblog_twitter_user_key' ) ) {
			echo '<p>Authenticated with Twitter as [account]</p>';
		} else {
			echo '<p>Sign in link coming soon.</p>';
		}
	}
}