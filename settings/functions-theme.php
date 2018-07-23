<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

define('THEME_VERSION','1.0');
#######################################################
# BASIC
#######################################################
/**
 * require widgets
 */
require_once get_stylesheet_directory() . '/widgets/widget-index.php';
/**
 * require functions for admin
 */
if(is_admin()){
	require_once get_stylesheet_directory().'/settings/functions-admin.php';
}
/**
 * function im to get option value
 */
if ( ! function_exists( 'im' ) ) :
function im( $name, $default = false ) {
	$config = get_option( 'optionsframework' );
	if ( ! isset( $config['id'] ) ) {
		return $default;
	}
	$options = get_option( $config['id'] );
	if ( isset( $options[$name] ) ) {
		return $options[$name];
	}
	return $default;
}
endif;
/**
 * post formats
 */
add_theme_support('post_formats',array('aside'));
/**
 * hide admin bar
 */
add_filter('show_admin_bar', 'hide_admin_bar');
function hide_admin_bar($flag) {
	return false;
}
/**
 * no self pingback
 */
add_action('pre_ping','noself_ping');
function noself_ping($links){
	$home = get_option('home');
	foreach($links as $l => $link){
		if(0===strpos($link,$home)){
			unset($links[$l]);
		}
	}
}
/**
 * smilies src
 */
add_filter('smilies_src','_smilies_src',1,10);
function _smilies_src($img_src,$img,$siteurl){
	return get_stylesheet_directory_uri().'/assets/images/smilies/'.$img;
}
/**
 * register nav-menu
 */
if(function_exists('register_nav_menus')){
	register_nav_menus(array(
		'nav'     => __('网站导航','im'),
		'topmenu' => __('顶部菜单','im')
	));
}
/**
 * register sidebar
 */
if(function_exists('register_sidebar')){
	$sidebars = array(
		'public_header' => __('公共头部','im'),
		'public_footer' => __('公共底部','im'),
		'home'          => __('首页','im'),
		'cat'           => __('分类页','im'),
		'tag'           => __('标签页','im'),
		'search'        => __('搜索页','im'),
		'single'        => __('文章页','im')
	);
	foreach ($sidebars as $key => $value) {
		register_sidebar(array(
			'name'          => $value,
			'id'            => $key,
			'description'   => sprintf(__('放置在这里的小工具会显示在%s的侧栏上。') , $value),
			'class'         => 'sidebar-'.$key,
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widgettitle">',
			'after_title'   => '</h3>'
		));
	}
}
#######################################################
# load scripts(Performance Settings)
#######################################################
add_action('wp_enqueue_scripts','_load_scripts');
function _load_scripts(){
	if(!is_admin()){
		wp_deregister_script('jquery');
		// delete l10n.js
		wp_deregister_script('l10n');
		$purl = get_stylesheet_directory_uri();
		// common css
		_cssloader(array('bootstrap' => $purl.'/assets/css/bootstrap.min.css','fontawesome' => $purl.'/assets/css/font-awesome.min.css','main' => 'main'));
		// stylesheet for user page
		if(is_page_template('pages/user.php')){
			_cssloader(array('user' => 'user'));
		}
		$jss = array(
			'localhost' => array(
				'jquery' => $purl.'/assets/js/libs/jquery.min.js',
				'bootstrap' => $purl.'/assets/js/libs/bootstrap.min.js'
			),
			'baidu' => array(
				'jquery' => 'http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js',
				'bootstrap' => 'http://apps.bdimg.com/libs/bootstrap/3.2.0/js/bootstrap.min.js'
			),
			'offi_site' => array(
				'jquery' => '//code.jquery.com/jquery-1.9.1.min.js',
				'bootstrap' => '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js'
			)
		);
		wp_register_script('jquery',im('js_cdn')?$jss[im('js_cdn')]['jquery']:$jss['localhost']['jquery'],false,THEME_VERSION,(im('jquery_bottom')?true:false));
		wp_register_script('bootstrap',im('js_cdn')?$jss[im('js_cdn')]['bootstrap']:$jss['localhost']['bootstrap'],false,THEME_VERSION,true);
		_jsloader(array('loader'));
	}
}
function _cssloader($arr){
	foreach ($arr as $key => $value) {
		$href = $value;
		if(strstr($href,'//')===false){
			$href = get_stylesheet_directory_uri().'/assets/css/'.$value.'.css';
		}
		wp_enqueue_style('_'.$key,$href,array(),THEME_VERSION,'all');
	}
}
function _jsloader($arr){
	foreach ($arr as $item) {
		wp_enqueue_script('_'.$item,get_stylesheet_directory_uri().'/assets/js/'.$item.'.js',array(),THEME_VERSION,true);
	}
}
#######################################################
# Remove redundant codes
#######################################################

