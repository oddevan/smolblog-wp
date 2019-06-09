<?php

require_once '../vendor/autoload.php';
require_once '../smolblog-config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

define( 'OAUTH_CALLBACK', 'https://smolblog.local/wp-content/plugins/smolblog-wp/Twitter/callback.php' );

$connection = new TwitterOAuth( SMOLBLOG_TWITTER_APPLICATION_KEY, SMOLBLOG_TWITTER_APPLICATION_SECRET );

$request_token = $connection->oauth( 'oauth/request_token', array( 'oauth_callback' => OAUTH_CALLBACK ) );

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->url( 'oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] ) );

header( 'Location: ' . $url, true, 302 );
