<?php get_header(); ?>
<div class="container image-container">
<?php while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'image-attachment' ); ?>>
		<header class="image-header">
			<h1 class="image-title"><?php the_title(); ?></h1>
			<footer class="image-meta">
				<?php $metadata = wp_get_attachment_metadata();?>
				<?php echo get_the_date().__('发布于：','im'); ?>
				<a href="<?php echo get_permalink( $post->post_parent ) ?>"><?php echo get_the_title( $post->post_parent ) ?></a> &nbsp; 
				<a target="_blank" href="<?php echo wp_get_attachment_url() ?>">原图(<?php echo $metadata['width'].'&times;'.$metadata['height'] ?>)</a> &nbsp; 
				<?php edit_post_link( '[编辑]', '<span class="image-edit-link">', '</span>' ); ?>
			</footer>
		</header>
		<div class="image-content">
			<?php
			$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
			foreach ( $attachments as $k => $attachment ) :
				if ( $attachment->ID == $post->ID )
					break;
			endforeach;
			if ( count( $attachments ) > 1 ) :
				$k++;
				if ( isset( $attachments[ $k ] ) ) :
					$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
				else :
					$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
				endif;
			else :
				$next_attachment_url = wp_get_attachment_url();
			endif;
			?>
			<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php echo wp_get_attachment_image( $post->ID, false ); ?></a>
			<?php if ( ! empty( $post->post_excerpt ) ) : ?>
				<div class="image-caption">
					<?php the_excerpt(); ?>
				</div>
			<?php endif; ?>
			<div class="image-description">
				<?php 
					the_content(); 
					// 分页,会有问题,暂不启用
					//wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) );
				?>
			</div>
			<nav class="image-navigation" role="navigation">
				<?php previous_image_link( false, '上一张' ); ?>
				<?php next_image_link( false, '下一张' ); ?>
			</nav>
		</div>
	</article>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>