#######################################################
# HEADER
#######################################################
add_action('wp_head','_the_header');
function _the_header(){
	_the_keywords();
	_the_description();
	_the_head_css();
	_the_head_code();
	_post_views_record();
}
#######################################################
# FOOTER
#######################################################
add_action('wp_footer','_the_footer');
/**
 * 
 */
#######################################################
# Basic Settings
#######################################################
function _moloader($name = '',$apply = true){
	if(!function_exists($name)){
		include get_stylesheet_directory().'/modules/'.$name.'.php';
	}
	if($apply&&function_exists($name)){
		$name();
	}
}
function _bodyclass(){
	$class = '';
	if(is_super_admin()){
		$class .= 'logged-admin';
	}
	if(im('nav_fixed')&&!is_page_template('pages/resetpassword.php')){
		$class .= 'nav_fixed';
	}
	if(im('list_comments_r')){
		$class .= 'list-comments-r';
	}
	if((is_single()||is_page())&&im('post_content_indent')){
		$class .= 'p_indent';
	}
	if((is_single()||is_page())&&comments_open()){
		$class .= 'comment-open';
	}
	if(im('list_type')=='text'){
		$class .= 'list-text';
	}
	if(is_category()){
		_moloader('mo_is_minicat',false);
		if(mo_is_minicat()){
			$class .= 'site-minicat';
		}
	}
	$class .= 'site-layout-'.(im('show_sidebar')?im('show_sidebar'):'2');
	return trim($class);
}
/**
 * nav-menu
 */
function _the_menu($location = 'nav'){
	echo str_replace("</ul></div>", "", preg_replace("/<div[^>]*><ul[^>]*>/", "", wp_nav_menu(array('theme_location'=>$location,'echo'=>false))));
}
/**
 * logo
 */
function _the_logo(){
	$tag = is_home()?'h1':'div';
	$site_name = get_bloginfo('name').(get_bloginfo('description')?_get_delimiter().get_bloginfo('description'):'');
	echo '<'.$tag.' class="logo"><a href="'.get_bloginfo('url').'" title="'.$site_name.'"><img src="'.im('logo_src').'" alt="'.$site_name.'">'.get_bloginfo('name').'</a></'.$tag.'>';
}
/**
 * 阅读量
 */
function _post_views_record(){
	if(is_singular()){
		global $post;
		$post_ID = $post->ID;
		if($post_ID){
			$post_views = (int)get_post_meta($post_ID,'views',true);
			if(!update_post_meta($post_ID,'views',($post_views+1))){
				add_post_meta($post_ID,'views',1,true);
			}
		}
	}
}
function _get_post_views($before = '阅读(',$after = ')'){
	global $post;
	$post_ID = $post->ID;
	$views = (int)get_post_meta($post_ID,'views',true);
	return $before.$views.$after;
}
/**
 * 评论数
 */
function _get_post_comments($before = '评论(',$after = ')'){
	return $before.get_comments_number_text('0','1','%').$after;
}
/**
 * Excerpt
 */
add_filter('excerpt_length','_excerpt_length');
function _excerpt_length($length){
	return 120;
}
function _get_excerpt($limit = 120,$after = '...'){
	$excerpt = get_the_excerpt();
	if(_new_strlen($excerpt)>$limit){
		return _str_cut(strip_tags($excerpt),0,$limit,$after);
	}else{
		return $excerpt;
	}
}
/**
 * Open Article Links In New Tab
 */
function _post_target_blank(){
	return im('target_blank')?'target="_blank"':'';
}
/**
 * thumbnail
 */
