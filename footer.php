<?php  
	if( im('footer_spreadings') ){
		_moloader('mo_footer_brand', false);
	}
?>
<footer class="footer">
	<div class="container">
		<?php if( im('bottom_flinks') && im('bottom_flinks_cat') && ((im('bottom_flinks_home')&&is_home()) || (!im('bottom_flinks_home'))) ){ ?>
			<div class="flinks">
				<?php 
					wp_list_bookmarks(array(
						'category'         => im('bottom_flinks_cat'),
						'category_orderby' => 'slug',
						'category_order'   => 'ASC',
						'orderby'          => 'rating',
						'order'            => 'DESC',
						'show_description' => false,
						'between'          => '',
						'title_before'     => '<strong>',
    					'title_after'      => '</strong>',
						'category_before'  => '',
						'category_after'   => ''
					));
				?>
			</div>
		<?php } ?>
		<?php if( im('cus_foot_content') ){ ?>
			<div class="fcode">
				<?php echo im('cus_foot_content') ?>
			</div>
		<?php } ?>
		<p>&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url() ?>"><?php echo get_bloginfo('name') ?></a> &nbsp; <?php echo im('footer_extra_info') ?></p>
		<?php echo im('trackcode') ?>
	</div>
</footer>
<?php
if(im('cservices')){
	_moloader('mo_cservice');
}
?>
<?php  
	$roll = '';
	if( is_home() && im('sidebar_scroll_index') ){
		$roll = im('sidebar_scroll_index_set');
	}else if( (is_category() || is_tag() || is_search()) && im('sidebar_scroll_list') ){
		$roll = im('sidebar_scroll_list_set');
	}else if( is_single() && im('sidebar_scroll_post') ){
		$roll = im('sidebar_scroll_post_set');
	}
	if( $roll ){
		$roll = json_encode(explode(' ', $roll));
	}else{
		$roll = json_encode(array());
	}
	_moloader('mo_get_user_rp');
?>
<script>
window.jsui={
    www: '<?php echo home_url() ?>',
    uri: '<?php echo get_stylesheet_directory_uri().'/assets' ?>',
    ver: '<?php echo THEME_VERSION ?>',
	roll: <?php echo $roll ?>,
    ajaxpager: '<?php echo im("ajaxpager") ?>',
    url_rp: '<?php echo mo_get_user_rp() ?>',
    com_edit_mode: '<?php echo im("comment_editable_after_submit") ?>'
};
</script>
<?php wp_footer(); ?>
</body>
</html>