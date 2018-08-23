<?php 
	get_header(); 
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>
<section class="container">
	<div class="content-wrap">
		<div class="content">
			<?php 
				if( $paged==1 && im('carousel_figure') ){ 
					_moloader('mo_slider', false);
					mo_slider('focusslide');
				} 
			?>
			<?php 
				$pagedtext = ''; 
				if( $paged > 1 ){
					$pagedtext = ' <small>第'.$paged.'页</small>';
				}
			?>
			<?php  
				if( im('micro_cat_home') ){
					_moloader('mo_minicat');
				}
			?>
			<?php _the_ads($name='ads_index_01', $class='asb-index asb-index-01') ?>
			<div class="title">
				<h3>
					<?php echo im('list_section_title') ? im('list_section_title') : '最新发布' ?>
					<?php echo $pagedtext ?>
				</h3>
				<?php 
					if( im('list_section_title_r') ){
						echo '<div class="more">'.im('list_section_title_r').'</div>';
					} 
				?>
			</div>
			<?php get_template_part( 'excerpt' ); ?>
			<?php _the_ads($name='ads_index_02', $class='asb-index asb-index-02') ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
</section>
<?php get_footer();