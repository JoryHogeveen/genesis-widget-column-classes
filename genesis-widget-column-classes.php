<?php
/*
 * @package Genesis
 * @author Jory Hogeveen
 *
 * Plugin Name:	Genesis Widget Column Classes
 * Description:	Add Genesis (old Bootstrap) column classes to widgets
 * Plugin URI:	https://wordpress.org/plugins/genesis-widget-column-classes/
 * Version:		1.1.4-dev
 * Author:		Jory Hogeveen
 * Author URI:	http://www.keraweb.nl
 * Text Domain:	genesis-widget-column-classes
 * Domain Path:	/languages/
 * License:		GPLv2
*/

/*
 * Copyright 2015-2016 Jory Hogeveen
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 */
 
! defined( 'ABSPATH' ) and die( 'You shall not pass!' );

if ( ! class_exists( 'WCC_Genesis_Widget_Column_Classes' ) ) {
	
final class WCC_Genesis_Widget_Column_Classes 
{

	/**
	 * The single instance of the class.
	 *
	 * @since	1.1.3
	 * @var		WCC_Genesis_Widget_Column_Classes
	 */
	private static $_instance = null;

	/**
	 * Plugin version
	 *
	 * @since  1.1
	 * @var    string
	 */
	private $version = '1.1.4';

	/**
	 * User ignore nag key
	 *
	 * @since  1.1
	 * @var    string
	 */
	private $noticeKey = 'wcc_ignore_genesis_notice';
	
	/**
	 * Array of possible column classes
	 *
	 * @since  1.1.4
	 * @var    array
	 */	
	private $column_classes = array( 
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
	
	/**
	 * Current user object
	 *
	 * @since  1.1
	 * @var    object
	 */	
	private $curUser = false;
	
	/**
	 * Init function to register plugin hook
	 *
	 * @since   1.1
	 * @access 	private
	 * @return	void
	 */
	private function __construct() {
		self::$_instance = $this;
		
		// Lets start!
		add_action( 'init', array( $this, 'init' ) );
	}
	
	/**
	 * Main Genesis Widget Column Classes Instance.
	 *
	 * Ensures only one instance of Genesis Widget Column Classes is loaded or can be loaded.
	 *
	 * @since	1.1.3
	 * @access 	public
	 * @static
	 * @see		Genesis_Widget_Column_Classes()
	 * @return	Genesis Widget Column Classes - Main instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init function/action and register all used hooks
	 *
	 * @since   1.1
	 * @access 	public
	 * @return	void
	 */
	public function init() {
		
		// Get the current user
		$this->curUser = wp_get_current_user();

		if ( isset( $this->curUser->ID ) ) {
			add_action( 'admin_notices', array( $this, 'genesis_notice' ) ); 
			add_action( 'wp_ajax_'.$this->noticeKey, array( $this, 'ignore_genesis_notice' ) );
		}
		// widget_form_callback instead of in_widget_form because we want these fields to show BEFORE the other fields
		add_filter( 'widget_form_callback', array( $this, 'widget_form_extend' ), 10, 2 );
		add_filter( 'widget_update_callback', array( $this, 'widget_update' ), 10, 2 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'sidebar_params' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

	}
	
	/**
	 * Add notice when theme is nog based on the Genesis Framework
	 * Checks for version in the notice ignore meta value. If the version is the same (user has clicked ignore), then hide it
	 *
	 * @since   0.1
	 * @access 	public
	 * @return	void
	 */
	public function genesis_notice() {
		if ( get_template() != 'genesis' ) {
			if ( get_user_meta( $this->curUser->ID, $this->noticeKey, true ) != $this->version ) {
				$class = 'notice notice-warning is-dismissible';
				$message = '<strong>' . __('Genesis Widget Column Classes', 'genesis-widget-column-classes') . ':</strong> ';
				$message .= sprintf( __('The %s is recommended to ensure that Genesis Widget Column Classes will work properly', 'genesis-widget-column-classes'), '<a href="http://my.studiopress.com/themes/genesis/" targer="_blank">Genesis Framework</a>');
				$ignore = '<a id="' . $this->noticeKey . '" href="?' . $this->noticeKey . '=1" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.', 'genesis-widget-column-classes') . '</span></a>';
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
	 * @access 	public
	 * @return	String
	 */
	public function ignore_genesis_notice() {
		update_user_meta( $this->curUser->ID, $this->noticeKey, $this->version );
		wp_die();
	}
	
	/**
	 * Add options to the widgets
	 *
	 * @since   0.1
	 * @access 	public
	 * @param	array	$instance
	 * @param	object	$widget
	 * @return	Array	$instance
	 */
	public function widget_form_extend( $instance, $widget ) {

		$instance = wp_parse_args( (array) $instance, 
			array( 
				'column-classes' => '', 
				'column-classes-first' => '' 
			) 
		);
		
		
		$row = '<p style="border: 1px solid #eee; padding: 5px; background: #f5f5f5;">';
		$row .= '<label for="' . $widget->get_field_id( 'column-classes' ) . '">' . __('Width', 'genesis-widget-column-classes') . ':</label> ';
		$row .= '<select name="' . $widget->get_field_name( 'column-classes' ) . '" id="' . $widget->get_field_id( 'column-classes' ) . '">';
		
		$row .= '<option value="">- ' . __('none', 'genesis-widget-column-classes') . ' -</option>';
		
		foreach ( $this->column_classes as $class_name ) {
			if ( ! empty( $class_name ) ) {
				$class_label = $class_name;
				$row .= '<option value="' . $class_name . '" ' . selected( $instance['column-classes'], $class_name, false ) . '>' . $class_label . '</option>';
			}
		}
	
		$row .= '</select>';
		$row .= ' <label for="' . $widget->get_field_id( 'column-classes-first' ) . '">' . __('First', 'genesis-widget-column-classes') . ':</label> <input type="checkbox" value="1" name="' . $widget->get_field_name( 'column-classes-first' ) . '" id="' . $widget->get_field_id( 'column-classes-first' ) . '" ' . checked( $instance['column-classes-first'], 1, false ) . '>';
		$row .= '</p>';
	
		echo $row;
		return $instance;
	}
	
	/**
	 * Add the new fields to the update instance
	 *
	 * @since   0.1
	 * @access 	public
	 * @param	array	$instance
	 * @param	array	$new_instance
	 * @return	Array	$instance
	 */
	public function widget_update( $instance, $new_instance ) {

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
	 * @since   0.1
	 * @access 	public
	 * @param	array	$params
	 * @return	array	$params
	 */
	public function sidebar_params( $params ) {
		global $wp_registered_widgets;

		if ( empty( $params[0] ) ) {
			return $params;
		}
		$widget_id	= $params[0]['widget_id'];

		if ( empty( $wp_registered_widgets[ $widget_id ] ) ) {
			return $params;
		}
		$widget_obj	= $wp_registered_widgets[ $widget_id ];

		if ( empty( $widget_obj['callback'][0] ) || empty( $widget_obj['callback'][0]->option_name ) ) {
			return $params;
		}
		$widget_opt	= get_option( $widget_obj['callback'][0]->option_name );

		if ( empty( $widget_obj['params'][0]['number'] ) ) {
			return $params;
		}
		$widget_num	= $widget_obj['params'][0]['number'];
		
		$widget_extra_classes = '';
		if ( isset( $widget_opt[ $widget_num ]['column-classes'] ) && ! empty( $widget_opt[ $widget_num ]['column-classes'] ) ) {
			$widget_extra_classes .= $widget_opt[ $widget_num ]['column-classes'].' ';
		}
		if ( isset( $widget_opt[ $widget_num ]['column-classes-first'] ) && 1 == $widget_opt[ $widget_num ]['column-classes-first'] ) {
			$widget_extra_classes .= 'first ';
		}
	
		$params[0]['before_widget'] = preg_replace( '/class="/', 'class="'.$widget_extra_classes , $params[0]['before_widget'], 1 );
	
		return $params;
	}
	
	/**
	 * Load plugin textdomain.
	 *
	 * @since 	1.1.3
	 * @access 	public
	 * @return	void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'genesis-widget-column-classes', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since  1.1.3
	 * @access public
	 * @return void
	 */
	public function __toString() {
		return get_class( $this );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since  1.1.3
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'genesis-widget-column-classes' ), '1.0.0' );
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since  1.1.3
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'genesis-widget-column-classes' ), '1.0.0' );
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  1.1.3
	 * @access public
	 * @return null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( get_class( $this ) . "::{$method}", esc_html__( 'Method does not exist.', 'genesis-widget-column-classes' ), '1.0.0' );
		unset( $method, $args );
		return null;
	}
	
}

/**
 * Main instance of Genesis Widget Column Classes.
 *
 * Returns the main instance of WCC_Genesis_Widget_Column_Classes to prevent the need to use globals.
 *
 * @since  1.1.3
 * @return WCC_Genesis_Widget_Column_Classes
 */
function Genesis_Widget_Column_Classes() {
	return WCC_Genesis_Widget_Column_Classes::get_instance();
}
Genesis_Widget_Column_Classes();

} // end if class_exists