add_theme_support('post-thumbnails');
set_post_thumbnail_size(220, 150, true);
function _get_post_thumbnail($size = 'thumbnail',$class = 'thumb'){
	global $post;
	$r_src = '';
	if(has_post_thumbnail()){
		$domsxe = get_the_post_thumbnail();
		preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $domsxe, $strResult, PREG_PATTERN_ORDER);
		$images = $strResult[1];
		foreach ($images as $src) {
			$r_src = $src;
			break;
		}
	}else{
		$thumblink = get_post_meta($post->ID,'thumbnail',true);
		if(im('thumbnails_link')&&!empty($thumblink)){
			$r_src = $thumblink;
		}elseif (im('thumbnails')) {
			$content = $post->post_content;  
	        preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $content, $strResult, PREG_PATTERN_ORDER);  
	        $images = $strResult[1];
	        foreach ($images as $src) {
	        	if(im('thumbnails_suffix')){
	        		$filetype = _get_filetype($src);
	        		$src = rtrim($src,'.'.$filetype).im('thumbnails_suffix').'.'.$filetype;
	        	}
	        	$r_src = $src;
	        	break;
	        }
		}
	}
	if($r_src){
		if(im('ajax_thumbnail')){
			return sprintf('<img data-src="%s" alt="%s" src="%s" class="'.$class.'">', $r_src, $post->post_title._get_delimiter().get_bloginfo('name'), get_stylesheet_directory_uri().'/img/thumbnail.png');
		}else{
			return sprintf('<img src="%s" alt="%s" class="'.$class.'">', $r_src, $post->post_title._get_delimiter().get_bloginfo('name'));
		}
	}else{
		return sprintf('<img data-thumb="default" src="%s" class="'.$class.'">', get_stylesheet_directory_uri().'/img/thumbnail.png');
	}
}
#获取文件类型
function _get_filetype($filename) {
    $exten = explode('.', $filename);
    return end($exten);
}
#根据附件链接获取附件ID
function _get_attachment_id_from_src($link) {
	global $wpdb;
	$link = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link);
	return $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE guid='$link'");
}
#######################################################
# Article Page Setting
#######################################################
#文章（包括feed）末尾加版权说明【暂停使用】
#add_filter('the_content', '_post_copyright');
function _post_copyright($content) {
	_moloader('mo_is_minicat', false);
	if ( !is_page() && !mo_is_minicat() ) {
		if (im('ads_post_02')) {
			$content .= '<p class="asb-post-footer"><b>AD：</b><strong>【' . im('ads_post_02_prefix') . '】</strong><a'.(im('ads_post_02_link_blank')?' target="_blank"':'').' href="' . im('ads_post_02_link') . '">' . im('ads_post_02_title') . '</a></p>';
		}
		if( im('post_copyright') ){
			$content .= '<p class="post-copyright">' . im('post_copyright_prefix') . '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a> &raquo; <a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
		}
	}
	return $content;
}
#######################################################
# SEO
#######################################################
/**
 * hythen
 */
function _get_delimiter(){
	return im('hythen')?im('hythen'):'-';
}
/**
 * subtitle
 */
function get_the_subtitle($span=true){
	global $post;
	$post_ID = $post->ID;
	$subtitle = get_post_meta($post_ID,'subtitle',true);
	if(!empty($subtitle)){
		if($span){
			return '<span>'.$subtitle.'</span>';
		}else {
			return ' '.$subtitle;
		}
	}else{
		return false;
	}
}
/**
 * Title
 * https://developer.wordpress.org/reference/functions/wp_title/
 */
function _title(){
	global $new_title;
	if($new_title) return $new_title;
	global $paged;
	$html = '';
	$t = trim(wp_title('',false));
	if((is_single()||is_page())&&get_the_subtitle(false)){
		$t .= get_the_subtitle(false);
	}
	if($t){
		$html .= $t._get_delimiter();
	}
	$html .= get_bloginfo('name');
	if(is_home()){
		if($paged>1){
			$html .= _get_delimiter().__('最新发布','im');
		}else if(get_option('blogdescription')){
			$html .= _get_delimiter().get_option('blogdescription');
		}
	}
	if(is_category()){
		global $wp_query;
		$cat_ID = get_query_var('cat');
		$cat_title = get_term_meta($cat_ID,'title',true);
		if($cat_title){
			$html = $cat_title;
		}
	}
	if($paged>1){
		$html .= _get_delimiter().'第'.$paged.'页';
	}
	return $html;
}
/**
 * keywords
 */
