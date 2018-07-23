<?php
class widget_ui_readers extends WP_Widget{
	/*function widget_ui_readers(){
		$widget_ops = array(
			'classname'  => 'widget_ui_readers',
			'description'=> __('显示近期评论最多的网友信息','im')
		);
		$this->WP_Widget('widget_ui_readers','IM 活跃读者',$widget_ops);
	}*/
	function __construct(){
		parent::__construct(
			'widget_ui_readers',
			'IM 活跃读者',
			array(
				'classname'  => 'widget_ui_readers',
				'description'=> __('显示近期评论最多的网友信息','im')
			)
		);
	}
	function widget($args,$instance){
		extract($args);
		$title   = apply_filters('widget_name',$instance['title']);
		$timer   = isset($instance['timer'])?$instance['timer']:500;
		$limit   = isset($instance['limit'])?$instance['limit']:32;
		$addlink   = isset($instance['addlink'])?$instance['addlink']:'';
		echo $before_widget;
		echo $before_title.$title.$after_title;
		echo '<ul>';
		echo loyal_readers($timer,$limit,$addlink);
		echo '</ul>';
		echo $after_widget;
	}
	function form($instance){
		$defaults = array(
			'title'  => __('活跃读者','im'),
			'limit'  => 32,
			'timer'  => 500,
			'addlink'=> ''
		);
		$instance = wp_parse_args((array)$instance,$defaults);
?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('标题：','im');?> </label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" value="<?php echo esc_attr($instance['title']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php esc_attr_e('显示数目：','im');?> </label>
			<input type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit'));?>" value="<?php echo esc_attr($instance['limit']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('timer')); ?>"><?php esc_attr_e('几天内：','im');?> </label>
			<input type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id('timer')); ?>" name="<?php echo esc_attr($this->get_field_name('timer'));?>" value="<?php echo esc_attr($instance['timer']);?>" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('addlink')); ?>" name="<?php echo esc_attr($this->get_field_name('addlink'));?>" <?php checked($instance['addlink'],'on');?> />
			<label for="<?php echo esc_attr($this->get_field_id('addlink')); ?>"><?php esc_attr_e('加链接','im');?> </label>
		</p>
<?php
	}
}
/* 
 * 读者墙
 * loyal_readers( $timer='7', $limit='32' );
 * $timer 几天内
 * $limit 显示条数
*/
function loyal_readers($timer,$limit,$addlink){
	global $wpdb;
	$comments = $wpdb->get_results("SELECT count(comment_author) AS cnt, user_id, comment_author, comment_author_url, comment_author_email FROM $wpdb->comments WHERE comment_date > date_sub( now(), interval $timer day ) AND user_id!='1' AND comment_approved='1' AND comment_type='' GROUP BY comment_author ORDER BY cnt DESC LIMIT $limit");
	$html = '';
	foreach ($comments as $comment) {
		$c_url = $comment->comment_author_url;
		if ($c_url == '') $c_url = 'javascript:;';

		if($addlink == 'on'){
			$c_urllink = ' href="'. $c_url . '"';
		}else{
			$c_urllink = '';
		}
		$html .= '<li><a title="['.$comment->comment_author.'] 近期点评'. $comment->cnt .'次" target="_blank"'.$c_urllink.'>'._get_the_avatar($user_id=$comment->user_id, $user_email=$comment->comment_author_email).'</a></li>';
	}
	echo $html;
}