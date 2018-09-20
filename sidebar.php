<?php if( im('sidebar_type') == 'site-layout-1' ) return; ?>
<aside class="sidebar">
<?php 
	_moloader('mo_notice', false);
	if (function_exists('dynamic_sidebar')){
		dynamic_sidebar('public_header'); 
		if (is_home()){
			dynamic_sidebar('home'); 
		}
		elseif (is_category()){
			dynamic_sidebar('cat'); 
		}
		else if (is_tag() ){
			dynamic_sidebar('tag'); 
		}
		else if (is_search()){
			dynamic_sidebar('search'); 
		}
		else if (is_single()){
			dynamic_sidebar('single'); 
		}
		dynamic_sidebar('public_footer');
	} 
?>
</aside>