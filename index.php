<?php
get_header();
// function your_theme_menu_item_class( $classes, $item ) {
//  var_dump($item);
//   // Add slugs to menu-items
//   if ( 'category' == $item->object ) {
//     $category = get_category( $item->object_id );
//     $classes[] = 'category-' . $category->slug;
//   } elseif ( 'format' == $item->object ){
//     $format = get_term($item->object_id);
//     $classes[] = 'format-' . $format->slug;
//   }
//   return $classes;  
// }
 
// add_filter( 'nav_menu_css_class', 'your_theme_menu_item_class', 10, 2);
// function wpdocs_add_menu_parent_class( $items ) {
//     $parents = array();
 
//     // Collect menu items with parents.
//     foreach ( $items as $item ) {
//         if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
//             $parents[] = $item->menu_item_parent;
//         }
//     }
 
//     // Add class.
//     foreach ( $items as $item ) {
//         if ( in_array( $item->ID, $parents ) ) {
//             $item->classes[] = 'menu-parent-item';
//         }
//     }
//     return $items;
// }
// add_filter( 'wp_nav_menu_objects', 'wpdocs_add_menu_parent_class' );

//wp_nav_menu();
//var_dump(wp_get_nav_menu_items(4));
get_footer();
//the_widget( 'widget_ui_ads' );
//dynamic_sidebar('public_header');
var_dump($GLOBALS['comment']);
echo get_option('thread_comments_depth');