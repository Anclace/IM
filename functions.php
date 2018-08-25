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
/**
 * [archives_list by zwwooooo | http://zww.me]
 * @return [type] [description]
 * 源代码是使用jQuery在前端进行文章计数，现改为后台计数
 */
 function archives_list() {
     if( !$output = get_option('archives_list') ){
         $output = '<div id="archives"><p>[<a id="al_expand_collapse" href="javascript:void(0)">全部展开/收缩</a>] <em>(注: 点击月份可以展开)</em></p>';
         $args = array(
            //'post_type' => 'post', 
            'posts_per_page' => -1, 
            'ignore_sticky_posts' => 1 
         );
         $the_query = new WP_Query( $args ); 
         $year=0; $mon=0;$i=0;$j=0;
         while ( $the_query->have_posts() ) : $the_query->the_post();
             $year_tmp = get_the_time('Y');
             $mon_tmp = get_the_time('m');
             if ($mon != $mon_tmp && $mon > 0) {
                $output .= '</ul></li>';
                $output = str_replace('countmonth', $i, $output);
                $i = 0;
             }
             if ($year != $year_tmp && $year > 0) {
                $output .= '</ul>';
                $output = str_replace('countyear', $j, $output);
                $j = 0;
             }
             if ($year != $year_tmp) {
                 $year = $year_tmp;
                 $output .= '<h3 class="al_year">'. $year .' 年（countyear篇文章）</h3><ul class="al_mon_list">'; //输出年份
             }
             if ($mon != $mon_tmp) {
                 $mon = $mon_tmp;
                 $output .= '<li><span class="al_mon">'. $mon .' 月（countmonth篇文章）</span><ul class="al_post_list">'; //输出月份
             }
             $output .= '<li>'. get_the_time('d日: ') .'<a href="'. get_permalink() .'">'. get_the_title() .'</a> <em>('. get_comments_number_text('无', '1条', '%条') .'评论)</em></li>'; //输出文章日期和标题
             $i++;$j++;
         endwhile;
         wp_reset_postdata();
         $output .= '</ul></li></ul></div>';
         $output = str_replace('countmonth', $i, $output);
         $output = str_replace('countyear', $j, $output);
         update_option('archives_list', $output);//做缓存，减少数据库查询
     }
     echo $output;
 }
 function clear_arc_cache() {
     update_option('archives_list', '');
 }
 add_action('save_post', 'clear_arc_cache'); // 新发表文章/修改文章时更新
# JS For Archive Page
add_action( 'my_inline_script', 'my_inline_script' );
function my_inline_script() { ?>
<script type="text/javascript">
jQuery(document).ready(function($){
    //存档页面 jQ伸缩
     (function(){
         $('#al_expand_collapse,#archives span.al_mon').css({cursor:"s-resize"});
         $('#archives span.al_mon').each(function(){
             var num=$(this).next().children('li').size();
             var text=$(this).text();
             $(this).html(text+'<em> ( '+num+' 篇文章 )</em>');
         });
         var $al_post_list=$('#archives ul.al_post_list'),
             $al_post_list_f=$('#archives ul.al_post_list:first');
         $al_post_list.hide(1,function(){
             $al_post_list_f.show();
         });
         $('#archives span.al_mon').click(function(){
             $(this).next().slideToggle(400);
             return false;
         });
         $('#al_expand_collapse').click(function(){
            if(open){
                $al_post_list.show();
                open = false;
            }else{
                $al_post_list.hide();
                open = true;
            }
         });
     })();
 });
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