<?php
/**
 * Used for index/archive/search/author/catgory/tag.
 *
 */
$ii = 0;
while ( have_posts() ) : the_post(); 
    $_thumb = _get_post_thumbnail();
    $_excerpt_text = '';
    if( im('list_type')=='text' || (im('list_type') == 'auto' && strstr($_thumb, 'data-thumb="default"')) ){
        $_excerpt_text = ' excerpt-text';
    }
    $ii++;
    echo '<article class="excerpt excerpt-'.$ii. $_excerpt_text .'">';
        if( im('list_type') == 'thumb' ){
            echo '<a'._post_target_blank().' class="focus" href="'.get_permalink().'">'.$_thumb.'</a>';
        }else if( im('list_type') == 'auto' && !strstr($_thumb, 'data-thumb="default"') ){
            echo '<a'._post_target_blank().' class="focus" href="'.get_permalink().'">'.$_thumb.'</a>';
        }
        echo '<header>';
            if( im('post_widgets')['catlink'] && !is_category() ) {
                $category = get_the_category();
                if(! empty($category)){
                    echo '<a class="cat" href="'.get_category_link($category[0]->term_id ).'">'.$category[0]->cat_name.'<i></i></a> ';
                }
            };
            echo '<h2><a'._post_target_blank().' href="'.get_permalink().'" title="'.get_the_title().get_the_subtitle(false)._get_delimiter().get_bloginfo('name').'">'.get_the_title().get_the_subtitle().'</a></h2>';
        echo '</header>';
        echo '<p class="meta">';
        if( im('post_widgets')['pubdate'] ){
            echo '<time><i class="fa fa-clock-o"></i>'.get_the_time('Y-m-d').'</time>';
        }
        if( im('post_widgets')['authors'] ){
            $author = get_the_author();
            if( im('author_link') ){
                $author = '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.$author.'</a>';
            }
            echo '<span class="author"><i class="fa fa-user"></i>'.$author.'</span>';
        }
        if( im('post_widgets')['views'] ){
            echo '<span class="pv"><i class="fa fa-eye"></i>'._get_post_views().'</span>';
        }
        if ( comments_open() && im('post_widgets')['comments'] ) {
            echo '<a class="pc" href="'.get_comments_link().'"><i class="fa fa-comments-o"></i>'._get_post_comments().'</a>';
        }
        if ( im('post_widgets')['like'] ) _moloader('mo_like');
        echo '</p>';
        echo '<p class="note">'._get_excerpt().'</p>';
        if( im('direct_link')['list_article'] ) _moloader('mo_post_link');
    echo '</article>';
endwhile; 
_moloader('mo_paging');
wp_reset_query();