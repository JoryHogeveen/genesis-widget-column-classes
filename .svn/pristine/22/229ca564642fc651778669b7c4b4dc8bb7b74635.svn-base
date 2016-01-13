<?php
/*
 * @package Genesis
 * @author Jory Hogeveen
 *
 * Plugin Name:	Genesis Widget Column Classes
 * Description:	Add Genesis (old Bootstrap) column classes to widgets
 * Plugin URI:	https://wordpress.org/plugins/genesis-widget-column-classes/
 * Version:		1.1
 * Author:		Jory Hogeveen
 * Author URI:	http://www.keraweb.nl
 * Text Domain:	wcc_ignore_genesis_notice
 * Domain Path:	/languages/
 * License:		GPLv2
*/

! defined( 'ABSPATH' ) and die( 'You shall not pass!' );

$wcc_genesis_widget_column_classes = new WCC_Genesis_Widget_Column_Classes();

class WCC_Genesis_Widget_Column_Classes {
	
	/**
	 * Plugin version
	 *
	 * @since  1.1
	 * @var    String
	 */
	protected $version = '1.1';

	/**
	 * User ignore nag key
	 *
	 * @since  1.1
	 * @var    String
	 */
	protected $noticeKey = 'wcc_ignore_genesis_notice';
	
	/**
	 * Current user object
	 *
	 * @since  1.1
	 * @var    Object
	 */	
	protected $curUser = false;
	
	/**
	 * Init function to register plugin hook
	 *
	 * @since   1.1
	 * @return	void
	 */
	function WCC_Genesis_Widget_Column_Classes() {
		
		// Lets start!
		add_action( 'init', array( $this, 'init' ) );
	}
	