function _the_keywords(){
	global $new_keywords;
	if($new_keywords){
		echo "<meta name=\"keywords\" content=\"{$new_keywords}\" />\n";
		return;
	}
	global $s,$post;
	$keywords = '';
	if(is_singular()){
		if(get_the_tags($post->ID)){
			foreach(get_the_tags($post->ID) as $tag){
				$keywords .= $tag->name.',';
			}
		}
		foreach(get_the_category($post->ID) as $category){
			$keywords .= $category->cat_name . ',';
		}
		$keywords = substr_replace($keywords, '', -2);
		$the = trim(get_post_meta($post->ID,'keywords',true));
		if($the){
			$keywords = $the;
		}
	}elseif(is_home()){
		$keywords = im('site_keywords');
	}elseif(is_tag()){
		$keywords = single_tag_title('',false);
	}elseif(is_category()){
		global $wp_query;
		$cat_ID = get_query_var('cat');
		$keywords = get_term_meta($cat_ID,'keywords',true);
		if(!$keywords){
			$keywords = single_cat_title('',false);
		}
	}elseif(is_search()){
		$keywords = esc_html($s,1);
	}else{
		$keywords = trim(wp_title('',false));
	}
	if($keywords){
		echo "<meta name=\"keywords\" content=\"{$keywords}\" />\n";
	}
}
/**
 * description
 */
function _the_description(){
	global $new_description;
	if($new_description){
		echo "<meta name=\"description\" content=\"$new_description\" />\n";
	}
	global $s,$post;
	$description = '';
	$blog_name = get_bloginfo('name');
	if(is_singular()){
		if(!empty($post->post_excerpt)){
			$text = $post->post_excerpt;
		}else{
			$text = $post->post_content;
		}
		$description = trim(str_replace(array("\r\n","\r","\n","　"," ")," ",str_replace("\"","'",strip_tags($text))));
		$description = mb_substr($description,0,200,'utf-8');
		if(!$description){
			$description = $blog_name."-".trim(wp_title('',false));
		}
		$the = trim(get_post_meta($post->ID,'description',true));
		if($the){
			$description = $the;
		}
	}elseif(is_home()){
		$description = im('site_descriptions');
	}elseif(is_tag()){
		$description = trim(strip_tags(tag_description()));
	}elseif(is_category()){
		global $wp_query;
		$cat_ID = get_query_var('cat');
		$description = get_term_meta($cat_ID,'description',true);
		if(!$description){
			$description = trim(strip_tags(category_description()));
		}
	}elseif(is_search()){
		$description = $blog_name.":'".esc_html($s,1)."'的搜索结果";
	}else{
		$description = $blog_name."'".trim(wp_title('',false))."'";
	}
	if($description){
		echo "<meta name=\"description\" content=\"$description\" />\n";	
	}	
}
/**
 * no category base in url
 */
