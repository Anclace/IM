<?php  
	$pagemenus = im('page_left_part_menus');
	$menus = '';
	if( $pagemenus ){
		$pageURL = curPageURL();
		foreach ($pagemenus as $key => $value) {
			if( $value ) {
				$purl = get_permalink($key);
				$menus .= '<li '.($purl==$pageURL?'class="active"':'').'><a href="'.$purl.'">'.get_the_title($key).'</a></li>';
			}
		}
	}

?>
<div class="pageside">
	<div class="pagemenus">
		<ul class="pagemenu">
			<?php echo $menus ?>
		</ul>
	</div>
</div>