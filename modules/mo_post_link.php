<?php  
/**
 * [mo_post_link description]
 * @return [type] [description]
 */
function mo_post_link(){
    global $post;
    $post_ID = $post->ID;
    $link = get_post_meta($post_ID, 'direct_link', true);
    if( $link ){
    	echo '<div class="post-linkto"><a class="btn btn-primary'. (!is_single()?' btn-xs':' btn-lg')  .'" href="'. $link .'"'. (im('direct_link_open_type')?' target="_blank"':'') . (im('direct_link_nofollow')?' rel="external nofollow"':'') .'>'.im('direct_link_title') .'</a></div>';
    }
}