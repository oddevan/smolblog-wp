<?php
/**
 * Set the required plugins and load the composer autoloader
 *
 * @since 0.1.0
 * @package Smolblog\WP
 */

require_once WPMU_PLUGIN_DIR . '/wds-required-plugins/wds-required-plugins.php';

/**
 * Set required plugins.
 *
 * @since 0.1.0
 * @author me@eph.me
 *
 * @param array $required Current list of required plugins.
 * @return array Required plugins including ours.
 */
function smolblog_required_plugins( $required ) {

	$required = array_merge( $required, array(
		'advanced-custom-fields/acf.php',
		'smolblog-wp/smolblog-wp.php',
	) );

	return $required;
}

/**
 * Network-activate the required plugins
 *
 * @since 0.1.0
 */
add_filter( 'wds_network_required_plugins', 'smolblog_required_plugins' );

/**
 * Activate the Composer autoloader. This is required for Smolblog plugins
 * to function.
 *
 * @since 0.1.0
 */
require_once __DIR__ . '/../vendor/autoload.php';
