<?php
/**
 * Botiga child functions
 *
 */


/**
 * Enqueues the parent stylesheet. Do not remove this function.
 *
 */
add_action( 'wp_enqueue_scripts', 'botiga_child_enqueue' );
function botiga_child_enqueue() {
    
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

define( 'THEME_VERSION', '1.0.0' );

// Initialize child theme
require_once 'classes/_autoloader.inc.php';
WearNSlay::createTheme( THEME_VERSION );