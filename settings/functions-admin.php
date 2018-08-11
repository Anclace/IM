<?php
/**
 * Loads the Options Panle
 */
define('OPTIONS_FRAMEWORK_DIRECTORY', get_stylesheet_directory_uri().'/settings/');
require_once dirname(__FILE__).'/options-framework.php';
/**
 * Loads options.php from child or parent theme
 * https://developer.wordpress.org/reference/functions/load_template/
 */ 
if ( $optionsfile = locate_template( 'options.php' ) ) {
    load_template( $optionsfile );
} else {
    load_template(get_stylesheet_directory(). '/options.php' );
}
/**
 * Add link manager
 */
add_filter( 'pre_option_link_manager_enabled', '__return_true' );
/**
 * Editor Customization
 * https://codex.wordpress.org/Quicktags_API
 * https://codex.wordpress.org/TinyMCE
 * Recommend Editor Plugin:TinyMCE Advanced
 */
add_editor_style(get_locale_stylesheet_uri().'/settings/css/editor-style.css');
function add_mce_buttons($buttons) {
	//var_dump($buttons);
	$addbuttons = array('underline','strikethrough','superscript','subscript');
	$addbuttons2 = array('unlink','hr','wp_page');
	array_splice($buttons,3,0,$addbuttons);
	array_splice($buttons,13,0,'alignjustify');
	array_splice($buttons,15,0,$addbuttons2);
    return $buttons;
}
function add_mce_buttons_2($buttons) {
 	array_splice($buttons, 0, count($buttons));
	$buttons[] = 'fontselect';
	$buttons[] = 'fontsizeselect';
	$buttons[] = 'styleselect';
	$buttons[] = 'forecolor';
	$buttons[] = 'backcolor';
	$buttons[] = 'outdent';
	$buttons[] = 'indent';
	$buttons[] = 'undo';
	$buttons[] = 'redo';
	$buttons[] = 'cut';
	$buttons[] = 'copy';
	$buttons[] = 'paste';
	$buttons[] = 'pastetext';
	$buttons[] = 'removeformat';
	$buttons[] = 'charmap';
	$buttons[] = 'newdocument';//新建文本（类似于清空文本）
	if ( ! wp_is_mobile() ) {
		$buttons[] = 'wp_help';
	}
	//$buttons[] = 'del';
	//$buttons[] = 'anchor';//锚文本
	//$buttons[] = 'image';//插入图片
	//$buttons[] = 'code';//打开HTML代码编辑器
	//$buttons[] = 'cleanup';//清除冗余代码
	//$buttons[] = 'wp_adv';//隐藏按钮显示开关
	//$buttons[] = 'wp_adv_start';//隐藏按钮区起始部分
	//$buttons[] = 'wp_adv_end';//隐藏按钮区结束部分
	//$buttons[] = 'separator';
	return $buttons;
}
add_filter("mce_buttons", "add_mce_buttons");
add_filter("mce_buttons_2", "add_mce_buttons_2");
/*
*Press Ctrl+Enter to reply a comment in edit-comments page
*/
//global $pagenow;
if(is_admin()&&$pagenow=='edit-comments.php'):
	add_action('admin_footer', 'ctrlenter_reply_comments');
	function ctrlenter_reply_comments() {
		echo '<script type="text/javascript">
			jQuery(document).ready(function($){
				$("textarea").keypress(function(e){
					if(e.ctrlKey&&e.which==13||e.which==10){
	                    $("#replybtn").click();
	                }
				})
			})
		</script>';
	};
endif;
/**
 * post meta source
 * https://developer.wordpress.org/reference/functions/add_meta_box/
 */
$postmeta_source = array(
	array(
		"name" => "source_value",
		"std"  => "",
		"title"=> __('来源名','im').': '
	),
	array(
		"name" => "source_link",
		"std"  => "",
		"title"=> __('来源网址','im').': '
	)
);
if(im('article_source_section')):
	add_action("admin_menu","postmeta_source_bulid");
	add_action("save_post","postmeta_source_save");
