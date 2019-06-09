<?php
/**
 * Main class for the Smolblog plugin
 *
 * @package Smolblog\WP
 * @since 2019-05-29
 */

namespace Smolblog\WP;

use WebDevStudios\OopsWP\Utility\Hookable;

require_once '../smolblog-config.php';

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
			add_action( 'admin_init', [ $this, 'handle_initiate_oauth' ] );
	}

	public function handle_initiate_oauth() {
		if ( isset( $_GET['smolblog_action'] ) ) {
			switch( $_GET['smolboog_action'] ) {
				case 'twitter_engage' :
				$connection = new Abraham\TwitterOAuth\TwitterOAuth( SMOLBLOG_TWITTER_APPLICATION_KEY, SMOLBLOG_TWITTER_APPLICATION_SECRET );

				
			}
		}
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
			echo '<p><a href="?page=smolblog&amp;action=twitter-connect" class="button">Sign in with Twitter</a></p>';
		}
	}
}
