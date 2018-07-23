<?php
class widget_ui_posts extends WP_Widget{
	/*function widget_ui_posts(){
		$widget_ops = array(
			'classname'   => 'widget_ui_posts',
			'description' => __('图文展示(最新文章+热门文章+随机文章)','im')
		);
		$this->WP_Widget('widget_ui_posts','IM 聚合文章',$widget_ops);
	}*/
	function __construct(){
		parent::__construct(
			'widget_ui_posts',
			'IM 聚合文章',
			array(
				'classname'   => 'widget_ui_posts',
				'description' => __('聚合文章(图文展示)','im')
			)
		);
	}
	function widget($args,$instance){
		extract($args);
		$title   = apply_filters('widget_name',$instance['title']);
		$limit   = isset($instance['limit'])?$instance['limit']:6;
		$cat     = isset($instance['cat'])?$instance['cat']:'';
		$orderby = isset($instance['orderby'])?$instance['orderby']:'comment_count';
		$img     = isset($instance['img'])?$instance['img']:'';
		$comn    = isset($instance['comn'])?$instance['comn']:'';
		$style = '';
		if(!$img) $style = ' class="nopic"';
		echo $before_widget;
		echo $before_title.$title.$after_title;
		echo '<ul'.$style.'>';
		echo posts_list($orderby,$limit,$cat,$img,$comn);
		echo '</ul>';
		echo $after_widget;
	}
	function form($instance){
		$default = array(
			'title'   => __('热门文章','im'),
			'limit'   => 6,
			'cat'     => '',
			'orderby' => 'comment_count',
			'img'     => '',
			'comn'    => ''
		);
		$instance = wp_parse_args((array)$instance,$default);
?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title'));?>"><?php esc_attr_e( '标题：', 'im' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" value="<?php echo esc_attr($instance['title']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('orderby'));?>"><?php esc_attr_e( '排序：', 'im' ); ?></label>
			<select name="<?php echo esc_attr($this->get_field_name('orderby'));?>" id="<?php echo esc_attr($this->get_field_id('orderby'));?>" class='widefat'>
				<option value="comment_count" <?php selected('comment_count',$instance['orderby']);?>><?php esc_attr_e( '评论数', 'im' ); ?></option>
				<option value="views" <?php selected('views',$instance['orderby']);?>><?php esc_attr_e( '浏览量', 'im' ); ?></option>
				<option value="date" <?php selected('date',$instance['orderby']);?>><?php esc_attr_e( '发布时间', 'im' ); ?></option>
				<option value="rand" <?php selected('rand',$instance['orderby']);?>><?php esc_attr_e( '随机', 'im' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('cat'));?>"><?php esc_attr_e( '分类限制：', 'im' ); ?><a style="font-weight: bold;color:#f60;text-decoration: none;" href="javascript:;" title="格式：1,2 &nbsp;表限制ID为1,2分类的文章&#13;格式：-1,-2 &nbsp;表排除分类ID为1,2的文章&#13;也可直接写1或者-1；注意逗号须是英文的">？</a></label>
			<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('cat'));?>" name="<?php echo esc_attr($this->get_field_name('cat'));?>" value="<?php echo esc_attr($instance['cat']);?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('limit'));?>"><?php esc_attr_e( '显示数目：', 'im' ); ?></label>
			<input type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id('limit'));?>" name="<?php echo esc_attr($this->get_field_name('limit'));?>" value="<?php echo esc_attr($instance['limit']);?>" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('img'));?>" name="<?php echo esc_attr($this->get_field_name('img'));?>" <?php checked($instance['img'],'on'); ?>" />
			<label for="<?php echo esc_attr($this->get_field_id('img'));?>"><?php esc_attr_e( '显示图片', 'im' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="widefat" id="<?php echo esc_attr($this->get_field_id('comn'));?>" name="<?php echo esc_attr($this->get_field_name('comn'));?>" <?php checked($instance['comn'],'on'); ?>" />
			<label for="<?php echo esc_attr($this->get_field_id('comn'));?>"><?php esc_attr_e( '显示评论数', 'im' ); ?></label>
		</p>
<?php
	}
}
function posts_list($orderby,$limit,$cat,$img,$comn){
	$args = array(
		'cat'                 => $cat,
		'order'               => 'DESC',
		'posts_per_page'      => $limit,
		'ignore_sticky_posts' => 1,
	);
	if($orderby !== 'views'){
		$args['orderby'] = $orderby;
	}else{
		$args['orderby'] = 'meta_value_num';
		$args['meta_query'] = array(
			array(
				'key'   => 'views',
				'order' => 'DESC'
			)
		);
	}
	$the_query = new WP_Query($args);
	if($the_query->have_posts()){
		while ($the_query->have_posts()){
			$the_query->the_post();
?>
			<li>
				<a href="<?php echo the_permalink();?>" <?php echo  _post_target_blank() ?>>
					<?php 
						if($img){
							echo '<span class="thumbnail">';
							echo _get_post_thumbnail();
							echo '</span>';
						}else{
							$img = '';
						}
					?>
						<span class="text"><?php the_title();?><?php echo get_the_subtitle() ?></span>
						<span class="muted"><?php the_time('Y-m-d');?></span>
					<?php 
						if($comn){
					?>
						<span class="muted"><?php echo '评论(', comments_number('0', '1', '%'), ')'; ?></span>
					<?php
						}
					?>
				</a>
			</li>
<?php
		}
		wp_reset_postdata();
	}
}