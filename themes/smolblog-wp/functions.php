<?php
/**
 * Simple functions.php file to enable the child theme
 *
 * @package smolblog/wp
 * @since 0.1.0
 */

/**
 * Enqueue the parent style
 *
 * @author Evan Hildreth <me@eph.me>
 * @since 0.1.0
 */
function my_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', [], '0.1.0' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
