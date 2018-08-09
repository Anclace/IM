<?php 
/**
 * [mo_minicat description]
 * @return [type] [description]
 */
function mo_minicat(){
	if( !im('micro_cat_from') ) return;
	$args = array(
	    'ignore_sticky_posts' => 1,
	    'posts_per_page' => 1,
	    'cat' => im('micro_cat_from')
	);
	$fitpost = new WP_Query($args);
	if($fitpost->have_posts()) :
		while ($fitpost->have_posts()) : $fitpost->the_post();
			$category = get_the_category();
			echo '<article class="excerpt-minic excerpt-minic-index">';
			echo '<h2><a'._post_target_blank().' class="red" href="'.get_category_link($category[0]->term_id ).'">【'.(im('micro_cat_home_title') ? im('micro_cat_home_title') : '今日观点').'】</a> <a href="'.get_permalink().'" title="'.get_the_title().get_the_subtitle(false)._get_delimiter().get_bloginfo('name').'">'.get_the_title().get_the_subtitle().'</a></h2>';
			echo '<p class="note">'._get_excerpt().'</p>';
		    echo '</article>';
		endwhile;
	endif;
}