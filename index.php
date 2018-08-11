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
//the_widget( 'widget_ui_ads' );
//dynamic_sidebar('public_header');
comments_template('', true);
_moloader('mo_footer_brand','true');
_moloader('mo_get_post_from',false);
echo mo_get_post_from();
_moloader('mo_is_minicat',false);
var_dump(mo_is_minicat());
_moloader('mo_minicat',true);
_moloader('mo_notice',true);
_moloader('mo_pagemenu',true);
// 分页
if(!is_singular()) {
echo '<h2>分页：</h2><br/>';
global $paged;
echo '当前页码：'.$paged.'<br/>';
$paged = (get_query_var('paged'))?get_query_var('paged'):1;
$the_query = new WP_Query(array(
	'ignore_sticky_posts' =>1,
	'paged' => $paged
));
if($the_query->have_posts()):
	while ($the_query->have_posts()):$the_query->the_post();
		the_title();
		echo '<br/>';
	endwhile;
	next_posts_link(__('Older Entries','im'),2);
	_moloader('mo_paging',true);
	previous_posts_link(__('Newer Entries','im'));
	wp_reset_postdata();
else:
	echo '<p>'._e('Sorry,no posts matched your criteria','im');
endif;
}
// 直达链接
echo "<h2>直达链接</h2>";
_moloader('mo_post_link');
// 相关阅读
echo '<h2>相关阅读</h2>';
_moloader('mo_posts_related');
// 分享模块
echo '<h2>分享模块</h2>';
_moloader('mo_share');
// 轮播图
echo '<h2>轮播图</h2>';
_moloader('mo_slider',false);
mo_slider('focusslide');

get_footer();