if(im('no_cat_in_url')&&!function_exists('no_category_base_refresh_rules')){
	/* hooks */
	register_activation_hook(__FILE__,    'no_category_base_refresh_rules');
	register_deactivation_hook(__FILE__,  'no_category_base_deactivate');
	/* actions */
	add_action('created_category',  'no_category_base_refresh_rules');
	add_action('delete_category',   'no_category_base_refresh_rules');
	add_action('edited_category',   'no_category_base_refresh_rules');
	add_action('init',              'no_category_base_permastruct');
	/* filters */
	add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
	add_filter('query_vars',             'no_category_base_query_vars');    // Adds 'category_redirect' query variable
	add_filter('request',                'no_category_base_request');       // Redirects if 'category_redirect' is set
	function no_category_base_refresh_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	function no_category_base_deactivate() {
		remove_filter( 'category_rewrite_rules', 'no_category_base_rewrite_rules' ); // We don't want to insert our custom rules again
		no_category_base_refresh_rules();
	}
	/**
	 * Removes category base.
	 * @return void
	 */
	function no_category_base_permastruct()
	{
		global $wp_rewrite;
		global $wp_version;
		if ( $wp_version >= 3.4 ) {
			$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
		} else {
			$wp_rewrite->extra_permastructs['category'][0] = '%category%';
		}
	}
	/**
	 * Adds our custom category rewrite rules.
	 * @param  array $category_rewrite Category rewrite rules.
	 * @return array
	 */
	function no_category_base_rewrite_rules($category_rewrite) {
		global $wp_rewrite;
		$category_rewrite=array();
		/* WPML is present: temporary disable terms_clauses filter to get all categories for rewrite */
		if ( class_exists( 'Sitepress' ) ) {
			global $sitepress;
			remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
			$categories = get_categories( array( 'hide_empty' => false ) );
			add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ), 10, 4 );
		} else {
			$categories = get_categories( array( 'hide_empty' => false ) );
		}

		foreach( $categories as $category ) {
			$category_nicename = $category->slug;

			if ( $category->parent == $category->cat_ID ) {
				$category->parent = 0;
			} elseif ( $category->parent != 0 ) {
				$category_nicename = get_category_parents( $category->parent, false, '/', true ) . $category_nicename;
			}
			$category_rewrite['('.$category_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
			$category_rewrite["({$category_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
			$category_rewrite['('.$category_nicename.')/?$'] = 'index.php?category_name=$matches[1]';
		}
		// Redirect support from Old Category Base
		$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		$old_category_base = trim( $old_category_base, '/' );
		$category_rewrite[$old_category_base.'/(.*)$'] = 'index.php?category_redirect=$matches[1]';
		return $category_rewrite;
	}

	function no_category_base_query_vars($public_query_vars) {
		$public_query_vars[] = 'category_redirect';
		return $public_query_vars;
	}
	/**
	 * Handles category redirects.
	 * @param $query_vars Current query vars.
	 * @return array $query_vars, or void if category_redirect is present.
	 */
	function no_category_base_request($query_vars) {
		if( isset( $query_vars['category_redirect'] ) ) {
			$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
			status_header( 301 );
			header( "Location: $catlink" );
			exit();
		}
		return $query_vars;
	}
	// 
}
#######################################################
# ADs
#######################################################
function _the_ads($name = '',$class = ''){
	if(!im($name)) return;
	if(wp_is_mobile()){
		echo '<div class="asb asb-m '.$class.'">'.im($name.'_m').'</div>';
	}else{
		echo '<div class="asb '.$class.'">'.im($name.'_nm').'</div>';
	}
}
#######################################################
# INTERACTION
#######################################################
/**
 * get Gravatar avatar
 */
function _get_default_avatar(){
	return get_stylesheet_directory_uri().'/assets/img/avatar-default.png';
}
function _get_user_avatar($user_id = '') {
	if (!$user_id) {
		return false;
	}
	$avatar = get_user_meta($user_id, 'avatar',true);
	if ($avatar) {
		return $avatar;
	} else {
		return false;
	}
}
function _get_the_avatar($user_id = '', $user_email = '', $src = false, $size = 50) {
	$user_avtar = _get_user_avatar($user_id);
	if ($user_avtar) {
		$attr = 'data-src';
		if ($src) {
			$attr = 'src';
		}
		return '<img class="avatar avatar-' . $size . ' photo" width="' . $size . '" height="' . $size . '" ' . $attr . '="' . $user_avtar . '">';
	} else {
		$avatar = get_avatar($user_email, $size, _get_default_avatar());
		if ($src) {
			return $avatar;
		} else {
			return str_replace(' src=', ' data-src=', $avatar);
		}
	}
}
//
if(!im('gravatar_url')||im('gravatar_url')=='ssl'){
	add_filter('get_avatar', 'get_ssl_avatar');
}else if(im('gravatar_url')=='duoshuo'){
	add_filter('get_avatar', 'get_duoshuo_avatar', 10, 3);
}
// 从Gravatar官方ssl获取 
function get_ssl_avatar($avatar) {
	$avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="https://secure.gravatar.com/avatar/$1?s=$2&d=mm" class="avatar avatar-$2" height="50" width="50">',$avatar);
	return $avatar;
}
// 多说官方Gravatar头像调用(多说已关闭！！！)
function get_duoshuo_avatar($avatar) {
	$avatar = str_replace(array("www.gravatar.com","0.gravatar.com","1.gravatar.com","2.gravatar.com"),"gravatar.duoshuo.com",$avatar);
	return $avatar;
}
/**
 * COMMENTS
 */
