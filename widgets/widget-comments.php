<?php
class widget_ui_comments extends WP_Widget{
	/*function widget_ui_comments() {
		$widget_ops = array( 'classname' => 'widget_ui_comments', 'description' => '显示网友最新评论（头像+名称+评论）' );
		$this->WP_Widget( 'widget_ui_comments', 'IM 最新评论', $widget_ops );
	}*/
	function __construct(){
		parent::__construct(
			'widget_ui_comments',
			'IM 最新评论',
			array(
				'classname' => 'widget_ui_comments',
				'description' => __('最新评论','im')
			)
		);
	}
	function widget($args,$instance){
		extract($args);
		$title   = apply_filters('widget_title',$instance['title']);
		$number   = isset($instance['number'])?$instance['number']:8;
		$exclude_user_id = isset($instance['exclude_user_id'])?$instance['exclude_user_id']:'1';
		$exclude_post_id = isset($instance['exclude_post_id'])?$instance['exclude_post_id']:'';
		echo $before_widget;
		echo $before_title.$title.$after_title;
		echo '<ul>';
		echo mod_newcomments($number,$exclude_post_id,$exclude_user_id);
		echo '</ul>';
		echo $after_widget;
	}
	function form($instance){
		$defaults = array(
			'title' => __('最新评论','im'),
			'number' => 8,
			'exclude_user_id' => '1',
			'exclude_post_id' => ''
		);
		$instance = wp_parse_args((array)$instance,$defaults);
?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title'));?>"><?php esc_attr_e( '标题：', 'im' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" value="<?php echo esc_attr($instance['title']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number'));?>"><?php esc_attr_e( '显示数目：', 'im' ); ?></label>
			<input type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id('number'));?>" name="<?php echo esc_attr($this->get_field_name('number'));?>" value="<?php echo esc_attr($instance['number']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('exclude_user_id'));?>"><?php esc_attr_e( '排除某用户ID(多个用“,”隔开)：', 'im' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('exclude_user_id'));?>" name="<?php echo esc_attr($this->get_field_name('exclude_user_id'));?>" value="<?php echo esc_attr($instance['exclude_user_id']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('exclude_post_id'));?>"><?php esc_attr_e( '排除某文章ID(多个用“,”隔开)：', 'im' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('exclude_post_id'));?>" name="<?php echo esc_attr($this->get_field_name('exclude_post_id'));?>" value="<?php echo esc_attr($instance['exclude_post_id']);?>" />
		</p>
<?php
	}
}
function mod_newcomments( $limit,$outpost,$outer ){
	global $wpdb;
	$outpost = form_data_valid('comment_post_ID',$outpost);
	$outer   = form_data_valid('user_id',$outer);
	$sql = "SELECT DISTINCT ID, post_title, post_password, user_id, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved,comment_author_email, comment_type,comment_author_url, SUBSTRING(comment_content,1,100) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE  ".$outpost.$outer." comment_approved = '1' AND comment_type = '' AND post_password = '' ORDER BY comment_date_gmt DESC LIMIT $limit";
	$comments = $wpdb->get_results($sql);
	$output = '';
	foreach ( $comments as $comment ) {
		$output .= '<li><a href="'.get_comment_link($comment->comment_ID).'" title="'.$comment->post_title.'上的评论">'._get_the_avatar($user_id=$comment->user_id, $user_email=$comment->comment_author_email).' <strong>'.$comment->comment_author.'</strong> '._get_time_ago( $comment->comment_date_gmt ).'说：<br>'.str_replace(' src=', ' data-original=', convert_smilies(strip_tags($comment->com_excerpt))).'</a></li>';
	}
	echo $output;
};
// validation
function form_data_valid($field,$args){
	if(!$field) return;
	$valid_id = array();
	$args = trim($args,',');
	if(strpos($args,',')){
		$id = explode(',', $args);
		foreach ($id as $key => $value) {
			echo $value;
			if(is_numeric($value)&&!strpos($value,'.')){
				$valid_id[] = (int)$value;
			}
		}
	}else{
		if(is_numeric($args)&&!strpos($args,'.')){
			$valid_id[] = (int)$args;
		}
	}
	$count = count($valid_id);
	if($count>1){
		return $field." NOT IN (".implode(',', $valid_id).") AND ";
	}else if($count==1){
		return $field."!='".$valid_id[0]."' AND ";
	}else{
		return '';
	}
}