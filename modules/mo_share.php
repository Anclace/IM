<?php 
/**
 * [mo_share description]
 * @param  string $stop [description]
 * @return [type]       [description]
 */
function mo_share($stop=''){
    if( im('share_module') ){
        echo im('share_module_code');
    }
    return;
	$shares = array(
        'qzone',
        'tsina',
        'weixin',
        'tqq',
        'sqq',
        'bdhome',
        'tqf',
        'renren',
        'diandian',
        'youdao',
        'ty',
        'kaixin001',
        'taobao',
        'douban',
        'fbook',
        'twi',
        'mail',
        'copy'
    );
    $html = '';
    foreach ($shares as $value) {
        $html .= '<a class="bds_'.$value.'" data-cmd="'.$value.'"></a>';
        if( $stop == $value ){
            break;
        }
    }
    echo '<span>'.__('分享到：', 'im').'</span>'.$html;
    if( !$stop ){
        echo '<a class="bds_more" data-cmd="more">'.__('更多', 'im').'</a> <span>(</span><a class="bds_count" data-cmd="count"></a><span>)</span>';
    }
}