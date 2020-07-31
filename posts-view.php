<?php

/**
 * Plugin Name:       Posts View
 * Plugin URI:        https://github.com/tanmayjay/wordpress/tree/master/3-Plugin-API/posts-view
 * Description:       A plugin to show the view count of each post.
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Tanmay Kirtania
 * Author URI:        https://linkedin.com/in/tanmay-kirtania
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       posts-view
 * 
 * 
 * Copyright (c) 2020 Tanmay Kirtania (jktanmay@gmail.com). All rights reserved.
 * 
 * This program is a free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see the License URI.
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class Posts_View {
    
    /**
     * Static class object
     *
     * @var object
     */
    private static $instance;

    const version = '1.0.3';

    /**
     * Private class constructor
     */
    private function __construct() {
        $this->define_constants();
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Private class cloner
     */
    private function __clone() {}

    /**
     * Initializes a singleton instance
     * 
     * @return \Posts_View
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Defines the required constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'JPV_VERSION', self::version );
        define( 'JPV_FILE', __FILE__ );
        define( 'JPV_PATH', __DIR__ );
        define( 'JPV_URL', plugins_url( '', JPV_FILE ) );
        define( 'JPV_ASSETS', JPV_URL . '/assets' );
    }

    /**
     * Updates info on plugin activation
     *
     * @return void
     */
    public function activate() {
        $activator = new Jay\JPV\Activator();
        $activator->run();
    }

    /**
     * Initializes the plugin
     *
     * @return void
     */
    public function init_plugin() {
        add_filter( 'the_content', [ $this, 'parse_post_view_count' ] );
        add_action( 'wp_head', 'jpv_set_post_view_count' );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_filter( 'emphasize_text', 'jpv_emphasize_text', 10, 2 );

        new Jay\JPV\Frontend();
    }

    /**
     * Parses the post view counts and wraps it with style
     *
     * @return string
     */
    public function parse_post_view_count( $content ) {
        global $post;
        $view_count = jpv_get_post_view_count( $post );

        $view_count_em = apply_filters( 'emphasize_text', $view_count, 'Views' );
        ob_start();

        ?>
        <span class="jpv-span"><?php echo $view_count_em; ?></span>
        <?php

        $post_content = ob_get_clean();
        return $content . $post_content;
    }

    /**
     * Includes the stylesheet
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'jpv-styles', JPV_ASSETS . '/css/style.css', '', '1.0.1' );
    }
}

/**
 * Initializes the main plugin
 *
 * @return \Posts_View
 */
function posts_view() {
    return Posts_View::get_instance();
}

//kick off the plugin
posts_view();