//用户评论被回复时邮件通知
add_action('comment_post', '_comment_mail_notify');
function _comment_mail_notify($comment_id) {
	$admin_notify = '1';// admin 要不要收回复通知 ( '1'=要 ; '0'=不要 )
	$admin_email = get_bloginfo('admin_email');// $admin_email 可改为你指定的 e-mail.
	$comment = get_comment($comment_id);
	$comment_author_email = trim($comment->comment_author_email);
	$parent_id = $comment->comment_parent ? $comment->comment_parent : '';
	global $wpdb;
	if ($wpdb->query("Describe {$wpdb->comments} comment_mail_notify") == '') {
		$wpdb->query("ALTER TABLE {$wpdb->comments} ADD COLUMN comment_mail_notify TINYINT NOT NULL DEFAULT 0;");
	}

	if (($comment_author_email != $admin_email && isset($_POST['comment_mail_notify'])) || ($comment_author_email == $admin_email && $admin_notify == '1')) {
		$wpdb->query("UPDATE {$wpdb->comments} SET comment_mail_notify='1' WHERE comment_ID='$comment_id'");
	}

	$notify = $parent_id ? get_comment($parent_id)->comment_mail_notify : '0';
	$spam_confirmed = $comment->comment_approved;
	if ($parent_id != '' && $spam_confirmed != 'spam' && $notify == '1') {
		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));// e-mail 发出点, no-reply 可改为可用的 e-mail.
		$to = trim(get_comment($parent_id)->comment_author_email);
		$subject = 'Hi，您在 [' . get_option("blogname") . '] 的留言有人回复啦！';

		$letter = (object) array(
			'author' => trim(get_comment($parent_id)->comment_author),
			'post' => get_the_title($comment->comment_post_ID),
			'comment' => trim(get_comment($parent_id)->comment_content),
			'replyer' => trim($comment->comment_author),
			'reply' => trim($comment->comment_content),
			'link' => htmlspecialchars(get_comment_link($parent_id)),
			'sitename' => get_option('blogname')
		);

		$additional_info = '';
		if(im('mail_info1')&&im('mail_info1_link')){
			$additional_info .= '<a href="' . im('mail_info1_link') . '" target="_blank" style="text-decoration:underline;color:#61B3E6;font-family:Microsoft Yahei">' . im('mail_info1') . '</a><br/>';
		}
		if(im('mail_info2')&&im('mail_info2_link')){
			$additional_info .= '<a href="' . im('mail_info2_link') . '" target="_blank" style="text-decoration:underline;color:#61B3E6;font-family:Microsoft Yahei">' . im('mail_info2') . '</a><br/>';
		}
		
		$message = '
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse"><tbody><tr><td><table width="600" cellpadding="0" cellspacing="0" border="0" align="center" style="border-collapse:collapse"><tbody><tr><td><table width="100%" cellpadding="0" cellspacing="0" border="0"><tbody><tr><td width="73" align="left" valign="top" style="border-top:1px solid #d9d9d9;border-left:1px solid #d9d9d9;border-radius:5px 0 0 0"></td><td valign="top" style="border-top:1px solid #d9d9d9"><div style="font-size:14px;line-height:10px"><br><br><br><br></div><div style="font-size:18px;line-height:18px;color:#444;font-family:Microsoft Yahei">Hi, ' . $letter->author . '<br><br><br></div><div style="font-size:14px;line-height:22px;color:#444;font-weight:bold;font-family:Microsoft Yahei">您在' . $letter->sitename . '《' . $letter->post . '》的评论：</div><div style="font-size:14px;line-height:10px"><br></div><div style="font-size:14px;line-height:22px;color:#666;font-family:Microsoft Yahei">&nbsp; &nbsp;&nbsp; &nbsp; ' . $letter->comment . '</div><div style="font-size:14px;line-height:10px"><br><br></div><div style="font-size:14px;line-height:22px;color:#5DB408;font-weight:bold;font-family:Microsoft Yahei">' . $letter->replyer . ' 回复您：</div><div style="font-size:14px;line-height:10px"><br></div><div style="font-size:14px;line-height:22px;color:#666;font-family:Microsoft Yahei">&nbsp; &nbsp;&nbsp; &nbsp; ' . $letter->reply . '</div><div style="font-size:14px;line-height:10px"><br><br><br><br></div><div style="text-align:center"><a href="' . $letter->link . '" target="_blank" style="text-decoration:none;color:#fff;display:inline-block;line-height:44px;font-size:18px;background-color:#61B3E6;border-radius:3px;font-family:Microsoft Yahei">&nbsp; &nbsp;&nbsp; &nbsp;点击查看回复&nbsp; &nbsp;&nbsp; &nbsp;</a><br><br></div></td><td width="65" align="left" valign="top" style="border-top:1px solid #d9d9d9;border-right:1px solid #d9d9d9;border-radius:0 5px 0 0"></td></tr><tr><td style="border-left:1px solid #d9d9d9">&nbsp;</td><td align="left" valign="top" style="color:#999"><div style="font-size:8px;line-height:14px"><br><br></div><div style="min-height:1px;font-size:1px;line-height:1px;background-color:#e0e0e0">&nbsp;</div><div style="font-size:12px;line-height:20px;width:425px;font-family:Microsoft Yahei"><br>'.$additional_info.'此邮件由' . $letter->sitename . '系统自动发出，请勿回复！</div></td><td style="border-right:1px solid #d9d9d9">&nbsp;</td></tr><tr><td colspan="3" style="border-bottom:1px solid #d9d9d9;border-right:1px solid #d9d9d9;border-left:1px solid #d9d9d9;border-radius:0 0 5px 5px"><div style="min-height:42px;font-size:42px;line-height:42px">&nbsp;</div></td></tr></tbody></table></td></tr><tr><td><div style="min-height:42px;font-size:42px;line-height:42px">&nbsp;</div></td></tr></tbody></table></td></tr></tbody></table>';

		$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
		wp_mail($to, $subject, $message, $headers);
		//echo 'mail to '. $to. '<br/> ' . $subject. $message; // for testing
	}
}
//默认都邮件通知
add_action('comment_form', '_comment_add_checkbox');
function _comment_add_checkbox() {
	echo '<label for="comment_mail_notify" class="checkbox inline hide" style="padding-top:0"><input type="checkbox" name="comment_mail_notify" id="comment_mail_notify" value="comment_mail_notify" checked="checked"/>有人回复时邮件通知我</label>';
}
#######################################################
# CUSTOMIZIONS SETTINGS
#######################################################
/**
 * 自定义头部代码
 */