endif;
function postmeta_source_bulid(){
	add_meta_box('postmeta_source_box',__('来源','im'),'postmeta_source_render','post','normal','low');
}
function postmeta_source_render($post){
	global $postmeta_source;
	foreach($postmeta_source as $meta_box){
		$meta_box_value = get_post_meta($post->ID,$meta_box['name'],true);
		if($meta_box_value == "")
			$meta_box_value = $meta_box['std'];
		echo '<p>'.esc_html($meta_box['title']).'</p>';
		echo '<p><input type="text" style="width:100%" value="'.esc_attr($meta_box_value).'" name="'.esc_attr($meta_box['name']).'"></p>';
	}
	echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
}
function postmeta_source_save($post_id){
	global $postmeta_source;
	if(!wp_verify_nonce(isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'',plugin_basename(__FILE__)))
		return;
    if(defined( 'DOING_AUTOSAVE' )&&DOING_AUTOSAVE){
        return;
    }
 	if(!current_user_can('edit_post',$post_id))
		return;
    foreach($postmeta_source as $meta_box){
    	if(!isset($_POST[$meta_box['name']]))
    		return;
   		$data = sanitize_text_field($_POST[$meta_box['name']]);
   		//if($data!=""){
   			update_post_meta( $post_id, $meta_box['name'], $data);
   		//}
   }
}
/**
 * post meta keywords&description
 */
$postmeta_keywords_description = array(
	array(
		"name" => "keywords",
		"std"  => "",
		"title"=> __('关键词','im').'：'
	),
	array(
		"name" => "description",
		"std"  => "",
		"title"=> __('描述','im').'：'
	)
);
if(im('post_keywords_discriptions_customizion')):
	add_action("admin_menu","postmeta_keywords_description_bulid");
	add_action("save_post","postmeta_keywords_description_save");
endif;
function postmeta_keywords_description_bulid(){
	$post_types = array( 'post', 'page' );
	add_meta_box('postmeta_keywords_description_box',__('自定义关键字和描述','im'),'postmeta_keywords_description_render',$post_types,'normal','high');
}
function postmeta_keywords_description_render($post){
	global $postmeta_keywords_description;
	foreach($postmeta_keywords_description as $meta_box){
		$meta_box_value = get_post_meta($post->ID,$meta_box['name'],true);
		if($meta_box_value == "")
			$meta_box_value = $meta_box['std'];
		echo '<p>'.esc_html($meta_box['title']).'</p>';
		if($meta_box['name'] == 'keywords'){
			echo '<p><input type="text" style="width:100%" value="'.esc_attr($meta_box_value).'" name="'.esc_attr($meta_box['name']).'"></p>';
		}else{
			echo '<p><textarea style="width:100%" name="'.esc_attr($meta_box['name']).'">'.esc_attr($meta_box_value).'</textarea></p>';
		}
	}
	echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__)).'" />';
}
function postmeta_keywords_description_save($post_id){
	global $postmeta_keywords_description;
	if(!wp_verify_nonce(isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'',plugin_basename(__FILE__)))
		return;
    if(defined( 'DOING_AUTOSAVE' )&&DOING_AUTOSAVE){
        return;
    }
 	if(!current_user_can('edit_post',$post_id))
		return;
    foreach($postmeta_keywords_description as $meta_box){
    	if(!isset($_POST[$meta_box['name']]))
    		return;
   		$data = sanitize_text_field($_POST[$meta_box['name']]);
   		//if($data!=""){
   			update_post_meta( $post_id, $meta_box['name'], $data);
   		//}
   }
}
/**
 * post meta direct_link
 * 
 */
$postmeta_direct_link = array(
	array(
		"name"  => "direct_link",
		"std"   => ""/*,
		"tittle"=> __("直达链接","im")."："*/
	)
);
if(im('direct_link')['list_article']||im('direct_link')['article_article']){
	add_action("admin_menu","postmeta_direct_link_build");
	add_action("save_post","postmeta_direct_link_save");
}
function postmeta_direct_link_build(){
	add_meta_box("postmeta_direct_link_box",__("直达链接","im"),"postmeta_direct_link_render","post","normal","high");
}
function postmeta_direct_link_render($post){
	global $postmeta_direct_link;
	foreach ($postmeta_direct_link as $meta_box) {
		$meta_box_value = get_post_meta($post->ID,$meta_box['name'],true);
		if($meta_box_value == "")
			$meta_box_value = $meta_box['std'];	
		echo '<p>'.(isset($meta_box['title'])?$meta_box['title']:'').'</p>';
		echo '<p><input type="text" style="width:100%" value="'.esc_attr($meta_box_value).'" name="'.esc_attr($meta_box['name']).'"></p>';
	}
	echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__)).'"/>';
}
function postmeta_direct_link_save($post_id){
	global $postmeta_direct_link;
	if(!wp_verify_nonce(isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'',plugin_basename(__FILE__)))
		return;
	if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) 
        return;
	if(!current_user_can('edit_post',$post_id))
		return;
	foreach ($postmeta_direct_link as $meta_box) {
		if(!isset($_POST[$meta_box['name']]))
			return;
		$data = sanitize_text_field( $_POST[$meta_box['name']] );
		update_post_meta( $post_id, $meta_box['name'], $data );
	}	
}
/**
 * post meta subtitle
 */
