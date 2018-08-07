<?php 
/**
 * [mo_get_user_page description]
 * @return [type] [description]
 */
function mo_get_user_page(){
	$pid = im('user_page');
	if( !$pid ){
		return false;
	}
	if( get_permalink($pid) ){
		return get_permalink($pid);
	}
	return false;
}