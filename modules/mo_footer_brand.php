<div class="branding branding-black">
	<div class="container">
		<h2><?php echo im('footer_spreadings_title') ?></h2>
		<?php  
			for ($i=1; $i <= 2; $i++) { 
				if( im('footer_spreadings_btn_title_'.$i) && im('footer_spreadings_btn_link_'.$i) ){
					echo '<a'.(im('footer_spreadings_btn_link_target_'.$i)?' target="blank"':'').' class="btn btn-lg" href="'.im('footer_spreadings_btn_link_'.$i).'">'.im('footer_spreadings_btn_title_'.$i).'</a>';
				}
			}
		?>
	</div>
</div>