<?php
class widget_ui_sticky extends WP_Widget {
	/*function widget_ui_sticky() {
		$widget_ops = array( 
			'classname' => 'widget_ui_posts', 
			'description' => '置顶推荐' 
		);
		$this->WP_Widget( 'widget_ui_sticky', 'IM 置顶推荐', $widget_ops );
	}*/
	function __construct(){
		parent::__construct( 
			'widget_ui_sticky', 
			'IM 置顶推荐', 
			array( 
				'classname'   => 'widget_ui_posts',
				'description' => __('置顶文章','im')
			) 
		);
	}
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_name', $instance['title']);
		$limit = isset($instance['limit']) ? $instance['limit'] : 6;
		$img   = isset($instance['img']) ? $instance['img'] : '';
		$comn   = isset($instance['comn']) ? $instance['comn'] : '';
		$style='';
		if( !$img ) $style = ' class="nopic"';
		echo $before_widget;
		echo $before_title.$title.$after_title; 
		echo '<ul'.$style.'>';
		echo dd_sticky_posts_list( $limit,$img,$comn );
		echo '</ul>';
		echo $after_widget;
	}
	function form( $instance ) {
		$defaults = array( 
			'title' => __('置顶推荐','im'), 
			'limit' => 6,  
			'img' => '',
			'comn' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label>
				标题：
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			</label>
		</p>
		<p>
			<label>
				显示数目：
				<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $instance['limit']; ?>" />
			</label>
		</p>
		<p>
			<label>
				<input class="checkbox" type="checkbox" <?php checked( $instance['img'], 'on' ); ?> id="<?php echo $this->get_field_id('img'); ?>" name="<?php echo $this->get_field_name('img'); ?>">显示图片
			</label>
		</p>
		<p>
			<label>
				<input class="checkbox" type="checkbox" <?php checked( $instance['comn'], 'on' ); ?> id="<?php echo $this->get_field_id('comn'); ?>" name="<?php echo $this->get_field_name('comn'); ?>">显示评论数
			</label>
		</p>
		
	<?php
	}
}


function dd_sticky_posts_list($limit,$img,$comn) {
	$sticky = get_option('sticky_posts'); 
	rsort( $sticky );
	$args = array(
		'post__in'       => $sticky,
		'posts_per_page' => $limit
	);
	$the_query = new WP_Query($args);
	if($the_query->have_posts()):
		while ($the_query->have_posts()):
			$the_query->the_post();
?>
			<li>
				<a href="<?php echo the_permalink();?>" <?php echo  _post_target_blank() ?>>
					<?php 
						if( $img ){
							echo '<span class="thumbnail">';
							echo _get_post_thumbnail(); 
							echo '</span>'; 
						}else{
							$img = '';
						} 
					?>
						<span class="text"><?php the_title(); ?><?php echo get_the_subtitle() ?></span>
						<span class="muted"><?php the_time('Y-m-d');?></span>
					<?php 
						if( $comn ){ 
					?>
						<span class="muted"><?php echo '评论(', comments_number('0', '1', '%'), ')'; ?></span>
					<?php 
						} 
					?>
				</a>
			</li>
<?php
    	endwhile; 
    	wp_reset_postdata();
	endif;
}