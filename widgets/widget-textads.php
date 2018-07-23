<?php
class widget_ui_textads extends WP_Widget {
	/*function widget_ui_textads() {
		$widget_ops = array( 
			'classname'   => 'widget_ui_textasb', 
			'description' => '显示一个文本特别推荐' );
		$this->WP_Widget( 'widget_ui_textads', 'IM 特别推荐', $widget_ops );
	}*/
	function __construct(){
		parent::__construct( 
			'widget_ui_textads', 
			'IM 特别推荐', 
			array( 
				'classname' => 'widget_ui_textasb'
			)
		);
	}
	function widget( $args, $instance ) {
		extract( $args );
		$title   = apply_filters('widget_name', $instance['title']);
		$tag     = isset($instance['tag']) ? $instance['tag'] : '';
		$content = isset($instance['content']) ? $instance['content'] : '';
		$link    = isset($instance['link']) ? $instance['link'] : '';
		$style   = isset($instance['style']) ? $instance['style'] : '';
		$blank   = isset($instance['blank']) ? $instance['blank'] : '';
		$lank = '';
		if( $blank ) $lank = ' target="_blank"';
		echo $before_widget;
		echo '<a class="'.$style.'" href="'.$link.'"'.$lank.'>';
		echo '<strong>'.$tag.'</strong>';
		echo '<h2>'.$title.'</h2>';
		echo '<p>'.$content.'</p>';
		echo '</a>';
		echo $after_widget;
	}
	function form($instance) {
		$defaults = array( 
			'title' => '百度', 
			'tag' => '吐血推荐', 
			'blank' => 'on', 
			'content' => '有事搜一搜，没事看一看', 
			'link' => 'https://www.baidu.com/', 
			'style' => 'style02'
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label>
				名称：
				<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" class="widefat" />
			</label>
		</p>
		<p>
			<label>
				描述：
				<textarea id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" class="widefat" rows="3"><?php echo $instance['content']; ?></textarea>
			</label>
		</p>
		<p>
			<label>
				标签：
				<input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo $instance['tag']; ?>" class="widefat" />
			</label>
		</p>
		<p>
			<label>
				链接：
				<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="url" value="<?php echo $instance['link']; ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				样式：
				<select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" class="widefat">
					<option value="style01" <?php selected('style01', $instance['style']); ?>>蓝色</option>
					<option value="style02" <?php selected('style02', $instance['style']); ?>>橘红色</option>
					<option value="style03" <?php selected('style03', $instance['style']); ?>>绿色</option>
					<option value="style04" <?php selected('style04', $instance['style']); ?>>紫色</option>
					<option value="style05" <?php selected('style05', $instance['style']); ?>>青色</option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<input class="widefat" class="checkbox" type="checkbox" <?php checked( $instance['blank'], 'on' ); ?> id="<?php echo $this->get_field_id('blank'); ?>" name="<?php echo $this->get_field_name('blank'); ?>">新打开浏览器窗口
			</label>
		</p>
<?php
	}
}