function _the_head_code(){
	if(im('head_code')){
		echo "\n<!--HEADER_CODE_START-->\n".im('head_code')."\n<!--HEADER_CODE_END-->\n";
	}
}
/**
 * 网站整体变灰
 * 主题风格
 * 网页最大宽度
 * 自定义CSS样式
 */
function _the_head_css(){
	$styles = '';
	if(im('site_gray')){
		$styles .= "html{filter:progid:DXImageTransform.Microsoft.BasicImage(grayscale=1);-webkit-filter: grayscale(100%);}";
	}
	if(im('site_width')&&im('site_width')!=='1200'){
		$styles .= ".container{max-width:".im('site_width')."px}";
	}
	$color = '';
	if(im('theme_skin')&&im('theme_skin')!=='45B6F7'){
		$color = im('theme_skin');
	}
	if(im('theme_skin_custom')&&im('theme_skin_custom')!=='#45B6F7'){
		$color = substr(im('theme_skin_custom'),1);
	}
	if($color){
		$styles .= 'a:hover, .site-navbar li:hover > a, .site-navbar li.active a:hover, .site-navbar a:hover, .search-on .site-navbar li.navto-search a, .topbar a:hover, .site-nav li.current-menu-item > a, .site-nav li.current-menu-parent > a, .site-search-form a:hover, .branding-primary .btn:hover, .title .more a:hover, .excerpt h2 a:hover, .excerpt .meta a:hover, .excerpt-minic h2 a:hover, .excerpt-minic .meta a:hover, .article-content .wp-caption:hover .wp-caption-text, .article-content a, .article-nav a:hover, .relates a:hover, .widget_links li a:hover, .widget_categories li a:hover, .widget_ui_comments strong, .widget_ui_posts li a:hover .text, .widget_ui_posts .nopic .text:hover , .widget_meta ul a:hover, .tagcloud a:hover, .textwidget a, .textwidget a:hover, .sign h3, #navs .item li a, .url, .url:hover, .excerpt h2 a:hover span, .widget_ui_posts a:hover .text span, .widget-navcontent .item-01 li a:hover span, .excerpt-minic h2 a:hover span, .relates a:hover span{color: #'.$color.';}.btn-primary, .label-primary, .branding-primary, .post-copyright:hover, .article-tags a, .pagination ul > .active > a, .pagination ul > .active > span, .pagenav .current, .widget_ui_tags .items a:hover, .sign .close-link, .pagemenu li.active a, .pageheader, .resetpasssteps li.active, #navs h2, #navs nav, .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary, .tag-clouds a:hover{background-color: #'.$color.';}.btn-primary, .search-input:focus, #bdcs .bdcs-search-form-input:focus, #submit, .plinks ul li a:hover,.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary{border-color: #'.$color.';}.search-btn, .label-primary, #bdcs .bdcs-search-form-submit, #submit, .excerpt .cat{background-color: #'.$color.';}.excerpt .cat i{border-left-color:#'.$color.';}@media (max-width: 720px) {.site-navbar li.active a, .site-navbar li.active a:hover, .m-nav-show .m-icon-nav{color: #'.$color.';}}@media (max-width: 480px) {.pagination ul > li.next-page a{background-color:#'.$color.';}}';
	}
	if(im('cus_csscode')){
		$styles .= im('cus_csscode');
	}
	if($styles){
		echo '<style>'.$styles.'</style>';
	}
}
/**
 * 自定义底部代码
 */
