<?php
add_action('wp_footer','rewards',10,2);
function rewards(){
	if(im('reward_enable')){
		$connection = '<div class="rewards-popover-mask" data-event="rewards-close"></div>';
		$connection .= '<div class="rewards-popover">';
		$connection .= '	<h3>'.im('reward_info').'</h3>';
			for($i = 1;$i <= 2;$i++){
				if(im('reward_method_img'.$i)&&im('reward_method_'.$i)){
					$connection .= '<figure class="rewards-popover-item">';
					$connection .= '	<img src="'.im('reward_method_img'.$i).'" alt="'.im('reward_method_'.$i).'">';
					$connection .= '	<figcaption>'.im('reward_method_'.$i).'</figcaption>';
					$connection .= '</figure>';
				}
			}
		$connection .= '	<span class="rewards-popover-close" data-event="rewards-close"><i class="fa fa-close"></i></span>';
		$connection .= '</div>'."\n";
		echo $connection;
	}
}
// button postion
function mo_reward($position = true,$float = false){
	if($position){
		echo '<a href="javascript:;" class="action action-rewards" data-event="rewards"><i class="fa fa-jpy"></i> 赏</a>';
	}
	if($float){
		echo "";
	}
}