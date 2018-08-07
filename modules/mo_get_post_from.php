<?php
/**
 * [mo_get_post_from description]
 * @param  string $pid      [description]
 * @param  string $prevtext [description]
 * @return [type]           [description]
 */
function mo_get_post_from($pid='', $prevtext='来源：'){
    if( !im('article_source_section') ){
        return;
    }
    if( !$pid ){
        $pid = get_the_ID();
    }
    $fromname = trim(get_post_meta($pid, "source_value", true));
    $fromurl = trim(get_post_meta($pid, "source_link", true));
    $from = '';
    if( $fromname ){
        if( $fromurl && im('article_source_section_link') ){
            $from = '<a href="'.$fromurl.'" target="_blank" rel="external nofollow">'.$fromname.'</a>';
        }else{
            $from = $fromname;
        }
        $from = (im('article_source_section_title')?im('article_source_section_title'):$prevtext).$from;
    }
    return $from; 
}