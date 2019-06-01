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
 * @package smolblog\wp
 * @since 2019-05-29
 */

defined( 'ABSPATH' ) || die( 'Please do not.' );
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/class-smolblog.php';

$plugin = new smolblog\wp\Smolblog();
$plugin->register_hooks();
