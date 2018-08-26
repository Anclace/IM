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
            <div class="archives-meta">
                <?php
                    $post_count    = wp_count_posts()->publish;
                    $page_count    = wp_count_posts('page')->publish;
                    $article_totle = $post_count+$page_count;
                    $cat_count     = wp_count_terms('category');
                    $tag_count     = wp_count_terms('post_tag');
                    $comment_count = wp_count_comments()->total_comments;
                    global $wpdb;
                    $last = $wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')");
                    $last = date('Y-m-d', strtotime($last[0]->MAX_m));
                    printf("站点统计：%s 篇文章、%s 个分类、%s 个标签、%s 条评论、最后更新：%s",$article_totle,$cat_count,$tag_count,$comment_count,$last)
                ?>
            </div>
		</article>
		<?php endwhile;  ?>
        <?php archives_list();?>
		<?php //comments_template('', true); ?>
	</div>
</div>
<?php
get_footer();