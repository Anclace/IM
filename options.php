<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */
function optionsframework_option_name() {
	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );
	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);
	// echo $themename;
}
/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'options_framework_theme'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */
function optionsframework_options() {
	// Theme Skin Color
	$theme_skin_colors = array(
		'45B6F7' => 100,
		'FF5E52' => 1,
		'2CDB87' => 2,
		'00D6AC' => 3,
		'16C0F8' => 4,
		'EA84FF' => 5,
		'FDAC5F' => 6,
		'FD77B2' => 7,
		'76BDFF' => 8,
		'C38CFF' => 9,
		'FF926F' => 10,
		'8AC78F' => 11,
		'C7C183' => 12,
		'555555' => 13
	);
	//List Type
	$list_type = array(
		'thumb' =>__('图文（缩略图尺寸：220*150，默认已自动裁剪）','im'),
		'text' =>__('文字','im'),
		'auto' =>__('自动（有缩略图时图文模式，否则文字模式）','im'),
	);
	//Post Widgets
	$post_widgets = array(
		'views' => __('阅读量（无需安装插件）','im'),
		'comments' => __('评论数（列表）','im'),
		'pubdate' => __('发布时间（列表）','im'),
		'authors' => __('作者（列表）','im'),
		'catlink' => __('分类链接（列表）','im')
	);
	$post_widgets_defaults = array(
		'views' => '1',
		'comments' => '1',
		'pubdate' => '1',
		'authors' => '1',
		'catlink' => '1'
	);
	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	//$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	//Pull all the link categories into an array
	$options_linkcats = array();
	$options_linkcats_obj = get_terms('link_category');
	foreach ($options_linkcats_obj as $linkcats) {
		$options_linkcats[$linkcats->term_id] = $linkcats->name;
	}
	// If using image radio buttons, define a directory path
	$imagepath =  get_stylesheet_directory_uri() . '/assets/images/';
	// ADs descriptions
	$ads_desc = __('可添加任意广告联盟代码或自定义代码', 'im');;
	$options = array();
	/**
	 * Basic Settings
	 */
	$options[] = array(
		'name' => __('基本设置','im'),
		'type' => 'heading'
	);
	// LOGO
	$options[] = array(
		'name' => __('LOGO','im'),
		'desc' => __('建议尺寸：140*32，格式PNG'),
		'id'   => 'logo_src',
		'std'  => $imagepath.'logo.png',
		'type' => 'upload'
	);
	// Brand Title
	$options[] = array(
		'name' => __('品牌文字','im'),
		'desc' => __('显示在logo旁边的两个短文字，换行填写两段文字','im'),
		'id'   => 'brand',
		'std'  => "第一行\n第二行",
		'type' => 'textarea',
		'settings' => array(
			'rows' => 2
		)
	);
	// Theme Skin
	$options[] = array(
		'name' => __('主题风格','im'),
		'desc' => __('选择你喜欢的颜色,可自定义','im'),
		'id'   => 'theme_skin',
		'std'  => '45B6F7',
		'type' => 'colorradio',
		'options' => $theme_skin_colors
	);
	// Theme Skin Customize
	$options[] = array(
		'desc' => __('不喜欢上面的颜色可在此自定义，如不使用清空即可','im'),
		'id'   => 'theme_skin_custom',
		'type' => 'color'
	);
	// Show Sidebar
	$options[] = array(
		'name' => __('是否显示侧栏','im'),
		'desc' => __('显示侧栏','im'),
		'id'   => 'show_sidebar',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Content Max Width
	$options[] = array(
		'name' => __('网页最大宽度','im'),
		'desc' => __('单位：px','im'),
		'id'   => 'site_width',
		'std'  => 1200,
		'type' => 'text',
		'class'=> 'mini'
	);
	// Fixed Nav In PC Frontend
	$options[] = array(
		'name' => __('PC端滚动时导航固定','im'),
		'desc' => __('开启（由于网址导航页左侧菜单固定，对此页面无效）','im'),
		'id'   => 'nav_fixed',
		'type' => 'checkbox'
	);
	// Open Article Links In New Tab
	$options[] = array(
		'name' => __('新窗口打开文章','im'),
		'desc' => __('新窗口打开文章','im'),
		'id'   => 'target_blank',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Load Pages By Ajax
	$options[] = array(
		'name' => __('分页无限加载页数','im'),
		'desc' => __('为0时表示不开启该功能','im'),
		'id'   => 'ajaxpager',
		'std'  => 5,
		'type' => 'text',
		'class'=> 'mini'
	);
	// List Type
	$options[] = array(
		'name' => __('列表模式','im'),
		'id'   => 'list_type',
		'std'  => 'thumb',
		'type' => 'radio',
		'options' => $list_type
	);
	// Thumbnails
	$options[] = array(
		'name' => __('缩略图','im'),
		'desc' => __('自动提取文章首图为缩略图（如文章已设置特色图或外链缩略图，此设置无效）','im'),
		'id'   => 'thumbnails',
		'type' => 'checkbox'
	);
	// Thumbnails Auto
	$options[] = array(
		'desc' => __('将自动加入文章首图地址后缀之前，默认为空。如：文章首图地址”aaa/bbb.jpg”，此处填写的字符是“-220x150”，则缩略图实际地址为“aaa/bbb-220x150.jpg”','im'),
		'id'   => 'thumbnails_suffix',
		'std'  => 'thumb',
		'type' => 'text',
		'class'=> 'mini thumbnails_hidden',
	);
	// Thumbnails Link
	$options[] = array(
		'desc' => __('外链缩略图（开启后会在后台编辑文章时出现外链缩略图地址输入框，填写一个图片地址即可在文章列表中显示。注：如文章添加了特色图像，列表中显示的缩略图优先选择该特色图像。）','im'),
		'id'   => 'thumbnails_link',
		'type' => 'checkbox'
	);
	// Post widgets
	$options[] = array(
		'name' => __('文章小部件','im'),
		'desc' => __('列表中是否显示小部件','im'),
		'id'   => 'post_widgets',
		'std'  => $post_widgets_defaults,
		'type' => 'multicheck',
		'options' => $post_widgets
	);
	// Comment Pop Align Right
	$options[] = array(
		'name' => __('列表中评论数靠右','im'),
		'desc' => __('列表中评论数靠右','im'),
		'id'   => 'list_comments_r',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Author Link
	$options[] = array(
		'name' => __('作者名加链接','im'),
		'desc' => __('列表、文章有作者的地方都会加上链接','im'),
		'id'   => 'author_link',
		'type' => 'checkbox'
	);
	// Footer Extra Info
	$options[] = array(
		'name' => __('网站底部信息','im'),
		'desc' => __('版权、网站地图（网站地图可自行使用sitemap插件自动生成）等','im'),
		'id'   => 'footer_extra_info',
		'std'  => '<a href="'.site_url('/sitemap.xml').'">'.__('网站地图', 'haoui').'</a>'."\n",
		'type' => 'textarea'
	);
	// Site Gray
	$options[] = array(
		'name' => __('网站整体变灰','im'),
		'desc' => __('网站整体变灰','im'),
		'id'   => 'site_gray',
		'type' => 'checkbox'
	);
	// Footer Spreadings
	$options[] = array(
		'name' => __('站点底部推广区','im'),
		'desc' => __('是否开启站点底部推广区','im'),
		'id'   => 'footer_spreadings',
		'type' => 'checkbox'
	);
	// Footer Spreadings Title
	$options[] = array(
		//'name' => __('标题','im'),
		'desc' => __('标题：建议20字内','im'),
		'id'   => 'footer_spreadings_title',
		'std'  => '更专业 更方便',
		'type' => 'text',
		'class'=> 'footer_spreadings_hidden'
	);
	// Footer Spreadings Buttons
	for($i = 1;$i <= 2;$i++){
		// Footer Spreadings Buttons Title
		$options[] = array(
			'name' => __('按钮','im').$i,
			'desc' => __('按钮文字','im'),
			'id'   => 'footer_spreadings_btn_title_'.$i,
			'type' => 'text',
			'class'=> 'footer_spreadings_hidden'
		);
		// Footer Spreadings Buttons Link
		$options[] = array(
			'desc' => __('按钮链接','im'),
			'id'   => 'footer_spreadings_btn_link_'.$i,
			'type' => 'text',
			'class'=> 'footer_spreadings_hidden'
		);
		// Footer Spreadings Buttons Link Open Style
		$options[] = array(
			'desc' => __('新窗口打开','im'),
			'id'   => 'footer_spreadings_btn_link_target_'.$i,
			'type' => 'checkbox',
			'class'=> 'footer_spreadings_hidden'
		);
	}
	/**
	 * Home Page Settings
	 */
	$options[] = array(
		'name' => __('首页设置','im'),
		'type' => 'heading'
	);
	// Don't show articles in these categories
	$options[] = array(
		'name' => __('首页不显示该分类下文章','im'),
		'id'   => 'ignore_articles_cat',
		'type' => 'multicheck',
		'options' => $options_categories
	);
	// Don't show these posts
	$options[] = array(
		'name' => __('首页不显示以下ID文章','im'),
		'desc' => __('每行填写一个文章ID','im'),
		'id'   => 'ignore_posts',
		'type' => 'textarea',
		'settings' => array(
			'rows' => 5
		)
	);
	// Notice  District
	$options[] = array(
		'name' => __('公告模块','im'),
		'desc' => __('显示公告模块','im'),
		'id'   => 'notice_district',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Notice District Title
	$options[] = array(
		'name' => __('公告模块标题','im'),
		'desc' => __('建议4个字以内','im'),
		'id'   => 'notice_district_title',
		'std'  => '站点公告',
		'type' => 'text',
		'class'=> 'notice_district_hidden'
	);
	// Notice District Contents From The Category Bellow
	$options[] = array(
		'name' => __('选择分类设置为网站公告','im'),
		'desc' => __('选择该分类为网站公告','im'),
		'id'   => 'notice_district_cat',
		'type' => 'select',
		'class'=> 'notice_district_hidden',
		'options' => $options_categories
	);
	// Carousel Figure
	$options[] = array(
		'name' => __('轮播图','im'),
		'desc' => __('开启轮播图','im'),
		'id'   => 'carousel_figure',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Carousel Figure Order
	$options[] = array(
		'name' => __('图片排序','im'),
		'desc' => __('数字间空格隔开，默认1 2 3 4 5','im'),
		'id'   => 'carousel_figure_order',
		'std'  => '1 2 3 4 5',
		'type' => 'text',
		'class'=> 'carousel_figure_hidden'
	);
	// Carousel Figure Item
	for($i = 1;$i <= 5;$i++){
		// Carousel Figure Item Title
		$options[] = array(
			'name' => __('图片','im').$i,
			'desc' => __('图片标题','im'),
			'id'   => 'carousel_figure_item_title_'.$i,
			'std'  => 'IM',
			'type' => 'text',
			'class'=> 'carousel_figure_hidden'
		);
		// Carousel Figure Item Link
		$options[] = array(
			'desc' => __('链接地址','im'),
			'id'   => 'carousel_figure_item_link_'.$i,
			'std'  => 'http://www.webzgq.com',
			'type' => 'text',
			'class'=> 'carousel_figure_hidden'
		);
		// Carousel Figure Item Image
		$options[] = array(
			'desc' => __('图片上传（820*200）','im'),
			'id'   => 'carousel_figure_item_image_'.$i,
			'std'  => $imagepath.'xiu.jpg',
			'type' => 'upload',
			'class'=> 'carousel_figure_hidden'
		);
		// Carousel Figure Item Open Style
		$options[] = array(
			'desc' => __('新窗口打开','im'),
			'id'   => 'carousel_figure_item_open_'.$i,
			'type' => 'checkbox',
			'class'=> 'carousel_figure_hidden'
		);
	}
	// List Section Title
	$options[] = array(
		'name' => __('列表板块标题','im'),
		'desc' => __('列表板块标题','im'),
		'id'   => 'list_section_title',
		'std'  => __('最新发布','im'),
		'type' => 'text'
	);
	// Contents of Right Part of the List Section Title  
	$options[] = array(
		'name' => __('列表板块标题右侧内容','im'),
		'desc' => __('列表板块标题右侧内容','im'),
		'id'   => 'list_section_title_r',
		'std'  => '<a href="链接地址">显示文字</a><a href="链接地址">显示文字</a><a href="链接地址">显示文字</a><a href="链接地址">显示文字</a>',
		'type' => 'textarea'
	);
	/**
	 * Article Page Settings
	 */
	$options[] = array(
		'name' => __('文章页设置','im'),
		'type' => 'heading'
	);
	// Hide Share Section In Mobile Frontend
	$options[] = array(
		'name' => __('手机端不显示分享模块','im'),
		'desc' => __('手机端不显示分享模块','im'),
		'id'   => 'hide_share_distric',
		'type' => 'checkbox'
	);
	// Enable subtitle
	$options[] = array(
		'name' => __('副标题','im'),
		'desc' => __('开启副标题','im'),
		'id'   => 'enable_subtitle',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Show the prev&nest post of current post
	$options[] = array(
		'name' => __('显示上下篇文章','im'),
		'desc' => __('显示上下篇文章','im'),
		'id'   => 'post_prevnext',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Show Author Info Section
	$options[] = array(
		'name' => __('显示文章作者介绍','im'),
		'desc' => __('显示文章作者介绍','im'),
		'id'   => 'post_author_info',
		'type' => 'checkbox'
	);
	// Related Posts Section
	$options[] = array(
		'name' => __('相关文章','im'),
		'desc' => __('是否显示相关文章','im'),
		'id'   => 'post_related_section',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Related Posts Section Title
	$options[] = array(
		'desc' => __('相关文章板块标题','im'),
		'id'   => 'post_related_section_title',
		'std'  => __('相关推荐',"im"),
		'type' => 'text',
		'class'=> 'post_related_section_hidden'
	);
	// Related Posts Number
	$options[] = array(
		'desc' => __('显示文章数量','im'),
		'id'   => 'post_related_num',
		'std'  => 10,
		'type' => 'text',
		'class'=> 'post_related_section_hidden'
	);
	// Article Source Section
	$options[] = array(
		'name' => __('文章来源','im'),
		'desc' => __('是否显示文章来源','im'),
		'id'   => 'article_source_section',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Article Source Link
	$options[] = array(
		'desc' => __('是否加上来源链接','im'),
		'id'   => 'article_source_section_link',
		'std'  => '1',
		'type' => 'checkbox',
		'class'=> 'article_source_section_hidden'
	);
	// Article Source Link Section Title
	$options[] = array(
		'desc' => __('自定义来源标题','im'),
		'id'   => 'article_source_section_title',
		'std'  => __('来源：','im'),
		'type' => 'text',
		'class'=> 'article_source_section_hidden'
	);
	// Post Content Indent
	$options[] = array(
		'name' => __('内容段落缩进','im'),
		'desc' => __('只对前端文章展示有效','im'),
		'id'   => 'post_content_indent',
		'type' => 'checkbox'
	);
	// Copyright Section After Content
	$options[] = array(
		'name' => __('文章页尾版权提示','im'),
		'desc' => __('是否显示文章页尾版权提示','im'),
		'id'   => 'post_copyright',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Copyright Section Title
	$options[] = array(
		'desc' => __('版权提示前缀','im'),
		'id'   => 'post_copyright_prefix',
		'std'  => __('未经允许不得转载：','im'),
		'type' => 'text',
		'class'=> 'post_copyright_hidden'
	);
	// Post keywords and discriptions
	$options[] = array(
		'name' => __('文章关键词及描述','im'),
		'desc' => __('自动使用主题配置的关键词和描述（具体规则可以自行查看页面源码）','im'),
		'id'   => 'post_keywords_discriptions',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Post keywords and discriptions customizion
	$options[] = array(
		'name' => __('自定义文章关键词及描述','im'),
		'desc' => __('开启后需在编辑文章时书写关键词和描述，如为空则使用如上规则，开启此项必须开启如上选项','im'),
		'id'   => 'post_keywords_discriptions_customizion',
		'type' => 'checkbox',
		'class'=> 'post_keywords_discriptions_hidden'
	);
	/**
	 * Unique Page Settings
	 */
	$options[] = array(
		'name' => __('独立页面','im'),
		'type' => 'heading'
	);
	// Site URL Navgation Page
	$options[] = array(
		'name' => __('网址导航页','im'),
		'desc' => __('标题下方描述','im'),
		'id'   => 'navpage_desc',
		'std'  => __('这里显示的是网址导航的一句话描述...','im'),
		'type' => 'text'
	);
	// Site URL Navgation Page Show Link Category
	$options[] = array(
		'desc' => __('要显示的链接分类','im'),
		'id'   => 'navpage_cats',
		'type' => 'multicheck',
		'options' => $options_linkcats
	);
	// Reader Wall(time limit)
	$options[] = array(
		'name' => __('读者墙','im'),
		'desc' => __('限制在多少个月内（单位：月）','im'),
		'id'   => 'readerwall_limit_time',
		'std'  => 200,
		'type' => 'text',
		'class'=> 'mini'
	);
	// Reader Wall Number
	$options[] = array(
		'desc' => __('显示个数','im'),
		'id'   => 'readerwall_limit_number',
		'std'  => 200,
		'type' => 'text',
		'class'=> 'mini'
	);
	// Page Left Part Menus
	$options[] = array(
		'name' => __('页面左侧菜单','im'),
		'desc' => __('页面左侧菜单','im'),
		'id'   => 'page_left_part_menus',
		'type' => 'multicheck',
		'options' => $options_pages
	);
	// Friend Links Categories
	$options[] = array(
		'name' => __('友情链接分类选择','im'),
		'desc' => __('友情链接分类选择','im'),
		'id'   => 'friend_links_cat',
		'type' => 'multicheck',
		'options' => $options_linkcats
	);
	/**
	 * SEO Settings
	 */
	$options[] = array(
		'name' => __('SEO设置','im'),
		'type' => 'heading'
	);
	// Site keywords
	$options[] = array(
		'name' => __('站点默认关键字','im'),
		'desc' => __('建议个数在5-10之间，用英文逗号隔开','im'),
		'id'   => 'site_keywords',
		'type' => 'textarea',
		'settings' => array(
			'rows' => 2
		)
	);
	// Site descriptions
	$options[] = array(
		'name' => __('站点描述','im'),
		'desc' => __('建议字数在30—70个之间','im'),
		'id'   => 'site_descriptions',
		'type' => 'textarea',
		'settings' => array(
			'rows' => 2
		)
	);
	// Hyphen
	$options[] = array(
		'name' => __('全站连接符','im'),
		'desc' => __('一般为“-”或“_”，已经选择，请勿更改','im'),
		'id'   => 'hythen',
		//'std'  =>  _im('connector') ? _im('connector') : '-',
		'std'  => '-',
		'type' => 'text',
		'class'=> 'mini'
	);
	// No category in URL
	$options[] = array(
		'name' => __('分类URL去除category字样','im'),
		'desc' => __('开启（主题已内置no-category插件功能，请不要安装插件；开启后请去设置-固定连接中点击保存即可）','im'),
		'id'   => 'no_cat_in_url',
		'type' => 'checkbox'
	);
	// Friend Links Section In the Bottom of Page
	$options[] = array(
		'name' => __('底部友情链接','im'),
		'desc' => __('开启底部友情链接','im'),
		'id'   => 'bottom_flinks',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Friend Links Section In the Bottom for home page
	$options[] = array(
		'desc' => __('只在首页开启','im'),
		'id'   => 'bottom_flinks_home',
		'std'  => '1',
		'type' => 'checkbox',
		'class'=> 'bottom_flinks_hidden'
	);
	// Friend Links From Which Link Categories
	$options[] = array(
		'name' => __('选择一个友链的链接分类','im'),
		'desc' => __('选择一个友链的链接分类','im'),
		'id'   => 'bottom_flinks_cat',
		'type' => 'select',
		'class'=> 'bottom_flinks_hidden',
		'options' => $options_linkcats
	);
	// Enable Baidu Inner Site Search
	$options[] = array(
		'name' => __('百度站内搜索','im'),
		'desc' => __('开启百度站内搜索','im'),
		'id'   => 'enable_baidu_inner',
		'type' => 'checkbox'
	);
	// Baidu Inner Site Seach Code
	$options[] = array(
		'desc' => __('此处存放百度自定义站内搜索代码（http://zn.baidu.com设置并获取）','im'),
		'id'   => 'baidu_search_code',
		'type' => 'textarea',
		'class'=> 'enable_baidu_inner_hidden',
		'settings' => array(
			'rows' => 5
		)
	);
	/**
	 * User Center Settings
	 */
	$options[] = array(
		'name' => __('会员中心','im'),
		'type' => 'heading'
	);
	// Enable User Center
	$options[] = array(
		'name' => __('开启会员中心','im'),
		'desc' => __('开启会员中心','im'),
		'id'   => 'enable_user_center',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Show User module in notice district of home page
	$options[] = array(
		'desc' => __('首页公告栏显示会员模块','im'),
		'id'   => 'user_on_notice_module',
		'std'  => '1',
		'type' => 'checkbox',
		'class'=> 'enable_user_center_hidden'
	);
	// User Center template
	$options[] = array(
		'name' => __('会员中心页面','im'),
		'desc' => __('会员中心页面','im'),
		'id'   => 'user_page',
		'type' => 'select',
		'class'=> 'enable_user_center_hidden',
		'options' => $options_pages
	);
	// User secure template
	$options[] = array(
		'name' => __('找回密码页面','im'),
		'desc' => __('找回密码页面','im'),
		'id'   => 'user_pw_page',
		'type' => 'select',
		'class'=> 'enable_user_center_hidden',
		'options' => $options_pages
	);
	// Allow User Publish Posts
	$options[] = array(
		'name' => __('允许用户发布文章','im'),
		'desc' => __('允许用户发布文章','im'),
		'id'   => 'allow_user_post',
		'std'  => '1',
		'type' => 'checkbox',
		'class'=> 'enable_user_center_hidden'
	);
	// Notifications of publishing
	$options[] = array(
		'name' => __('新投稿时邮件通知','im'),
		'desc' => __('新投稿时邮件通知','im'),
		'id'   => 'notification_of_user_post',
		'type' => 'checkbox',
		'class'=> 'enable_user_center_hidden'
	);
	// Notifications of publishing by E-mail
	$options[] = array(
		'name' => __('通知邮箱','im'),
		'desc' => __('通知邮箱','im'),
		'id'   => 'notification_by_email',
		'type' => 'text',
		'class'=> 'notification_of_user_post_hidden'
	);
	/**
	 * ADs District Settings
	 */
	$options[] = array(
		'name' => __('广告区','im'),
		'type' => 'heading'
	);
	// List Section of Home Page Top
	$options[] = array(
		'name' => __('首页文章列表板块上','im'),
		'desc' => __('首页文章列表板块上','im'),
		'id'   => 'ads_index_01',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_index_01_nm',
		'type' => 'textarea',
		'class'=> 'ads_index_01_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_index_01_m',
		'type' => 'textarea',
		'class'=> 'ads_index_01_hidden'
	);
	// List Section of Home Page Bottom
	$options[] = array(
		'name' => __('首页文章列表板块下','im'),
		'desc' => __('首页文章列表板块下','im'),
		'id'   => 'ads_index_02',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_index_02_nm',
		'type' => 'textarea',
		'class'=> 'ads_index_02_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_index_02_m',
		'type' => 'textarea',
		'class'=> 'ads_index_02_hidden'
	);
	// Content Section of Post Page Top
	$options[] = array(
		'name' => __('文章页正文上','im'),
		'desc' => __('文章页正文上','im'),
		'id'   => 'ads_post_01',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_post_01_nm',
		'type' => 'textarea',
		'class'=> 'ads_post_01_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_post_01_m',
		'type' => 'textarea',
		'class'=> 'ads_post_01_hidden'
	);
	// Words AD Right After Content Section of Post Page
	$options[] = array(
		'name' => __('文章页正文结尾文字广告','im'),
		'desc' => __('文章页正文结尾文字广告','im'),
		'id'   => 'ads_post_02',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('前缀','im'),
		'id'   => 'ads_post_02_prefix',
		'std'  => __('专业','im'),
		'type' => 'text',
		'class'=> 'ads_post_02_hidden'
	);
	$options[] = array(
		'desc' => __('标题','im'),
		'id'   => 'ads_post_02_title',
		'std'  => __('阿里巴巴','im'),
		'type' => 'text',
		'class'=> 'ads_post_02_hidden'
	);
	$options[] = array(
		'desc' => __('链接','im'),
		'id'   => 'ads_post_02_link',
		'std'  => 'http://www.webzgq.com',
		'type' => 'text',
		'class'=> 'ads_post_02_hidden'
	);
	$options[] = array(
		'desc' => __('是否新窗口打开','im'),
		'id'   => 'ads_post_02_link_blank',
		'std'  => '1',
		'type' => 'checkbox',
		'class'=> 'ads_post_02_hidden'
	);
	// Content Section of Post Page Bottom
	$options[] = array(
		'name' => __('文章页正文下','im'),
		'desc' => __('文章页正文下','im'),
		'id'   => 'ads_post_03',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_post_03_nm',
		'type' => 'textarea',
		'class'=> 'ads_post_03_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_post_03_m',
		'type' => 'textarea',
		'class'=> 'ads_post_03_hidden'
	);
	// Above Comments Section of Post Page
	$options[] = array(
		'name' => __('文章页评论上','im'),
		'desc' => __('文章页评论上','im'),
		'id'   => 'ads_post_04',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_post_04_nm',
		'type' => 'textarea',
		'class'=> 'ads_post_04_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_post_04_m',
		'type' => 'textarea',
		'class'=> 'ads_post_04_hidden'
	);
	// List Section of Category Page Top
	$options[] = array(
		'name' => __('分类页列表板块上','im'),
		'desc' => __('分类页列表板块上','im'),
		'id'   => 'ads_cat_01',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_cat_01_nm',
		'type' => 'textarea',
		'class'=> 'ads_cat_01_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_cat_01_m',
		'type' => 'textarea',
		'class'=> 'ads_cat_01_hidden'
	);
	// List Section of Category Page Bottom
	$options[] = array(
		'name' => __('分类页列表板块下','im'),
		'desc' => __('分类页列表板块下','im'),
		'id'   => 'ads_cat_02',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_cat_02_nm',
		'type' => 'textarea',
		'class'=> 'ads_cat_02_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_cat_02_m',
		'type' => 'textarea',
		'class'=> 'ads_cat_02_hidden'
	);
	// List Section of Tag Page Top
	$options[] = array(
		'name' => __('标签页列表板块上','im'),
		'desc' => __('标签页列表板块上','im'),
		'id'   => 'ads_tag_01',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_tag_01_nm',
		'type' => 'textarea',
		'class'=> 'ads_tag_01_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_tag_01_m',
		'type' => 'textarea',
		'class'=> 'ads_tag_01_hidden'
	);
	// List Section of Tag Page Bottom
	$options[] = array(
		'name' => __('标签页列表板块下','im'),
		'desc' => __('标签页列表板块下','im'),
		'id'   => 'ads_tag_02',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_tag_02_nm',
		'type' => 'textarea',
		'class'=> 'ads_tag_02_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_tag_02_m',
		'type' => 'textarea',
		'class'=> 'ads_tag_02_hidden'
	);
	// List Section of Search Results Page Top
	$options[] = array(
		'name' => __('搜索页列表板块上','im'),
		'desc' => __('搜索页列表板块上','im'),
		'id'   => 'ads_search_01',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_search_01_nm',
		'type' => 'textarea',
		'class'=> 'ads_search_01_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_search_01_m',
		'type' => 'textarea',
		'class'=> 'ads_search_01_hidden'
	);
	// List Section of Search Results Page Bottom
	$options[] = array(
		'name' => __('搜索页列表板块下','im'),
		'desc' => __('搜索页列表板块下','im'),
		'id'   => 'ads_search_02',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('非手机端','im').' '.$ads_desc,
		'id'   => 'ads_search_02_nm',
		'type' => 'textarea',
		'class'=> 'ads_search_02_hidden'
	);
	$options[] = array(
		'desc' => __('手机端','im').' '.$ads_desc,
		'id'   => 'ads_search_02_m',
		'type' => 'textarea',
		'class'=> 'ads_search_02_hidden'
	);
	/**
	 * Interaction Settings
	 */
	$options[] = array(
		'name' => __('互动设置','im'),
		'type' => 'heading'
	);
	// Gravatar Link
	$options[] = array(
		'name' => __('Gravatar头像获取','im'),
		'id'   => 'gravatar_url',
		'std'  => 'ssl',
		'type' => 'radio',
		'options' => array(
			'normal' => __('原有方式', 'im'),
			'ssl' => __('从Gravatar官方ssl获取', 'im'),
			'duoshuo' => __('从多说服务器获取', 'im')
		)
	);
	// Social WEIBO
	$options[] = array(
		'name' => __('微博','im'),
		'desc' => __('微博','im'),
		'id'   => 'weibo',
		'type' => 'text'
	);
	// Social TWITTER
	$options[] = array(
		'name' => __('Twitter','im'),
		'desc' => __('Twitter','im'),
		'id'   => 'twitter',
		'type' => 'text'
	);
	// Social FACEBOOK
	$options[] = array(
		'name' => __('Facebook','im'),
		'desc' => __('Facebook','im'),
		'id'   => 'facebook',
		'type' => 'text'
	);
	// Social QQ
	$options[] = array(
		'name' => __('QQ','im'),
		'desc' => __('QQ','im'),
		'id'   => 'qq',
		'type' => 'text'
	);
	// Social WECHAT
	$options[] = array(
		'name' => __('微信','im'),
		'desc' => __('微信','im'),
		'id'   => 'wechat',
		'std'  => 'IM',
		'type' => 'text'
	);
	$options[] = array(
		'desc' => __('二维码上传（200*200）','im'),
		'id'   => 'wechat_qr',
		'type' => 'upload'
	);
	// Social RSS
	$options[] = array(
		'name' => __('RSS订阅','im'),
		'desc' => __('RSS订阅','im'),
		'id'   => 'feed',
		'std'  => get_feed_link(),
		'type' => 'text'
	);
	// Comment Title
	$options[] = array(
		'name' => __('评论标题','im'),
		'desc' => __('评论标题','im'),
		'id'   => 'comment_title',
		'std'  => __('评论', 'im'),
		'type' => 'text'
	);
	// Comment Placeholder
	$options[] = array(
		'name' => __('评论框默认字符','im'),
		'desc' => __('评论框默认字符','im'),
		'id'   => 'comment_placehoder',
		'std'  => __('你说呀','im'),
		'type' => 'text'
	);
	// Comment Submit Text
	$options[] = array(
		'name' => __('评论提交按钮字符','im'),
		'desc' => __('评论提交按钮字符','im'),
		'id'   => 'comment_submit_text',
		'std'  => __('提交评论', 'im'),
		'type' => 'text'
	);
	// Comment reply notify mail with extra info
	$options[] = array(
		'name' => __('用户评论被回复时邮件内容附自定义信息（被回复都会邮件通知）','im'),
		'desc' => __('邮件附加信息1','im'),
		'id'   => 'mail_info1',
		'std'  => __('订阅'.get_bloginfo('name'),'im'),
		'type' => 'text'
	);
	$options[] = array(
		'desc' => __('邮件附加信息1链接','im'),
		'id'   => 'mail_info1_link',
		'std'  => get_feed_link(),
		'type' => 'text'
	);
	$options[] = array(
		'desc' => __('邮件附加信息2','im'),
		'id'   => 'mail_info2',
		'type' => 'text'
	);
	$options[] = array(
		'desc' => __('邮件附加信息2链接','im'),
		'id'   => 'mail_info2_link',
		'type' => 'text'
	);
	// Share Module
	$options[] = array(
		'name' => __('分享模块','im'),
		'desc' => __('分享模块','im'),
		'id'   => 'share_module',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Share Module Code
	$options[] = array(
		'name' => __('分享代码','im'),
		'desc' => __('默认是百度分享代码，可以改成其他分享代码','im'),
		'id'   => 'share_module_code',
		'std'  => '<div class="bdsharebuttonbox">
<span>分享到：</span>
<a class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
<a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
<a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
<a class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
<a class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
<a class="bds_bdhome" data-cmd="bdhome" title="分享到百度新首页"></a>
<a class="bds_tqf" data-cmd="tqf" title="分享到腾讯朋友"></a>
<a class="bds_youdao" data-cmd="youdao" title="分享到有道云笔记"></a>
<a class="bds_more" data-cmd="more">更多</a> <span>(</span><a class="bds_count" data-cmd="count" title="累计分享0次">0</a><span>)</span>
</div>
<script>
window._bd_share_config = {
    common: {
		"bdText"     : "",
		"bdMini"     : "2",
		"bdMiniList" : false,
		"bdPic"      : "",
		"bdStyle"    : "0",
		"bdSize"     : "24"
    },
    share: [{
        bdCustomStyle: "'. get_stylesheet_directory_uri() .'/css/share.css"
    }]
}
with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion="+~(-new Date()/36e5)];
</script>',
		'type' => 'textarea',
		'class'=> 'share_module_hidden',
		'settings' => array(
			'rows' => 10
		)
	);
	/**
	 * Performance Settings
	 */
	$options[] = array(
		'name' => __('性能选项','im'),
		'type' => 'heading'
	);
	// Place jQuery in the bottom of site
	$options[] = array(
		'name' => __('jQuery底部加载','im'),
		'desc' => __('开启（可提升页面内容加载速度，但部分依赖jQuery的插件可能失效）','im'),
		'id'   => 'jquery_bottom',
		'type' => 'checkbox'
	);
	// Js CDN
	$options[] = array(
		'name' => __('JS文件托管','im'),
		'desc' => __('JS文件是否托管','im'),
		'id'   => 'js_cdn',
		'std'  => 'localhost',
		'type' => 'radio',
		'options' => array(
			'localhost' => __('不托管', 'im'),
			'baidu' => __('百度', 'im'),
			'offi_site' => __('框架来源站点（分别引入jquery和bootstrap官方站点JS文件）', 'im')
		)
	);
	// Thumbnails loading by ajax 
	$options[] = array(
		'name' => __('文章缩略图异步加载','im'),
		'desc' => __('文章缩略图异步加载','im'),
		'id'   => 'ajax_thumbnail',
		'std'  => '1',
		'type' => 'checkbox'
	);
	/**
	 * Functionalities Settings
	 */
	$options[] = array(
		'name' => __('功能设置','im'),
		'type' => 'heading'
	);
	// Micro Categories
	$options[] = array(
		'name' => __('微分类','im'),
		'desc' => __('开启微分类','im'),
		'id'   => 'micro_cat',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Show posts from Micro Categories in home page
	$options[] = array(
		'name' => __('在首页显示微分类最新文章','im'),
		'desc' => __('在首页显示微分类最新文章','im'),
		'id'   => 'micro_cat_home',
		'std'  => '1',
		'type' => 'checkbox',
		'class'=> 'micro_cat_hidden'
	);
	// Micro Categories Title
	$options[] = array(
		'name' => __('标题（默认：今日观点）','im'),
		'desc' => __('标题（默认：今日观点）','im'),
		'id'   => 'micro_cat_home_title',
		'std'  => __('今日观点','im'),
		'type' => 'text',
		'class'=> 'micro_cat_hidden'
	);
	// Choose a category for micro category
	$options[] = array(
		'name' => __('选择分类设置为微分类','im'),
		'desc' => __('选择一个使用微分类展示模版，分类下文章将全文输出到微分类列表','im'),
		'id'   => 'micro_cat_from',
		'type' => 'select',
		'class'=> 'micro_cat_hidden',
		'options' => $options_categories
	);
	// Sidebars scroll as Window scrolls for home page
	$options[] = array(
		'name' => __('侧栏随动','im'),
		'desc' => __('首页','im'),
		'id'   => 'sidebar_scroll_index',
		'std'  => '1',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块','im'),
		'id'   => 'sidebar_scroll_index_set',
		'std'  => '1 2',
		'type' => 'text',
		'class'=> 'mini sidebar_scroll_index_hidden'
	);
	// Sidebars scroll as Window scrolls for cat&tag&search pages
	$options[] = array(
		'desc' => __('分类/标签/搜索页','im'),
		'id'   => 'sidebar_scroll_list',
		'std'  => '1',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块','im'),
		'id'   => 'sidebar_scroll_list_set',
		'std'  => '1 2',
		'type' => 'text',
		'class'=> 'mini sidebar_scroll_list_hidden'
	);
	// Sidebars scroll as Window scrolls for post page
	$options[] = array(
		'desc' => __('文章页','im'),
		'id'   => 'sidebar_scroll_post',
		'std'  => '1',
		'type' => 'checkbox'
	);
	$options[] = array(
		'desc' => __('设置随动模块，多个模块之间用空格隔开即可！默认：“1 2”，表示第1和第2个模块，建议最多3个模块','im'),
		'id'   => 'sidebar_scroll_post_set',
		'std'  => '1 2',
		'type' => 'text',
		'class'=> 'mini sidebar_scroll_post_hidden'
	);
	// Direct Link
	$options[] = array(
		'name' => __('直达链接','im'),
		'desc' => __('显示','im'),
		'id'   => 'direct_link',
		'type' => 'multicheck',
		'options' => array(
			'list_article' => __('在文章列表页显示','im'),
			'article_article' => __('在文章页显示','im')
		)
	);
	// Direct Link Open Type
	$options[] = array(
		'desc' => __('新窗口打开','im'),
		'id'   => 'direct_link_open_type',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Direct Link with arrtribute of nofollow
	$options[] = array(
		'desc' => __('链接添加nofollow','im'),
		'id'   => 'direct_link_nofollow',
		'std'  => '1',
		'type' => 'checkbox'
	);
	// Direct Link Title
	$options[] = array(
		'desc' => __('自定义显示文字，默认为：直达链接','im'),
		'id'   => 'direct_link_title',
		'std'  => __('直达链接','im'),
		'type' => 'text'
	);
	/**
	 * Customizitons Settings
	 */
	$options[] = array(
		'name' => __('自定义','im'),
		'type' => 'heading'
	);
	// Custom your CSS code
	$options[] = array(
		'name' => __('自定义CSS样式','im'),
		'desc' => __('位于&lt;/head&gt;之前，直接写样式代码，不用添加&lt;style&gt;标签','im'),
		'id'   => 'cus_csscode',
		'type' => 'textarea'
	);
	// Custom codes before </head>
	$options[] = array(
		'name' => __('自定义头部代码','im'),
		'desc' => __('位于&lt;/head&gt;之前，这部分代码是在主要内容显示之前加载，通常是CSS样式、自定义的&lt;meta&gt;标签、全站头部JS等需要提前加载的代码','im'),
		'id'   => 'head_code',
		'type' => 'textarea'
	);
	// Custom codes before </body>
	$options[] = array(
		'name' => __('自定义底部代码','im'),
		'desc' => __('位于&lt;/body&gt;之前，这部分代码是在主要内容加载完毕加载，通常是JS代码','im'),
		'id'   => 'foot_code',
		'type' => 'textarea'
	);
	// Custom the content above copyright section in the foot of the site
	$options[] = array(
		'name' => __('自定义网站底部内容','im'),
		'desc' => __('该块显示在网站底部版权上方，可已定义放一些链接或者图片之类的内容。','im'),
		'id'   => 'cus_foot_content',
		'type' => 'textarea'
	);
	// Custom your analytic and on
	$options[] = array(
		'name' => __('网站统计代码','im'),
		'desc' => __('位于底部，用于添加第三方流量数据统计代码，如：Google analytics、百度统计、CNZZ、51la，国内站点推荐使用百度统计，国外站点推荐使用Google analytics','im'),
		'id'   => 'trackcode',
		'type' => 'textarea'
	);
	return $options;
}
