<?php 
// ini_set('display_errors','On');
require dirname(__FILE__).'/../../../../wp-load.php';
$cuid = get_current_user_id();

function get_millisecond() {
    list($s1, $s2) = explode(' ', microtime());     
    return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);  
}