function _the_footer(){
	if(im('foot_code')){
		echo "<!--FOOTER_CODE_START-->\n".im('foot_code')."\n<!--FOOTER_CODE_END-->\n";
	}
}
#######################################################
# 
#######################################################
#wordpress截断函数mb_strimwidth()失效的解决方法
#https://www.themepark.com.cn/wordpressjdhsmbstrimwidthsxdjb.html
function _str_cut($str, $start, $width, $trimmarker) {
	$output = preg_replace('/^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $start . '}((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $width . '}).*/s', '\1', $str);
	return $output . $trimmarker;
}
#统计字符串长度
function _new_strlen($str,$charset='utf-8') {        
    $n = 0; $p = 0; $c = '';
    $len = strlen($str);
    if($charset == 'utf-8') {
        for($i = 0; $i < $len; $i++) {
            $c = ord($str{$i});
            if($c > 252) {
                $p = 5;
            } elseif($c > 248) {
                $p = 4;
            } elseif($c > 240) {
                $p = 3;
            } elseif($c > 224) {
                $p = 2;
            } elseif($c > 192) {
                $p = 1;
            } else {
                $p = 0;
            }
            $i+=$p;$n++;
        }
    } else {
        for($i = 0; $i < $len; $i++) {
            $c = ord($str{$i});
            if($c > 127) {
                $p = 1;
            } else {
                $p = 0;
        	}
            $i+=$p;$n++;
        }
    }        
    return $n;
}
#时间
#或可参考https://developer.wordpress.org/reference/functions/human_time_diff/
function _get_time_ago($ptime) {
	$ptime = strtotime($ptime);
	$etime = time() - $ptime;
	if ($etime < 1) {
		return '刚刚';
	}
	$interval = array(
		12 * 30 * 24 * 60 * 60 => '年前 (' . date('Y-m-d', $ptime) . ')',
		30 * 24 * 60 * 60 => '个月前 (' . date('m-d', $ptime) . ')',
		7 * 24 * 60 * 60 => '周前 (' . date('m-d', $ptime) . ')',
		24 * 60 * 60 => '天前',
		60 * 60 => '小时前',
		60 => '分钟前',
		1 => '秒前',
	);
	foreach ($interval as $secs => $str) {
		$d = $etime / $secs;
		if ($d >= 1) {
			$r = round($d);
			return $r . $str;
		}
	};
}
#HTTPS
function curPageURL() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["HTTPS"] != "on") 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } 
    else 
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}