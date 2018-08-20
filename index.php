<?php
get_header();
$strings = '<img width="220" height="150" src="http://static.webzgq.com/wp-content/uploads/2018/08/111-220x150.jpg" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="" />';
preg_match_all('/<img.*?(?: |\\t|\\r|\\n)?src=[\'"]?(.+?)[\'"]?(?:(?: |\\t|\\r|\\n)+.*?)?>/sim', $strings, $strResult, PREG_PATTERN_ORDER);
var_dump($strResult);
get_footer();