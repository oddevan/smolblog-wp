<?php
/**
 * Main class for the Smolblog plugin
 *
 * @package Smolblog\WP
 * @since 2019-05-29
 */

namespace Smolblog\WP;

use WebDevStudios\OopsWP\Utility\Hookable;
use Abraham\TwitterOAuth\TwitterOAuth;

require_once __DIR__ . '/../smolblog-config.php';

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
			add_action( 'admin_init', [ $this, 'handle_admin_actions' ] );
	}

	/**
	 * Handle URLs of the format [WP]/wp-admin/admin.php/smolblog/...
	 */
	public function handle_admin_actions() {
		if ( isset( $_SERVER['PATH_INFO'] ) ) {
			$path_info = explode( '/', $_SERVER['PATH_INFO'] );
			if ( 'smolblog' === $path_info[1] ) {
				if ( 'oauth' === $path_info[2] ) {
					if ( 'init' === $path_info[3] ) {
						if ( 'twitter' === $path_info[4] ) {
							$this->oauth_twitter_engage();
						}
					} elseif ( 'callback' === $path_info[3] ) {
						if ( 'twitter' === $path_info[4] ) {
							$this->oauth_twitter_callback();
						}
					}
				}
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
?>
		<h1>Smolblog</h1>

		<?php if ( get_option( 'smolblog_twitter_username' ) ) : ?>
			<p>Authenticated with Twitter as <?php echo esc_html( get_option( 'smolblog_twitter_username' ) ); ?></p>

			<p><a href="#" class="button">Import latest 10 tweets</a>
		<?php else : ?>
			<p><a href="/wp-admin/admin.php/smolblog/oauth/init/twitter" class="button">Sign in with Twitter</a></p>
		<?php endif; ?>
<?php
	}

	public function oauth_twitter_engage() {
		$callback_url = 'https://smolblog.local/wp-admin/admin.php/smolblog/oauth/callback/twitter';
		$connection   = new TwitterOAuth( SMOLBLOG_TWITTER_APPLICATION_KEY, SMOLBLOG_TWITTER_APPLICATION_SECRET );

		$request_token = $connection->oauth( 'oauth/request_token', array( 'oauth_callback' => $callback_url ) );

		set_transient( 'smolblog_twitter_oauth_request_' . get_current_blog_id(), $request_token, 5 * MINUTE_IN_SECONDS );

		$url = $connection->url( 'oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] ) );

		header( 'Location: ' . $url, true, 302 );
		die;
	}

	public function oauth_twitter_callback() {
		$request_token = get_transient( 'smolblog_twitter_oauth_request_' . get_current_blog_id() );

		if ( isset( $_REQUEST['oauth_token'] ) && $request_token['oauth_token'] !== $_REQUEST['oauth_token'] ) {
			wp_die( 'OAuth tokens did not match: Got "' . $_REQUEST['oauth_token'] . '" but expected "' . $request_token['oauth_token'] . '".' );
		}

		$connection = new TwitterOAuth( SMOLBLOG_TWITTER_APPLICATION_KEY,
																		SMOLBLOG_TWITTER_APPLICATION_SECRET,
																		$request_token['oauth_token'],
																		$request_token['oauth_token_secret'] );

		$access_token = $connection->oauth( 'oauth/access_token', [ 'oauth_verifier' => $_REQUEST['oauth_verifier'] ] );

		update_option( 'smolblog_oauth_twitter', $access_token, false );
		update_option( 'smolblog_twitter_username', $access_token['screen_name'], false );

		header( 'Location: /wp-admin/admin.php?page=smolblog', true, 302 );
	}
}