$postmeta_subtitle = array(
	array(
		"name" => "subtitle",
		"std"  => ""
	)
);
if(im('enable_subtitle')){
	add_action("admin_menu","postmeta_subtitle_build");
	add_action("save_post","postmeta_subtitle_save");
}
function postmeta_subtitle_build(){
	$post_type = array('post','page');
	add_meta_box('postmeta_subtitle_box',__('副标题','im'),'postmeta_subtitle_render',$post_type,'normal','high');
}
function postmeta_subtitle_render($post){
	global $postmeta_subtitle;
	foreach ($postmeta_subtitle as $meta_box) {
		$meta_box_value = get_post_meta($post->ID,$meta_box['name'],true);
		if($meta_box_value=="")
			$meta_box_value = $meta_box['std'];
		echo '<p>'.(isset($meta_box['title'])?$meta_box['title']:'').'</p>';
		echo '<p><input type="text" style="width:100%" value="'.esc_attr($meta_box_value).'" name="'.esc_attr($meta_box['name']).'"></p>';
	}
	echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__)).'"/>';
}
function postmeta_subtitle_save($post_id){
	global $postmeta_subtitle;
	if(!wp_verify_nonce(isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'',plugin_basename(__FILE__)))
		return;
	if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) 
        return;
	if(!current_user_can('edit_post',$post_id))
		return;
	foreach ($postmeta_subtitle as $meta_box) {
		if(!isset($_POST[$meta_box['name']]))
			return;
		$data = sanitize_text_field( $_POST[$meta_box['name']] );
		update_post_meta( $post_id, $meta_box['name'], $data );
	}
}
/**
 * post meta thumbnail_link
 */
$postmeta_thumbnail_link = array(
	array(
		"name"  => "thumbnail",
		"std"   => ""
	)
);
if(im('thumbnails_link')){
	add_action("admin_menu","postmeta_thumbnail_link_build");
	add_action("save_post","postmeta_thumbnail_link_save");
}
function postmeta_thumbnail_link_build(){
	$post_type = array('post','page');
	add_meta_box('postmeta_thumbnail_link_box',__('外链缩略图','im'),'postmeta_thumbnail_link_render',$post_type,'side','low');
}
function postmeta_thumbnail_link_render($post){
	global $postmeta_thumbnail_link;
	foreach ($postmeta_thumbnail_link as $meta_box) {
		$meta_box_value = get_post_meta($post->ID,$meta_box['name'],true);
		if($meta_box_value=="")
			$meta_box_value = $meta_box['std'];
		echo '<p>'.(isset($meta_box['title'])?$meta_box['title']:'').'</p>';
		echo '<p><input type="text" style="width:100%" value="'.esc_attr($meta_box_value).'" name="'.esc_attr($meta_box['name']).'"></p>';
	}
	echo '<input type="hidden" name="post_newmetaboxes_noncename" id="post_newmetaboxes_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__)).'"/>';
}
function postmeta_thumbnail_link_save($post_id){
	global $postmeta_thumbnail_link;
	if(!wp_verify_nonce(isset($_POST['post_newmetaboxes_noncename'])?$_POST['post_newmetaboxes_noncename']:'',plugin_basename(__FILE__)))
		return;
	if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) 
        return;
	if(!current_user_can('edit_post',$post_id))
		return;
	foreach ($postmeta_thumbnail_link as $meta_box) {
		if(!isset($_POST[$meta_box['name']]))
			return;
		$data = sanitize_text_field( $_POST[$meta_box['name']] );
		update_post_meta( $post_id, $meta_box['name'], $data );
	}
}
/**
 * Custom taxonomy(category) meta
 * META:seo title,seo keywords,seo description
 */
