<?php
class widget_ui_ads extends WP_Widget{
	/*function widget_ui_ads() {
		$widget_ops = array( 'classname' => 'widget_ui_ads', 'description' => '添加站点广告(包括富媒体)' );
		$this->WP_Widget( 'widget_ui_ads', 'IM 广告', $widget_ops );
	}*/
	function __construct(){
		parent::__construct(
			'widge_ui_ads',
			'IM 广告',
			array(
				'classname'   => 'widget_ui_ads',
				'description' => __('添加站点广告','im')
			)
		);
	}
	function widget($args,$instance){
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$code = isset($instance['code'])?$instance['code']:'';
		echo $before_widget;
		echo '<div class="item">'.$code.'</div>';
		echo $after_widget;
	}
	function form($instance){
		$default = array(
			'title' => __('广告','im'),
			'code'  => '<a href="'.get_bloginfo('url').'" target="_blank"><img src="'.get_stylesheet_directory_uri().'/assets/images/xiu.jpg"></a>'
		);
		$instance = wp_parse_args((array)$instance,$default);
?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title'));?>"><?php esc_attr_e( '广告名称：', 'im' ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" type="text" value="<?php echo esc_attr($instance['title']);?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('code'));?>"><?php esc_attr_e( '广告代码：', 'im' ); ?></label>
			<textarea name="<?php echo esc_attr($this->get_field_name('code'));?>" id="<?php echo esc_attr($this->get_field_id('code'));?>" class="widefat" rows="12" style="font-family: Courier New;"><?php echo esc_attr($instance['code']);?></textarea>
		</p>
<?php
	}
}