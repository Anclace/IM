<?php  
/**
 * Like function
 * @return [type] [description]
 */
function mo_like(){
    $liked = isset($_COOKIE['ulike_'.get_the_ID()])?' actived':'';
    echo '<a href="javascript:;" etap="like" data-id="'.get_the_ID().'" class="post-like'.(is_single()?' action action-like':'') .$liked.'" title="点赞"><i class="fa fa-thumbs-o-up"></i>'._get_post_like_number().'</a>';
}