	/**
	 * Init function/action and register all used hooks
	 *
	 * @since   1.1
	 * @return	void
	 */
	function init() {
		
		// Get the current user
		$this->curUser = wp_get_current_user();
		
		add_action( 'admin_notices', array( $this, 'genesis_notice' ) ); 
		add_action( 'wp_ajax_'.$this->noticeKey, array( $this, 'ignore_genesis_notice' ) );
		add_filter( 'widget_form_callback', array( $this, 'widget_form_extend' ), 10, 2 );
		add_filter( 'widget_update_callback', array( $this, 'widget_update' ), 10, 2 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'sidebar_params' ) );
	}
	
	/**
	 * Add notice when theme is nog based on the Genesis Framework
	 * Checks for version in the notice ignore meta value. If the version is the same (user has clicked ignore), then hide it
	 *
	 * @since   0.1
	 * @return	void
	 */
	function genesis_notice() {
		global $current_user;
		if ( get_template() != 'genesis' ) {
			if ( get_user_meta( $this->curUser->ID, $this->noticeKey, true ) != $this->version ) {
				$class = 'error notice is-dismissible';
				$message = '<strong>Genesis Widget Column Classes:</strong> The <a href="http://my.studiopress.com/themes/genesis/" targer="_blank">Genesis framework</a> is recommended to ensure that Genesis Widget Column Classes will work properly';
				$ignore = '<a id="' . $this->noticeKey . '" href="?' . $this->noticeKey . '=1" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></a>';
				$script = '<script>(function($) { $(document).on("click", "#' . $this->noticeKey . '", function(e){e.preventDefault();$.post(ajaxurl, {\'action\': \'' . $this->noticeKey . '\'});}) })( jQuery );</script>';
				echo '<div id="' . $this->noticeKey . '" class="' . $class . '"> <p>' . $message . '</p> ' . $ignore . $script . '</div>';
			}
		}
	}
	
	/**
	 * AJAX handler
	 * Stores plugin version
	 *
	 * Store format: Boolean
	 *
	 * @since   1.1
	 * @return	String
	 */
	function ignore_genesis_notice() {
		update_user_meta( $this->curUser->ID, $this->noticeKey, $this->version );
		wp_die();
	}
	
	/**
	 * Add options to the widgets
	 *
	 * @param	array	$instance
	 * @param	object	$widget
	 * 
	 * @since   0.1
	 * @return	Array	$instance
	 */
	function widget_form_extend( $instance, $widget ) {
		if ( ! isset( $instance['column-classes'] ) )
			$instance['column-classes'] = null;
			
		if ( ! isset( $instance['column-classes-first'] ) )
			$instance['column-classes-first'] = null;
		
		$columnClasses = array( '', 
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
		
		$row = '<p style="border: 1px solid #eee; padding: 5px; background: #f5f5f5;">';
		$row .= '<label for="widget-' . $widget->id_base . '-' . $widget->number . '-column-classes">' . __( 'Width' ) . ':</label> ';
		$row .= '<select name="widget-' . $widget->id_base . '[' . $widget->number . '][column-classes]" id="widget-' . $widget->id_base . '-' . $widget->number . '-column-classes">';
		
		foreach ( $columnClasses as $className ) {
			if ( $className != '' ) {
				$classLabel = $className;
			} else {
				$classLabel = __('none');
			}
			$selected = '';
			if ( isset( $instance['column-classes'] ) && $instance['column-classes'] == $className ) {
				$selected = ' selected="selected"';
			}
			$row .= '<option value="' . $className . '"' . $selected . '>' . $classLabel . '</option>';
		}
	
		$row .= '</select>';
		$checkedFirst = false;
		if ( isset( $instance['column-classes-first'] ) && $instance['column-classes-first'] == 1 ) {
			$checkedFirst = ' checked="checked"';
		}
		$row .= ' <label for="widget-' . $widget->id_base . '-' . $widget->number . '-column-classes-first">'.__('First').':</label> <input type="checkbox" value="1" name="widget-' . $widget->id_base . '[' . $widget->number . '][column-classes-first]" id="widget-' . $widget->id_base . '-' . $widget->number . '-column-classes-first"' . $checkedFirst . '>';
		$row .= '</p>';
	
		echo $row;
		return $instance;
	}
	
	/**
	 * Add the new fields to the update instance
	 *
	 * @param	array	$instance
	 * @param	array	$new_instance
	 *
	 * @since   0.1
	 * @return	Array	$instance
	 */
	function widget_update( $instance, $new_instance ) {
		if ( isset( $new_instance['column-classes'] ) ) {
			$instance['column-classes'] = strip_tags( $new_instance['column-classes'] );
		} else { $instance['column-classes'] = ''; }
		if ( isset( $new_instance['column-classes-first'] ) ) {
			$instance['column-classes-first'] = '1';//esc_attr( $new_instance['column-classes-first'] );
		} else { $instance['column-classes-first'] = ''; }
		return $instance;
	}
	
	/**
	 * Add classes to the widget
	 *
	 * @param	array	$params
	 *
	 * @since   0.1
	 * @return	Array	$params
	 */
	function sidebar_params( $params ) {
		global $wp_registered_widgets;
		$widget_id	= $params[0]['widget_id'];
		$widget_obj	= $wp_registered_widgets[$widget_id];
		$widget_opt	= get_option( $widget_obj['callback'][0]->option_name );
		$widget_num	= $widget_obj['params'][0]['number'];
		
		$widget_extra_classes = '';
		if ( isset( $widget_opt[$widget_num]['column-classes'] ) && ! empty( $widget_opt[$widget_num]['column-classes'] ) ) {
			$widget_extra_classes .= $widget_opt[$widget_num]['column-classes'].' ';
	
			if ( isset( $widget_opt[$widget_num]['column-classes-first'] ) && $widget_opt[$widget_num]['column-classes-first'] == 1) {
				$widget_extra_classes .= 'first ';
			}
		}
	
		$params[0]['before_widget'] = preg_replace( '/class="/', 'class="'.$widget_extra_classes , $params[0]['before_widget'], 1 );
	
		return $params;
	}

}
