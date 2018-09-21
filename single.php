<?php get_header(); ?>
<section class="container">
	<div class="content-wrap">
	<div class="content">
		<?php while (have_posts()) : the_post(); ?>
		<header class="article-header">
			<h1 class="article-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?><?php echo get_the_subtitle() ?></a></h1>
			<div class="article-meta">
				<span class="item"><?php echo get_the_time('Y-m-d'); ?></span>
				<?php _moloader('mo_get_post_from', false); ?>
				<?php if( mo_get_post_from() ){ ?><span class="item"><?php echo mo_get_post_from(); ?></span><?php } ?>
				<span class="item"><?php echo '分类：';the_category(' / '); ?></span>
				<?php if( im('post_widgets')['views'] ){ ?><span class="item post-views"><?php echo _get_post_views() ?></span><?php } ?>
				<span class="item"><?php echo _get_post_comments() ?></span>
				<span class="item"><?php edit_post_link('[编辑]'); ?></span>
			</div>
		</header>
		<article class="article-content">
			<?php _the_ads($name='ads_post_01', $class='asb-post asb-post-01') ?>
			<?php the_content(); ?>
		</article>
		<?php wp_link_pages('link_before=<span>&link_after=</span>&before=<div class="article-paging">&after=</div>&next_or_number=number'); ?>
		<?php if (im('ads_post_02')) {
			echo '<div class="asb-post-footer"><b>AD：</b><strong>【' . im('ads_post_02_prefix') . '】</strong><a'.(im('ads_post_02_link_blank')?' target="_blank"':'').' href="' . im('ads_post_02_link') . '">' . im('ads_post_02_title') . '</a></div>';
		} ?>
		<?php  
			if( im('direct_link')['article_article'] || im('post_widgets')['like'] || im('reward_enable') ){
				echo '<div class="post-actions">';
					if( im('post_widgets')['like'] ) _moloader('mo_like');
					if( im('reward_enable') ) _moloader('mo_reward');
					if( im('direct_link')['article_article'] ) _moloader('mo_post_link');
				echo '</div>';
			}
		?>
		<?php if( im('post_copyright') ){
			echo '<div class="post-copyright">' . im('post_copyright_prefix') . '<a href="' . get_bloginfo('url') . '">' . get_bloginfo('name') . '</a> &raquo; <a href="' . get_permalink() . '">' . get_the_title() . '</a></div>';
		} ?>
		<?php endwhile; ?>

		<?php if( !wp_is_mobile() || (!im('hide_share_distric') && wp_is_mobile()) ){ ?>
			<div class="action-share"><?php _moloader('mo_share'); ?></div>
		<?php } ?>

		<div class="article-tags"><?php the_tags('标签：','',''); ?></div>

		<?php if( im('post_author_info') ){ ?>
		<div class="article-author">
			<?php echo _get_the_avatar(get_the_author_meta('ID'), get_the_author_meta('email')); ?>
			<h4><i class="fa fa-user" aria-hidden="true"></i><a title="查看更多文章" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author_meta('nickname'); ?></a></h4>
			<?php echo get_the_author_meta('description'); ?>
		</div>
		<?php } ?>

		<?php if( im('post_prevnext') ){ ?>
            <nav class="article-nav">
                <span class="article-nav-prev"><?php previous_post_link('上一篇<br>%link'); ?></span>
                <span class="article-nav-next"><?php next_post_link('下一篇<br>%link'); ?></span>
            </nav>
        <?php } ?>

		<?php _the_ads($name='ads_post_03', $class='asb-post asb-post-02') ?>
		<?php 
			if( im('post_related_section') ){
				_moloader('mo_posts_related', false); 
				mo_posts_related(im('post_related_section_title'), im('post_related_num'));
			}
		?>
		<?php _the_ads($name='ads_post_04', $class='asb-post asb-post-03') ?>
		<?php comments_template('', true); ?>
	</div>
	</div>
	<?php 
		if( has_post_format( 'aside' )){

		}else{
			get_sidebar();
		} 
	?>
</section>
<?php get_footer(); 