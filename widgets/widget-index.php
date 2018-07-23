<?php
add_action('widgets_init','unregister_df_widgets');
function unregister_df_widgets(){
	//unregister_widget('WP_Widget_Search');
	//unregister_widget('WP_Widget_Recent_Comments');
}
$widgets = array(
	'ads',
	'comments',
	'posts',
	'readers',
	'statistics',
	'sticky',
	'tags',
	'textads'
);
foreach ($widgets as $widget) {
	include 'widget-'.$widget.'.php';
}
add_action('widgets_init','widget_ui_loader');
function widget_ui_loader(){
	global $widgets;
	foreach ($widgets as $widget) {
		register_widget('widget_ui_'.$widget);
	}
}