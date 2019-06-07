<?php
/**
 * Plugin Name: Smolblog for WordPress
 * Plugin URI:  https://dev.smolblog.com/smolblog-wp
 * Description: A monolithic plugin to turn a WordPress multisite install into a Smolblog install.
 * Version:     0.1.0
 * Author:      Smolblog
 * Author URI:  https://dev.smolblog.com/
 * Text Domain: smolblog
 * Domain Path: /languages
 * License:     GPL2
 *
 * @package Smolblog\WP
 * @since 2019-05-29
 */

defined( 'ABSPATH' ) || die( 'Please do not.' );

require_once __DIR__ . '/vendor/autoload.php';

$plugin = new Smolblog\WP\Smolblog();
$plugin->register_hooks();
