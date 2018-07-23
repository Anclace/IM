<?
#######################################################
# 
# 
#######################################################
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