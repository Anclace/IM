<?php
get_header();
global $wp_query;
$curauth = $wp_query->get_queried_object();
$description = get_user_meta($curauth->ID,'description',true);
?>
<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php
		echo '
			<div class="authorleader">',
				 _get_the_avatar($user_id=$curauth->ID, $user_email=$curauth->user_email),
				'<h1>'.$curauth->display_name.'的文章</h1>',
				'<div class="authorleader-desc">'.$description.'</div>',
			'</div>';
		get_template_part( 'excerpt' );
		?>
	</div>
	</div>
	<?php get_sidebar() ?>
</section>
<?php get_footer(); ?>