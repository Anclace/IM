<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="dns-prefetch" href="//apps.bdimg.com">
<link rel="dns-prefetch" href="//static.webzgq.com">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-title" content="<?php echo get_bloginfo('name') ?>">
<meta http-equiv="Cache-Control" content="no-siteapp"><!-- 禁止百度转码 -->
<title><?php echo _title(); ?></title>
<?php wp_head(); ?>
<link rel="shortcut icon" href="<?php echo home_url() . '/favicon.ico' ?>">
<!--[if lt IE 9]><script src="<?php echo get_stylesheet_directory_uri() ?>/js/libs/html5.min.js"></script><![endif]-->
</head>
<body <?php body_class(_bodyclass()); ?>>
<header class="header">
	<div class="container">
		<?php _the_logo(); ?>
		<?php  
			$_brand = im('brand');
			if( $_brand ){
				$_brand = explode("\n", $_brand);
				echo '<div class="brand">' . $_brand[0] . '<br>' . $_brand[1] . '</div>';
			}
		?>
		<ul class="site-nav site-navbar">
			<?php _the_menu('nav') ?>
			<?php if( !is_search() ){ ?>
				<li class="navto-search"><a href="javascript:;" class="search-show active"><i class="fa fa-search"></i></a></li>
			<?php } ?>
		</ul>
		<div class="topbar">
			<ul class="site-nav topmenu">
				<?php _the_menu('topmenu') ?>
				<?php if( im('wechat') || im('weibo') || im('qq') || im('twitter') || im('facebook') || im('feed') ){ ?>
				<li class="menusns">
					<a href="javascript:;">关注本站 <i class="fa fa-angle-down"></i></a>
					<ul class="sub-menu">
						<?php if(im('wechat')){ echo '<li><a class="sns-wechat" href="javascript:;" title="'.__('关注', 'im').im('wechat').'" data-src="'.im('wechat_qr').'"><i class="fa fa-wechat"></i> 微信</a></li>'; } ?>
						<?php if(im('weibo')){ echo '<li><a target="_blank" rel="external nofollow" href="'.im('weibo').'"><i class="fa fa-weibo"></i> 微博</a></li>'; } ?>
						<?php if(im('qq')){ echo '<li><a target="_blank" rel="external nofollow" href="'.im('qq').'"><i class="fa fa-tencent-weibo"></i> QQ</a></li>'; } ?>
						<?php if(im('twitter')){ echo '<li><a target="_blank" rel="external nofollow" href="'.im('twitter').'"><i class="fa fa-twitter"></i> Twitter</a></li>'; } ?>
						<?php if(im('facebook')){ echo '<li><a target="_blank" rel="external nofollow" href="'.im('facebook').'"><i class="fa fa-facebook"></i> Facebook</a></li>'; } ?>
						<?php if(im('feed')){ echo '<li><a target="_blank" href="'.im('feed').'"><i class="fa fa-rss"></i> RSS订阅</a></li>'; } ?>
					</ul>
				</li>
				<?php } ?>
			</ul>
			<?php if( is_user_logged_in() ): global $current_user; ?>
				<?php _moloader('mo_get_user_page', false) ?>
				Hi, <?php echo $current_user->display_name ?>
				<?php if( im('enable_user_center') ){ ?>
					&nbsp; &nbsp; <a href="<?php echo mo_get_user_page() ?>">进入会员中心</a>
				<?php } ?>
				<?php if( is_super_admin() ){ ?>
					&nbsp; &nbsp; <a target="_blank" href="<?php echo site_url('/wp-admin/') ?>">后台管理</a>
				<?php } ?>
			<?php elseif( im('enable_user_center') ): ?>
				<?php _moloader('mo_get_user_rp', false) ?>
				<a href="javascript:;" class="signin-loader">Hi, 请登录</a>
				&nbsp; &nbsp; <a href="javascript:;" class="signup-loader">我要注册</a>
				&nbsp; &nbsp; <a href="<?php echo mo_get_user_rp() ?>">找回密码</a>
			<?php endif; ?>
		</div>
		<i class="fa fa-bars m-icon-nav"></i>
	</div>
</header>
<div class="site-search">
	<div class="container">
		<?php  
			if( im('enable_baidu_inner') && im('baidu_search_code') ){
				echo '<form class="site-search-form"><input id="bdcsMain" class="search-input" type="text" placeholder="输入关键字"><button class="search-btn" type="submit"><i class="fa fa-search"></i></button></form>';
				echo im('baidu_search_code');
			}else{
				echo '<form method="get" class="site-search-form" action="'.esc_url( home_url( '/' ) ).'" ><input class="search-input" name="s" type="text" placeholder="输入关键字" value="'.htmlspecialchars($s).'"><button class="search-btn" type="submit"><i class="fa fa-search"></i></button></form>';
			}
		?>
	</div>
</div>
<?php
// Breadcrumb nav(Exclude templates from PAGE directory and CATEGORY minicat)
_moloader('mo_is_minicat',false);
if(preg_match('#/pages/#i', get_page_template())){

}elseif (is_category()&&mo_is_minicat()) {

}else{
	_moloader('mo_breadcrumb');
}
?>