<?php
/*
 * @package Genesis
 * @author Jory Hogeveen
 *
 * Plugin Name: Genesis Widget Column Classes
 * Version: 1.0.1
 * Description: Add Genesis (old Bootstrap) column classes to widgets
 * Author: Jory Hogeveen
 * Author URI: http://www.keraweb.nl
 * License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You shall not pass!' );
}

/**
 * Genesis nag
 */
function wcc_genesis_no_found() {
	if (get_template() != 'genesis') {
		$class = 'error';
		$message = 'The <a href="http://my.studiopress.com/themes/genesis/" targer="_blank">Genesis framework</a> is recommended to ensure that Genesis Widget Column Classes will work properly';
        echo '<div class="'.$class.'"> <p>'.$message.'</p></div>'; 
	}
}
add_action( 'admin_notices', 'wcc_genesis_no_found' ); 

/**
 * Add options to the widgets
 */
function wcc_genesis_widget_form_extend( $instance, $widget ) {
	if ( !isset($instance['column-classes']) )
		$instance['column-classes'] = null;
		
	if ( !isset($instance['column-classes-first']) )
		$instance['column-classes-first'] = null;
	
	$columnClasses = array('', 
		'one-half', 
		'one-third', 
		'one-fourth', 
		'one-sixth', 
		'two-thirds', 
		'two-fourths', 
		'two-sixths',
		'three-fourths',
		'three-sixths',
		'four-sixths',
		'five-sixths'
	);
	
	$row = '<p style="border-bottom: 1px solid #f5f5f5; padding-bottom: 5px;">';
	$row .= '<label for="widget-'.$widget->id_base.'-'.$widget->number.'-column-classes">Column Width</label> ';
	$row .= '<select name="widget-'.$widget->id_base.'['.$widget->number.'][column-classes]" id="widget-'.$widget->id_base.'-'.$widget->number.'-column-classes">';
	
	foreach ($columnClasses as $className) {
		if ($className != '') {
			$classLabel = $className;
		} else {
			$classLabel = __( 'none', 'wordpress' );
		}
		$selected = '';
		if (isset($instance['column-classes']) && $instance['column-classes'] == $className) {
			$selected = ' selected="selected"';
		}
		$row .= '<option value="'.$className.'"'.$selected.'>'.$classLabel.'</option>';
	}

	$row .= '</select>';
	$checkedFirst = false;
	if (isset($instance['column-classes-first']) && $instance['column-classes-first'] == 1) {
		$checkedFirst = ' checked="checked"';
	}
	$row .= ' <label for="widget-'.$widget->id_base.'-'.$widget->number.'-column-classes-first">'.__( 'First', 'wordpress' ).'</label> <input type="checkbox" value="1" name="widget-'.$widget->id_base.'['.$widget->number.'][column-classes-first]" id="widget-'.$widget->id_base.'-'.$widget->number.'-column-classes-first"'.$checkedFirst.'>';
	$row .= '</p>';

	echo $row;
	return $instance;
}
add_filter('widget_form_callback', 'wcc_genesis_widget_form_extend', 10, 2);

/**
 * Add the new fields to the update instance
 */
function wcc_genesis_widget_update( $instance, $new_instance ) {
	if (isset($new_instance['column-classes'])) {
		$instance['column-classes'] = strip_tags($new_instance['column-classes']);
	} else {$instance['column-classes'] = '';}
	if (isset($new_instance['column-classes-first'])) {
		$instance['column-classes-first'] = '1';//esc_attr($new_instance['column-classes-first']);
	} else {$instance['column-classes-first'] = '';}
	return $instance;
}
add_filter('widget_update_callback', 'wcc_genesis_widget_update', 10, 2);

/**
 * Add classes to the widget
 */
function wcc_genesis_sidebar_params( $params ) {
	global $wp_registered_widgets;
	$widget_id	= $params[0]['widget_id'];
	$widget_obj	= $wp_registered_widgets[$widget_id];
	$widget_opt	= get_option($widget_obj['callback'][0]->option_name);
	$widget_num	= $widget_obj['params'][0]['number'];
	
	$widget_extra_classes = '';
	if ( isset($widget_opt[$widget_num]['column-classes']) && !empty($widget_opt[$widget_num]['column-classes']) ) {
		$widget_extra_classes .= $widget_opt[$widget_num]['column-classes'].' ';

		if (isset($widget_opt[$widget_num]['column-classes-first']) && $widget_opt[$widget_num]['column-classes-first'] == 1) {
			$widget_extra_classes .= 'first ';
		}
	}

	$params[0]['before_widget'] = preg_replace( '/class="/', 'class="'.$widget_extra_classes , $params[0]['before_widget'], 1 );

	return $params;
}
add_filter('dynamic_sidebar_params', 'wcc_genesis_sidebar_params');
