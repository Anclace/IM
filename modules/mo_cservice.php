<?php
function mo_cservice(){
	$output = '';
	if(im('cservices_widgets')['cqq']&&im('qq')){
		$output .= '<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.im('qq').'&site=qq&menu=yes" rel="external nofollow"><i class="fa fa-qq"></i><span>QQ咨询</span></a><h6>QQ咨询</h6></li>';
	}
	if(im('cservices_widgets')['cwx']&&im('wechat')){
		$output .= '<li class="rollbar-qrcode"><a href="javascript:;" rel="external nofollow"><i class="fa fa-qrcode"></i><span>微信咨询</span></a><h6>关注微信<img src="'.im('wechat_qr').'"></h6></li>';
	}
	if(im('cservices_widgets')['col']&&im('col_link')){
		$output .= '<li><a target="_blank" href="'.im('col_link').'" rel="external nofollow"><i class="fa fa-globe"></i><span>在线咨询</span></a><h6>在线咨询</h6></li>';
	}
	if(im('cservices_widgets')['ctel']&&im('tel_num')&&wp_is_mobile()){
		$output .= '<li><a href="tel:'.im('tel_num').'" rel="external nofollow"><i class="fa fa-phone"></i><span>电话咨询</span></a><h6>电话咨询</h6></li>';
	}
	if($output){
		echo '
		<div class="rollbar">
			<ul class="ori">
				'.$output.'
			</ul>
		</div>'."\n";
	}
}