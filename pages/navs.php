<?php 
/**
 * Template name: Navs
 * Description:   A site navigation page
 */
get_header();
?>
<?php 
$link_cat_ids = array();
if( im('navpage_cats') ){
	foreach (im('navpage_cats') as $key => $value) {
		if( $value ) $link_cat_ids[] = $key;
	}
}
$link_cat_ids = implode(',', $link_cat_ids);
?>
<div class="pageheader">
	<div class="container">
		<div class="share">
			<?php _moloader('mo_share', false); mo_share('renren'); ?>
		</div>
		<h1><?php the_title(); ?></h1>
		<div class="note"><?php echo im('navpage_desc') ? im('navpage_desc') : '这里显示的是网址导航的一句话描述...' ?></div>
	</div>
</div>
<section class="container" id="navs">
	<nav>
		<ul></ul>
	</nav>
	<div class="items">
		<?php 
			if($link_cat_ids){
				$html = wp_list_bookmarks(array(
					'category'         => $link_cat_ids,
					'category_orderby' => 'slug',
					'category_order'   => 'ASC',
					'orderby'          => 'rating',
					'order'            => 'DESC',
					'echo'             => false,
					'show_description' => true,
					'between'          => '<br>',
					'title_li'         => __(''),
					'category_before'  => '<div class="item">',
					'category_after'   => '</div>'
				));
			}
			if( !empty($html) ){
				echo $html;
			}else{
				echo '<p style="font-size:20px;padding:15px;min-height:360px;">暫時還沒有添加導航網址喲！</p>';
			}
		?>
	</div>
</section>
<?php
get_footer();