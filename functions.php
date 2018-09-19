<?php
//Require theme functions 
require get_stylesheet_directory().'/settings/functions-theme.php';
#####################*Posts List For Home Page Only*#####################
function archives_for_homepage($query){
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $ignore_articles_cat = '';
    $ignore_posts = array();
    if( im('ignore_articles_cat') ){
        $pool = array();
        foreach (im('ignore_articles_cat') as $key => $value) {
            if( $value ) $pool[] = $key;
        }
        if(!empty($pool)){
            $ignore_articles_cat = '-'.implode(',-',$pool);
        }
    }
    if( im('ignore_posts') ){
        $pool = im('ignore_posts');
        $ignore_posts = explode("\n", $pool);
    }
    if ( $query->is_home() && $query->is_main_query()) {    
        $query->set('ignore_sticky_posts',1);
        $query->set('paged',$paged);
        $query->set('cat',$ignore_articles_cat);
        $query->set('post__not_in',$ignore_posts);
    }
}
add_action('pre_get_posts','archives_for_homepage');
#####################* Archive *#####################
function archives_list() {
    if( !$output = get_option('db_cache_archives_list') ){
        $output = '';
        // tag
        $tag_list = get_tags('orderby=count&order=DESC');
        if(!empty($tag_list)){
            $output .= '<div class="archives-item"><h2 class="archives-title">标签归档</h2><ul>';
            foreach ($tag_list as $tag) {
                $output .= '<li><a class="name" href="'.get_tag_link($tag).'">'. $tag->name .'</a><em>('. $tag->count .')</em>'; 
            }
            $output .= '</ul></div>';
        }
        // categories
        $cat_list = get_categories('orderby=count&order=DESC');
        if(!empty($cat_list)){
            $output .= '<div class="archives-item"><h2 class="archives-title">分类归档</h2><ul>';
            foreach ($cat_list as $cat) {
                $output .= '<li><a class="name" href="'.get_category_link($cat).'">'. $cat->name .'</a><em>('. $cat->count .')</em>'; 
            }
            $output .= '</ul></div>';
        }
        // posts & pages
        $exclude_posts_arg = array();
        $exclude_posts = im('archive_exlude_posts');
        if($exclude_posts){
            $exclude_posts_arg = explode(',', $exclude_posts);
        }
        $args = array(
            'post_type' => array('post','page'), 
            'posts_per_page' => -1, 
            'ignore_sticky_posts' => 1, 
            'post__not_in' => $exclude_posts_arg
        );
        $the_query = new WP_Query( $args );
        if(!empty($the_query)){
            $output .= '<div id="archives"><h2 class="archives-title">文章归档</h2><p><span id="al_expand_collapse">全部展开/收缩</span> <em>(注: 点击月份可以展开)</em></p>';
            $posts_rebuild = array();
            $year = $mon = 0;$j = 0;
            while ( $the_query->have_posts() ) : $the_query->the_post();
                $post_year = get_the_time('Y');
                $post_mon = get_the_time('m');
                $post_day = get_the_time('d');
                if ($year != $post_year) $year = $post_year;
                if ($mon != $post_mon) $mon = $post_mon;
                $comments_count = get_comments_number('0','1','%');
                $post_views = (int)get_post_meta(get_the_ID(),'views',true);
                $post_info = '';
                if($comments_count!='0'||$post_views){
                    $post_info .= '<em>(';
                    if($comments_count!='0')
                        $post_info .= $comments_count.'条评论';
                    if($post_views){
                        if($comments_count!='0'){
                            $post_info .= '、'.$post_views.'次浏览';
                        }else{
                            $post_info .= $post_views.'次浏览';
                        }
                    }
                    $post_info .= ')</em>';
                }
                $posts_rebuild[$year][$mon][] = '<li>'. get_the_time('d日: ') .'<a href="'. get_permalink() .'">'. get_the_title() .'</a>'.$post_info.'</li>';
            endwhile;
            wp_reset_postdata();
            foreach ($posts_rebuild as $key_y => $y) {
                $output .= '<h3 class="al_year">'. $key_y .' 年(count篇文章)</h3><ul class="al_mon_list">'; //输出年份
                foreach ($y as $key_m => $m) {
                    $posts = ''; $i = 0;
                    foreach ($m as $p) {
                        ++$i;
                        ++$j;
                        $posts .= $p;
                    }
                    $output .= '<li><span class="al_mon">'. $key_m .' 月 <em> ( '. $i .' 篇文章 )</em></span><ul class="al_post_list">'; //输出月份
                    $output .= $posts; //输出 posts
                    $output .= '</ul></li>';
                }
                $output .= '</ul>';
                $output = str_replace('count', $j, $output);
            }
            $output .= '</div>';
        }
        update_option('db_cache_archives_list', $output);//做缓存，减少数据库查询
    }
    echo $output;
}
function clear_db_cache_archives_list() {
    update_option('db_cache_archives_list', ''); 
}
add_action('save_post', 'clear_db_cache_archives_list'); // 新发表文章/修改文章时更新

# JS For Archive Page
# 如文章不多，可把代码里面 2 个 (s-10<1)?0:s-10 改为 s ，效果会好看点。
add_action( 'my_inline_script', 'my_inline_script' );
function my_inline_script() { ?>
<script type="text/javascript">
(function ($, window) {
    $(function() {
        var $a = $('#archives'),
            $m = $('.al_mon', $a),
            $l = $('.al_post_list', $a),
            $l_f = $('.al_post_list:first', $a);
        $l.hide();
        $l_f.show();
        $m.css('cursor', 's-resize').on('click', function(){
            $(this).next().slideToggle(400);
        });
        var animate = function(index, status, s) {
            if (index > $l.length) {
                return;
            }
            if (status == 'up') {
                $l.eq(index).slideUp(s, function() {
                    animate(index+1, status, (s-10<1)?0:s-10);
                });
            } else {
                $l.eq(index).slideDown(s, function() {
                    animate(index+1, status, (s-10<1)?0:s-10);
                });
            }
        };
        $('#al_expand_collapse').on('click', function(e){
            //e.preventDefault();因原来是a标签
            if ( $(this).data('s') ) {
                $(this).data('s', '');
                animate(0, 'up', 100);
            } else {
                $(this).data('s', 1);
                animate(0, 'down', 100);
            }
        });
    });
})(jQuery, window);
</script>
<?php
}
function load_archive_list_js(){
    if(is_page_template('pages/archives-new.php')){
        do_action('my_inline_script');
    }
}
add_action( 'wp_footer','load_archive_list_js' );
#####################* *#####################