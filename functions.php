<?php
//Require theme functions 
require get_stylesheet_directory().'/settings/functions-theme.php';
//Customize your functions
/* 
 * delete google fonts
 * ====================================================
*/
// Remove Open Sans that WP adds from frontend
if (!function_exists('remove_wp_open_sans')) :
    function remove_wp_open_sans() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
    }
    add_action('wp_enqueue_scripts', 'remove_wp_open_sans');
 
    // Uncomment below to remove from admin
    // add_action('admin_enqueue_scripts', 'remove_wp_open_sans');
endif;

function remove_open_sans() {    
    wp_deregister_style( 'open-sans' );    
    wp_register_style( 'open-sans', false );    
    wp_enqueue_style('open-sans','');    
}    
add_action( 'init', 'remove_open_sans' );
//
//
//
//
//
//
// var_dump(get_post_meta(7,'',true));

// delete wp_head code
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'wp_generator');
// WordPress Emoji Delete
remove_action( 'admin_print_scripts' ,  'print_emoji_detection_script');
remove_action( 'admin_print_styles'  ,  'print_emoji_styles');
remove_action( 'wp_head'             ,  'print_emoji_detection_script', 7);
remove_action( 'wp_print_styles'     ,  'print_emoji_styles');
remove_filter( 'the_content_feed'    ,  'wp_staticize_emoji');
remove_filter( 'comment_text_rss'    ,  'wp_staticize_emoji');
remove_filter( 'wp_mail'             ,  'wp_staticize_emoji_for_email');