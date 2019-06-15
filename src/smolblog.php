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

		<?php if ( ! empty ( $_GET['smolblog_action'] ) && 'import_twitter' === $_GET['smolblog_action'] ) : ?>
			<h2>Twitter import</h2>
			<pre>
			<?php $this->import_twitter(); ?>
			</pre>
		<?php elseif ( get_option( 'smolblog_twitter_username' ) ) : ?>
			<p>Authenticated with Twitter as <?php echo esc_html( get_option( 'smolblog_twitter_username' ) ); ?></p>

			<p><a href="?page=smolblog&amp;smolblog_action=import_twitter" class="button">Import latest 10 tweets</a>
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

	public function import_twitter() {
		$access_token = get_option( 'smolblog_oauth_twitter' );

		echo "Loading Twitter...\n";

		$twitter = new \TwitterAPIExchange( array(
			'oauth_access_token'        => SMOLBLOG_TWITTER_APPLICATION_KEY,
			'oauth_access_token_secret' => SMOLBLOG_TWITTER_APPLICATION_SECRET,
			'consumer_key'              => $access_token['oauth_token'],
			'consumer_secret'           => $access_token['oauth_token_secret'],
		));

		$url      = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getfield = '?count=100&trim_user=true&exclude_replies=true&include_rts=true&tweet_mode=extended';

		$twitter_json     = $twitter->setGetfield( $getfield )->buildOauth( $url, 'GET' )->performRequest();
		$twitter_response = json_decode( $twitter_json );

		$mkdwn = new \League\CommonMark\CommonMarkConverter();

		foreach ( $twitter_response as $tweet ) {
			$frontmatter = array(
				'date'           => $tweet->created_at,
				'slug'           => $tweet->id,
				'twitter_id'     => $tweet->id,
				'tags'           => array(),
				'categories'     => array(),
				'resources'      => array(),
				'format'         => 'aside',
				'reply_to_id'    => false,
				'thread_prev_id' => false,
			);

			$body = mb_substr( $tweet->full_text, $tweet->display_text_range[0], ( $tweet->display_text_range[1] - $tweet->display_text_range[0] ) );

			if ( ! empty( $tweet->retweeted_status ) ) {
				unset( $body );
				$body = $this->getTweetEmbed( $tweet->retweeted_status->id );
			} else {
				if ( $tweet->in_reply_to_status_id ) {
					if ( $tweet->in_reply_to_user_id !== $tweet->user->id ) {
						$body = $this->getTweetEmbed( $tweet->in_reply_to_status_id ) . "\n\n" . $body;
						$frontmatter['reply_to_id'] = $tweet->in_reply_to_status_id;
					} else {
						$frontmatter['thread_prev_id'] = $tweet->in_reply_to_status_id;
					}
				} elseif ( $tweet->is_quote_status ) {
					$body = $this->getTweetEmbed( $tweet->quoted_status_id ) . "\n\n" . $body;
				} else {
					$frontmatter['categories'][] = 'micropost';
					$frontmatter['format']       = 'status';
				}

				foreach ( $tweet->entities->urls as $tacolink ) {
					if ( $tweet->is_quote_status ) {
						$ind = strrpos( $tacolink->expanded_url, '/' );
						if ( substr( $tacolink->expanded_url, $ind + 1 ) === $tweet->quoted_status_id ) {
							$body = str_replace( $tacolink->url, '', $body );
						}
					}

					$body = str_replace(
						$tacolink->url,
						'[' . $tacolink->display_url . '](' . $tacolink->expanded_url . ')',
						$body
					);
				}

				$already_mentioned = array();
				foreach ( $tweet->entities->user_mentions as $atmention ) {
					if ( ! in_array( $atmention->screen_name, $already_mentioned, true ) ) {
						$body = str_replace(
							'@' . $atmention->screen_name,
							'[@' . $atmention->screen_name . '](https://twitter.com/' . $atmention->screen_name . ')',
							$body
						);
						$already_mentioned[] = $atmention->screen_name;
					}
				}

				if ( ! empty( $tweet->entities->hashtags ) ) {
					foreach ( $tweet->entities->hashtags as $hashtag ) {
						$body = str_replace(
							'#' . $hashtag->text,
							'[#' . $hashtag->text . '](https://twitter.com/hashtag/' . $hashtag->text . ')',
							$body
						);
						$frontmatter['tags'][] = $hashtag->text;
					}
				}
			}

			$body = $mkdwn->convertToHtml( $body );

			$new_post = array(
				'post_title'   => '',
				'post_content' => $body,
				'post_date'    => $this->parse_date( $frontmatter['date'] ),
				'post_excerpt' => '',
				'post_name'    => $frontmatter['slug'],
				'post_author'  => get_current_user_id(),
				'tags_input'   => $frontmatter['tags'],
			);

			$id = wp_insert_post( $new_post );

			if ( $id ) {
				echo "Imported tweet {$frontmatter['twitter_id']} as post {$id}\n";

				add_post_meta( $id, 'smolblog_twitter_id', $frontmatter['twitter_id'] );
				if ( $frontmatter['reply_to_id'] ) {
					add_post_meta( $id, 'smolblog_twitter_replyid', $frontmatter['reply_to_id'] );
				}
				if ( $frontmatter['thread_prev_id'] ) {
					add_post_meta( $id, 'smolblog_twitter_threadprevid', $frontmatter['thread_prev_id'] );
				}

				if ( ! empty( $tweet->extended_entities->media ) ) {
					foreach ( $tweet->extended_entities->media as $media ) {
						if ( 'photo' === $media->type ) {
							$imgid = $this->sideload_media( $media->media_url_https, $id );

							$body .= "\n\n" . '<!-- wp:image {"id":' . $imgid . '} -->
	<figure class="wp-block-image"><img src="' . wp_get_attachment_url( $imgid ) . '" alt="" class="wp-image-' . $imgid . '"/></figure>
	<!-- /wp:image -->';
						} elseif ( 'video' === $media->type || 'animated_gif' === $media->type ) {
							$video_url     = '#';
							$video_bitrate = -1;
							foreach ( $media->video_info->variants as $vidinfo ) {
								if ( 'video/mp4' === $vidinfo->content_type && $vidinfo->bitrate > $video_bitrate ) {
									$video_bitrate = $vidinfo->bitrate;
									$video_url     = $vidinfo->url;
								}
							}

							$vidid = $this->sideload_media( $video_url, $id );

							$body .= "\n\n" . '<!-- wp:video {"id":' . $vidid . '} -->
	<figure class="wp-block-video"><video controls ';

							if ( 'animated_gif' === $media->type ) {
								$body .= 'autoplay loop ';
							}

							$body .= 'preload="auto" src="' . wp_get_attachment_url( $vidid ) . '"></video></figure>
	<!-- /wp:video -->';
						}
					}

					$new_post['ID']           = $id;
					$new_post['post_content'] = $body;
					wp_insert_post( $new_post );
				}
			}
		}
	}

	private function sideload_media( $url, $post_id ) {
		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return $tmp;
		}
		$post_id    = 1;
		$desc       = 'Image from Twitter';
		$file_array = array();

		// Set variables for storage
		// fix file filename for query strings.
		preg_match( '/[^\?]+\.(jpg|jpe|jpeg|gif|png|mp4|m4v)/i', $url, $matches );

		$file_array['name']     = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink.
		if ( is_wp_error( $tmp ) ) {
			unlink( $file_array['tmp_name'] );
			$file_array['tmp_name'] = '';
		}

		// do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			unlink( $file_array['tmp_name'] );
			return $id;
		}

		return $id;
	}

	private function getfinalurl( $url ) {
		// via https://stackoverflow.com/questions/17472329/php-get-url-of-redirect-from-source-url .
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true ); // Must be set to true so that PHP follows any "Location:" header.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_exec( $ch );
		$newurl = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );

		return $newurl;
	}

	private function getTweetEmbed( $twid ) {
		$twurl = $this->getfinalurl( 'https://twitter.com/statuses/' . $twid );

		return '<!-- wp:core-embed/twitter {"url":"' . $twurl . '","type":"rich","providerNameSlug":"twitter","className":""} -->
<figure class="wp-block-embed-twitter wp-block-embed is-type-rich is-provider-twitter"><div class="wp-block-embed__wrapper">
' . $twurl . '
</div></figure>
<!-- /wp:core-embed/twitter -->';
	}

	/**
	 * Convert date in CSV file to 1999-12-31 23:52:00 format
	 *
	 * @param string $data
	 * @return string
	 */
	private function parse_date( $data ) {
		$timestamp = strtotime( $data );
		if ( false === $timestamp ) {
				return '';
		} else {
				return date( 'Y-m-d H:i:s', $timestamp );
		}
	}
}
