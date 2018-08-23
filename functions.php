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






#######################################################
# Home Page
#######################################################
// FOR HOME PAGE ONLY
function archives_for_homepage($query){
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $ignore_articles_cat = '';
    $ignore_posts = array();
    if( im('ignore_articles_cat') ){
        $pool = array();
        foreach (im('ignore_articles_cat') as $key => $value) {
            if( $value ) $pool[] = $key;
        }
        if(!empty($pool)){
            $ignore_articles_cat = '-'.implode(',-',$pool);
        }
    }
    if( im('ignore_posts') ){
        $pool = im('ignore_posts');
        $ignore_posts = explode("\n", $pool);
    }
    if ( $query->is_home() && $query->is_main_query()) {    
        $query->set('ignore_sticky_posts',1);
        $query->set('paged',$paged);
        $query->set('cat',$ignore_articles_cat);
        $query->set('post__not_in',$ignore_posts);
    }
}
add_action('pre_get_posts','archives_for_homepage');