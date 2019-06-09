<?php

require_once '../vendor/autoload.php';
require_once '../smolblog-config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

define( 'OAUTH_CALLBACK', 'https://smolblog.local/wp-content/plugins/smolblog-wp/Twitter/callback.php' );

$request_token                       = [];
$request_token['oauth_token']        = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

if ( isset( $_REQUEST['oauth_token'] ) && $request_token['oauth_token'] !== $_REQUEST['oauth_token'] ) {
	die( 'OAuth tokens did not match!' );
}

$connection = new TwitterOAuth( SMOLBLOG_TWITTER_APPLICATION_KEY,
																SMOLBLOG_TWITTER_APPLICATION_SECRET,
																$request_token['oauth_token'],
																$request_token['oauth_token_secret'] );

$access_token = $connection->oauth( 'oauth/access_token', [ 'oauth_verifier' => $_REQUEST['oauth_verifier'] ] );

echo '<h2>Access Token result:</h2><pre>' . print_r( $access_token, true ) . '</pre>';
