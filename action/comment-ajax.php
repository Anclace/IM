<?php
/**
 * Base on the original wordpress file:wp-comments-post.php
 * 新版本修复老版本评论被篡改的bug（当前文件下的comment-old0916.php,重命名前为comment.php）
 * bug1:在浏览器调试功能下可以启用js里面的此段：<input type="text" name="edit_id" id="edit_id" value="' + edit + '" style="display:none;" />（用来提交要修改评论的ID）,包括编辑其他提交到后台的字段,从而可以更改其他评论【具体只要看comment.js及comment-oldz.php文件就知如何操作】
 * bug2:还有其他错误没有处理（把wp根目录下wp-comments-post.php与之对比就明白了）
 */
if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}

	header('Allow: POST');
	header("$protocol 405 Method Not Allowed");
	header('Content-Type: text/plain');
	exit;
}

if( !$_POST ){
    err('您没有提交数据哦！');
}

require( dirname(__FILE__).'/../../../../wp-load.php' );

nocache_headers();

$edit_id = ( isset($_POST['edit_id']) ) ? $_POST['edit_id'] : null;
if($edit_id&&!im('comment_editable_after_submit'))
			err('管理员已禁止再次编辑评论');

$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
if ( is_wp_error( $comment ) ) {
	$data = intval( $comment->get_error_data() );
	if ( ! empty( $data ) ) {
		err($comment->get_error_message());
	} else {
		exit;
	}
}else{
// 为了安全，在页面提交的所有评论都需要审核
	$com_id = get_comment_ID();
	wp_update_comment(
		array(
			'comment_ID' => $com_id,
			'comment_approved' => '0'
		)
	);
// 前台可编辑评论功能【前面的代码用来验证提交数据的正确性以及过滤如邮箱等字段是否合法】，此处采用的是删除刚插入的评论而修改原有的，也可以直接保留刚插入的而删除原有的
	if( $edit_id ){
		// 将上面插入的评论至回收站（也可直接删除，这里留着是可以查看具体信息）
		wp_update_comment(
			array(
				'comment_ID' => $com_id,
				'comment_content' => '有人编辑：'.get_comment_text(),
				'comment_approved' => 'trash'
			)
		);
		// 覆盖刚插入的评论
		$comment = uptcoms($edit_id);
	}
// 返回数据（成功）
	echo '<li '; comment_class(); echo ' id="comment-'.get_comment_ID().'">';
	echo '<span class="comt-f">#</span>';
	echo '<div class="comt-avatar">';
		echo _get_the_avatar($user_id=$comment->user_id, $user_email=$comment->comment_author_email, $src=true);
	echo '</div>';
	echo '<div class="comt-main" id="div-comment-'.get_comment_ID().'">';
		echo '<p>'.str_replace(' src=', ' data-src=', convert_smilies(get_comment_text())).'</p>';
		echo '<div class="comt-meta"><cite class="comt-author">'.get_comment_author_link().'</cite>';
	    echo '<time>'._get_time_ago($comment->comment_date_gmt).'</time>'; 
		echo '</div>';
	    if ($comment->comment_approved == '0'){
	      echo '<span class="comt-approved">待审核</span>';
	    }
	echo '</div>';
}

// Save user name, email, and website in the browser for the next time user comment
$user = wp_get_current_user();
/*# Decide whether to cache based on user selection
$cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );
do_action( 'set_comment_cookies', $comment, $user, $cookies_consent );*/
do_action( 'set_comment_cookies', $comment, $user );

// update comments
function uptcoms($edit_id){
	$comment = get_comment($edit_id);
	if(!$comment)
		err('没有找到您要编辑的评论');

	$db_cauther      = $comment->comment_author;
	$db_cauther_mail = $comment->comment_author_email;
	$db_cauther_url  = $comment->comment_author_url;
	$db_cstatus      = $comment->comment_approved;
	$db_cparent      = $comment->comment_parent;
	$db_cpost		 = $comment->comment_post_ID;

	$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
	$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
	$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
	$comment_content      = ( isset($_POST['comment']) ) ? trim(strip_tags($_POST['comment'])) : null;
	$comment_parent       = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
	$comment_post_ID      = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;

	$user = wp_get_current_user();
	if ( $user->ID ) {
		if ( empty( $user->display_name ) )
			$user->display_name=$user->user_login;
		$comment_author       = esc_sql($user->display_name);
		$comment_author_email = esc_sql($user->user_email);
		$comment_author_url   = esc_sql($user->user_url);
	}

	$uptoption = ($db_cstatus != '0')||($comment_author != $db_cauther)||($comment_author_email != $db_cauther_mail)||($comment_author_url != $db_cauther_url)||($comment_parent != $db_cparent)||($comment_post_ID != $db_cpost);

	if($uptoption){
		err('无权限修改此项！');
	}
	/*详细调试
	if($db_cstatus != '0'){
		err('该评论已审核过，不能修改');
	}elseif($comment_author != $db_cauther){
		err('用户名不匹配');
	}elseif($comment_author_email != $db_cauther_mail){
		err('邮箱不匹配');
	}elseif($comment_author_url != $db_cauther_url){
		err('您的网址不匹配');
	}elseif($comment_parent != $db_cparent){
		err('父评论不匹配');
	}elseif($comment_post_ID != $db_cpost){
		err('修改的评论不属于当前文章哦');
	}else{

	}*/

	$commentdata = compact('comment_content');
	$commentdata['comment_ID'] = $edit_id;
	$commentdata['comment_approved'] = '0';

	if(wp_update_comment( $commentdata )){
		return get_comment($edit_id);
	}else{
		err('请稍后再试');
	}
}
// Error message
function err($ErrMsg) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}
	header("$protocol 405 Method Not Allowed");
    echo $ErrMsg;
    exit;
}

exit;