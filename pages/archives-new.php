<?php 
/**
 * Template name: 新版归档页
 * Description:   不需要在前端用jQuery来计数了
 */
get_header();
?>
<div class="container container-page">
	<?php _moloader('mo_pagemenu', false) ?>
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
		</header>
		<article class="article-content">
			<?php the_content(); ?>
		</article>
		<?php endwhile;  ?>
        <?php archives_list();?>
		<?php //comments_template('', true); ?>
	</div>
</div>
<?php
get_footer();