$cat_meta = array(
	'title'		=> __('标题','im'),
	'keywords'		=> __('关键词','im'),
	'description'	=> __('描述','im')
);
class __Tax_Cat_Meta{
	function __construct(){
		add_action('category_add_form_fields',array($this,'add_tax_field'));
		add_action('category_edit_form_fields',array($this,'edit_tax_field'));
		add_action('edited_category',array($this,'save_tax_meta'),10,2);
		add_action('create_category',array($this,'save_tax_meta'),10,2);
	}
	public function add_tax_field(){
		global $cat_meta;
		foreach ($cat_meta as $field => $value) {
			if($field=="description"){
				echo '
					<div class="form-field">
						<label for="term_meta_'.$field.'">SEO '.$value.'</label>
						<textarea name="term_meta_'.$field.'" id="term_meta_'.$field.'" rows="5" cols="40"></textarea>
					</div>
				';
			}else{
				echo '
					<div class="form-field">
						<label for="term_meta_'.$field.'">SEO '.$value.'</label>
						<input name="term_meta_'.$field.'" id="term_meta_'.$field.'" type="text" value="" size="40">
					</div>
				';
			}
		}
		echo '<input type="hidden" name="im_term_meta" id="im_term_meta" value="'.wp_create_nonce(basename(__FILE__)).'"/>';
	}
	public function edit_tax_field($term){
		global $cat_meta;
		foreach ($cat_meta as $field => $value) {
			$get_cat_meta = get_term_meta($term->term_id,$field,true);
			$cat_meta_value = isset($get_cat_meta)?$get_cat_meta:'';
			if($field=="description"){
				echo '
					<tr class="form-field">
						<th scope="row">
							<label for="term_meta_'.$field.'">SEO '.$value.'</label>
						</th>
						<td>
							<textarea name="term_meta_'.$field.'" id="term_meta_'.$field.'" rows="5" cols="50">'.$cat_meta_value.'</textarea>
						</td>
					</tr>
				';
			}else{
				echo '
					<tr class="form-field">
						<th scope="row">
							<label for="term_meta_'.$field.'">SEO '.$value.'</label>
						</th>
						<td><input name="term_meta_'.$field.'" id="term_meta_'.$field.'" type="text" value="'.$cat_meta_value.'" size="40">
						</td>
					</tr>
				';
			}
		}
		echo '<input type="hidden" name="im_term_meta" id="im_term_meta" value="'.wp_create_nonce(basename(__FILE__)).'"/>';
	}
	public function save_tax_meta($term_id){
		if ( ! isset( $_POST['im_term_meta'] ) || ! wp_verify_nonce( $_POST['im_term_meta'], basename( __FILE__ ) ) )
        	return;
        if(!current_user_can('manage_categories')){
			return;
		}
		global $cat_meta;
		foreach ($cat_meta as $field => $value) {
			if(!isset($_POST["term_meta_".$field]))
				return;
			$data = sanitize_text_field( $_POST["term_meta_".$field] );
			update_term_meta( $term_id, $field, $data );
		}
	}
}
$tax_cat_mata = new __Tax_Cat_Meta();
// 添加到表格栏目
add_filter( 'manage_edit-category_columns', 'cat_term_columns' );
function cat_term_columns( $columns ) {
    $columns['keywords'] = '关键词(测试添加列)';
    return $columns;
}
add_filter( 'manage_category_custom_column', 'im_manage_term_custom_column', 10, 3 );
function im_manage_term_custom_column( $out, $column, $term_id ) {
    if ( 'keywords' === $column ) {
        $keywords = get_term_meta( $term_id, 'keywords', true );
        if ( ! $keywords )
            $keywords = '';
        $out = sprintf( '<p>%s</p>', esc_attr( $keywords ) );
    }
    return $out;
}
/**
 * admin_footer_text
 */
function admin_footer () {
    echo '<a href="http://www.webzgq.com" target="_blank">WEBZGQ.COM</a>';
}
add_filter('admin_footer_text', 'admin_footer');
/*
 * Override a default filter for 'textarea' sanitization and use a different one.
 * Because the original one filters some tags of html and script in section 分享代码
 * NOTE:This action influences all textareas
 */
add_action('admin_init','change_textarea_santiziation', 100);
function change_textarea_santiziation() {
	remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
	add_filter( 'of_sanitize_textarea', 'new_sanitize_textarea' );
}
function new_sanitize_textarea($input) {
	return $input;
}
/*
 * This is an example of how to add custom scripts to the options panel.
 * This one shows/hides the an option when a checkbox is clicked.
 *
 * You can delete it if you not using that option
 */
add_action( 'optionsframework_custom_scripts', 'optionsframework_custom_scripts' );
function optionsframework_custom_scripts() { ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#example_showhidden').click(function() {
  		jQuery('#section-example_text_hidden').fadeToggle(400);
	});
	if (jQuery('#example_showhidden:checked').val() !== undefined) {
		jQuery('#section-example_text_hidden').show();
	}
});
</script>
<?php
}