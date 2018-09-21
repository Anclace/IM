<?php  
/**
 * [mo_post_link description]
 * @return [type] [description]
 */
function mo_post_link(){
    global $post;
    $post_ID = $post->ID;
    $linktxt = get_post_meta($post_ID, 'direct_link_txt', true);
    $link = get_post_meta($post_ID, 'direct_link_href', true);
    if( $link&&$linktxt ){
    	if(!is_single()){
    		echo '<div class="post-linkto"><a class="btn btn-primary btn-xs" href="'. $link .'"'. (im('direct_link_open_type')?' target="_blank"':'') . (im('direct_link_nofollow')?' rel="external nofollow"':'') .'>'.$linktxt.'</a></div>';
    	}else{
    		echo '<a class="action action-link" href="'. $link .'"'. (im('direct_link_open_type')?' target="_blank"':'') . (im('direct_link_nofollow')?' rel="external nofollow"':'') .'><i class="fa fa-external-link"></i>'.$linktxt.'</a>';
    	}
    }
}