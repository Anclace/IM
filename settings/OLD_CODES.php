<?php
#####################*Add meta to terms of taxonomy*#####################
/**
 * 添加taxonomy(如category、post_tag) meta
 * 此方法将数据存储在wp_options表中，一行包含所有所添加的数据
 * 缺点：因为数据存储在wp_options表中，如果用WP_Term_Query查询时会比较麻烦
 */
class __Tax_cat_meta{
	function __construct(){
		add_action('category_add_form_fields',array($this,'add_tax_field'));
		add_action('category_edit_form_fields',array($this,'edit_tax_field'));
		add_action('edited_category',array($this,'save_tax_meta'),10,2);
		add_action('created_category',array($this,'save_tax_meta'),10,2);
	}
	public function add_tax_field(){
		echo '
			<div class="form-field">
                <label for="term_meta[title]">SEO 标题</label>
                <input type="text" name="term_meta[title]" id="term_meta[title]" value="" size="40">
            </div>
            <div class="form-field">
                <label for="term_meta[keywords]">SEO 关键字</label>
                <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" value="" size="40">
            </div>
            <div class="form-field">
                <label for="term_meta[keywords]">SEO 描述</label>
                <textarea name="term_meta[description]" id="term_meta[description]" rows="4" cols="40"></textarea>
            </div>			
		';
	}
	public function edit_tax_field($term){
		$term_id = $term->term_id;
		$term_meta = get_option("_taxonomy_meta_$term_id");
		$meta_title = isset($term_meta['title'])?$term_meta['title']:'';
		$meta_keywords = isset($term_meta['keywords'])?$term_meta['keywords']:'';
		$meta_description = isset($term_meta['description'])?$term_meta['description']:'';
		echo '
			<tr class="form-field">
				<th scope="row">
					<label for="term_meta[title]">SEO 标题</label>
				</th>
				<td>
					<input type="text" name="term_meta[title]" id="term_meta[title]" value="'. $meta_title .'" size="40">
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="term_meta[keywords]">SEO 关键字</label>
				</th>
				<td>
					<input type="text" name="term_meta[keywords]" id="term_meta[keywords]" value="'. $meta_keywords .'" size="40">
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row">
					<label for="term_meta[description]">SEO 描述</label>
				</th>
				<td>
					<textarea name="term_meta[description]" id="term_meta[description]" rows="4" cols="40">'. $meta_description .'</textarea>
				</td>
			</tr>
		';
	}
	public function save_tax_meta($term_id){
		if(isset($_POST['term_meta'])){
			if(!current_user_can('manage_categories')){
				return $term_id;
			}
			$term_meta = array();
			$term_meta['title'] = isset($_POST['term_meta']['title'])?esc_sql($_POST['term_meta']['title']):'';
			$term_meta['keywords'] = isset($_POST['term_meta']['keywords'])?esc_sql($_POST['term_meta']['keywords']):'';
			$term_meta['description'] = isset($_POST['term_meta']['description'])?esc_sql($_POST['term_meta']['description']):'';
			update_option("_taxonomy_meta_$term_id",$term_meta);
		}
	}
}
$tax_cat_meta = new __Tax_cat_meta();
// 调用meta字段方法
function _get_tax_meta ($id=0,$field=''){
	$ops = get_option('_taxonomy_meta_$id');
	if(empty($ops)){
		return '';
	}
	if(empty($field)){
		return $ops;
	}
	return isset($ops[$field])?$ops[$field]:'';
}
#####################* Archive *#####################
/**
 * [archives_list by zwwooooo | http://zww.me]
 * @return [type] [description]
 * 源代码是使用jQuery在前端进行文章计数，现改为后台计数[20180826]
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

# JS For Specific Archive Page
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
//将以上JS放置特定页面底部
function load_archive_list_js(){
    if(is_page_template('pages/archives-new.php')){
        do_action('my_inline_script');
    }
}
add_action( 'wp_footer','load_archive_list_js' );
#####################* *#####################