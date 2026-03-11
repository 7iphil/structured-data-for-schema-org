<?php
/**
 * Plugin Name: Structured Data for Schema.org
 * Plugin URI: https://iphil.top/portfolio/structured-data-for-schema-org/
 * Description: Generate Schema.org structured data via shortcode. Supports HowTo, FAQPage, ItemList, CreativeWork.
 * Version: 1.0.5
 * Author: philstudio - Phil
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: structured-data-for-schema-org
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SDFSO_PATH', plugin_dir_path( __FILE__ ) );

spl_autoload_register( 'sdfso_autoloader' );

function sdfso_autoloader( $class_name ) {
    $file = SDFSO_PATH . 'includes/class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
    if ( file_exists( $file ) ) {
        require_once $file;
    }
}

add_action( 'init', 'sdfso_init' );

function sdfso_init() {
    $shortcode = new SDFSO_Shortcode();
    $shortcode->register();
}

add_action( 'wp_footer', 'sdfso_output', 1 );

function sdfso_output() {
    
    $queue = SDFSO_Schema::get_queue();
    
    if ( ! empty( $queue ) ) {
        foreach ( $queue as $schema ) {
            echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
        }
    }
}