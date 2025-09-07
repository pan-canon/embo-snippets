<?php

/**
 * Plugin Name: Embo Simple Snippets
 * Plugin URI:  https://github.com/pan-canon/embo-snippets/tree/main
 * Description: Loads snippets.php which defines a class with register() for your hooks.
 * Author: Pan Canon
 * Author URI: https://embo-studio.ua/
 * Version: 0.1.1
 * License: GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EMBO_SNIPPETS_DIR', plugin_dir_path( __FILE__ ) );

// Kill-switch via wp-config.php: define('EMBO_SNIPPETS_OFF', true);
if ( defined( 'EMBO_SNIPPETS_OFF' ) && EMBO_SNIPPETS_OFF ) return;

add_action( 'plugins_loaded', function () {
    $file = EMBO_SNIPPETS_DIR . 'snippets.php';
    if ( ! is_readable( $file ) ) return;

    require_once $file;

    // Expect a class Embo\Snippets\Snippets with method register()
    if ( class_exists( '\Embo\Snippets\Snippets' ) ) {
        $sn = new \Embo\Snippets\Snippets();
        if ( method_exists( $sn, 'register' ) ) {
            $sn->register();
        }
    }
}, 20 );