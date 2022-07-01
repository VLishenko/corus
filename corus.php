<?php
/**
 * @package Corus
 */
/*
Plugin Name: Corus Plugin
Description: Test task for Corus
Version: 1.0.0
Author: Vitalii Lishenko
Author URI: https://www.linkedin.com/in/vitalii-lishenko/
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

if( ! defined( 'ABSPATH' ) ) {
    die;
}

class CorusPlugin
{   
    /**
     * This is constructor
     *
     * @return void
     */
    function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__ );
		$this->plugin_url = plugin_dir_url( __FILE__ ); 

		add_action( 'init', array( $this, 'custom_post_type' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

        flush_rewrite_rules();
	}

    /**
     * This is activate
     *
     * @return void
     */
    function activate() {
        $this->custom_post_type();
    }

    /**
     * This is deactivate
     *
     * @return void
     */
    function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Public Styles
     *
     * @return void
     */
    function enqueue() {
        wp_register_style('corus-book', plugins_url('/public/css/corus-book.css', __FILE__), '', '1.0.0');
    }

    /**
     * Admin Styles, Scripts
     *
     * @return void
     */
    function admin_enqueue() {
        wp_enqueue_style( 'wp-color-picker' ); 
        wp_enqueue_style( 'admin-styles', plugins_url( '/admin/css/styles.css', __FILE__ ), '', '1.0.0');
        wp_enqueue_script( 'admin-scripts', plugins_url( '/admin/js/script.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), false, true );
    }

    /**
     * Register Post Type
     *
     * @return void
     */
    function custom_post_type() {
        $labels = [
            "name" => __( "Books", "Corus" ),
            "singular_name" => __( "Book", "Corus" )
        ];

        $args = [
            "label" => __( "Corus", "Corus" ),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "rest_namespace" => "wp/v2",
            "has_archive" => false,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => false,
            "rewrite" => [ "slug" => "corus_books", "with_front" => true ],
            "query_var" => true,
            "menu_position" => 21,
            "menu_icon" => "dashicons-book-alt",
            "supports" => [ "title" ],
            "show_in_graphql" => false,
        ];

        register_post_type( "corus_books", $args );
    }

    /**
     * Add Post Type Meta Fields
     *
     * @return void
     */
    public function add_post_meta_boxes() {
        add_meta_box(
            "post_metadata_advertising_category",
            "Book Settings",
            array( $this, 'post_meta_box_output' ),
            "corus_books",
            "normal",
            "low"
        );
	}

    /**
     * Admin Layout for Post Type Meta Fields
     *
     * @return void
     */
    public function post_meta_box_output(){
        require_once( "$this->plugin_path/templates/admin-cpt-book.php" );
    }

    /**
     * Save Post Type Meta Fields
     *
     * @return void
     */
    public function save_post_meta_boxes(){
        global $post;

        $is_valid_nonce = (isset($_POST['_corus-admin-nonce']) && wp_verify_nonce($_POST['_corus-admin-nonce'], '_corus-admin-nonce')) ? true : false;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if( !$is_valid_nonce || !isset($post->ID) )  {
            return;
        }

        if ( get_post_status( $post->ID ) === 'auto-draft' ) {
            return;
        }
        
        update_post_meta( $post->ID, "_book_author_name", sanitize_text_field( $_POST[ "_book_author_name" ] ) );
        update_post_meta( $post->ID, "_book_color", sanitize_text_field( $_POST[ "_book_color" ] ) );
    }

    /**
     * Register Short Code
     *
     * @return string
     */
    public function shortcode_book_post( $atts ){
        wp_enqueue_style('corus-book');
        
        $attributes = shortcode_atts( array(
            'id' => '',
        ), $atts );
         
        ob_start();

        require_once( $this->plugin_path. "templates/shortcode-book-post.php" );

        return ob_get_clean();
    }

/// end class
}

/**
 * Register Actions
 *
 * @return void
 */
if( class_exists('CorusPlugin') ) {
    $corusPlugin = new CorusPlugin();
    
    add_action( 'wp_enqueue_scripts', array( $corusPlugin, 'enqueue' ) );
    add_action( 'admin_enqueue_scripts', array( $corusPlugin, 'admin_enqueue' ) );
    add_action( 'admin_init', array( $corusPlugin, 'add_post_meta_boxes' ) );
    add_action( 'save_post', array( $corusPlugin, 'save_post_meta_boxes') );
    add_shortcode( 'book', array( $corusPlugin, 'shortcode_book_post') );
}

// activation
register_activation_hook( __FILE__, array( $corusPlugin, 'activate' ) );

// deactivation
register_activation_hook( __FILE__, array( $corusPlugin, 'deactivate' ) );

// unistall