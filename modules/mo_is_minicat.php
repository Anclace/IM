<?php 
/**
 * [mo_is_minicat description]
 * @return bool [description]
 */
function mo_is_minicat(){
	if( !im('micro_cat') ){
		return false;
	}
	if( !im('micro_cat_from') ){
		return false;
	}
	if( is_category() ){
		global $wp_query;
		$cat_ID = get_query_var('cat');
	}else if( !is_page() ){
		$category = get_the_category();
		$cat_ID = $category[0]->cat_ID;
	}
	if( $cat_ID == im('micro_cat_from') ){
		return true;
	}
	return false;
}