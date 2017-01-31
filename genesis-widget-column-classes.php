<?php
/*
 * @package Genesis_Widget_Column_Classes
 * @author  Jory Hogeveen
 *
 * Plugin Name: Genesis Widget Column Classes
 * Description: Add Genesis (old Bootstrap) column classes to widgets
 * Plugin URI:  https://wordpress.org/plugins/genesis-widget-column-classes/
 * Version:     1.2.1
 * Author:      Jory Hogeveen
 * Author URI:  http://www.keraweb.nl
 * Text Domain: genesis-widget-column-classes
 * Domain Path: /languages/
 * License:     GPLv2
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

/**
 * Plugin initializer class
 *
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Genesis_Widget_Column_Classes
 * @since   0.1
 * @version 1.2.1
 */
final class WCC_Genesis_Widget_Column_Classes
{

	/**
	 * The single instance of the class.
	 *
	 * @since  1.1.3
	 * @var    WCC_Genesis_Widget_Column_Classes
	 */
	private static $_instance = null;

	/**
	 * Plugin version.
	 *
	 * @since  1.1
	 * @var    string
	 */
	private $version = '1.2';

	/**
	 * User ignore nag key.
	 *
	 * @since  1.1
	 * @var    string
	 */
	private $noticeKey = 'wcc_ignore_genesis_notice';

	/**
	 * Array of possible column classes.
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
		'five-sixths',
	);

	/**
	 * Current user object.
	 *
	 * @since  1.1
	 * @var    WP_User
	 */
	private $curUser = null;

	/**
	 * Init function to register plugin hook
	 *
	 * @since   1.1
	 * @access  private
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
	 * @since   1.1.3
	 * @access  public
	 * @static
	 * @see     Genesis_Widget_Column_Classes()
	 * @return  WCC_Genesis_Widget_Column_Classes
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Init function/action and register all used hooks.
	 *
	 * @since   1.1
	 * @access  public
	 * @return  void
	 */
	public function init() {

		// Get the current user.
		$this->curUser = wp_get_current_user();

		/**
		 * Change the default column classes.
		 *
		 * @since  1.1.4
		 * @param  array  $column_classes  The column classes.
		 */
		$this->column_classes = apply_filters( 'genesis_widget_column_classes', $this->column_classes );

		if ( isset( $this->curUser->ID ) ) {
			add_action( 'admin_notices', array( $this, 'genesis_notice' ) );
			add_action( 'wp_ajax_' . $this->noticeKey, array( $this, 'ignore_genesis_notice' ) );
		}

		if ( is_admin() ) {
			add_action( 'init', array( $this, 'load_textdomain' ) );
		}

		// widget_form_callback instead of in_widget_form because we want these fields to show BEFORE the other fields
		add_filter( 'widget_form_callback', array( $this, 'widget_form_extend' ), 10, 2 );
		add_filter( 'widget_update_callback', array( $this, 'widget_update' ), 10, 2 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'sidebar_params' ), 99999 ); // Make sure to be the last one
	}

	/**
	 * Add notice when theme is nog based on the Genesis Framework.
	 * Checks for version in the notice ignore meta value. If the version is the same (user has clicked ignore), then hide it.
	 *
	 * @since   0.1
	 * @access  public
	 * @return  void
	 */
	public function genesis_notice() {
		if ( 'genesis' !== get_template() ) {
			if ( get_user_meta( $this->curUser->ID, $this->noticeKey, true ) !== $this->version ) {
				$class = 'notice notice-warning is-dismissible';
				$message = '<strong>' . __( 'Genesis Widget Column Classes', 'genesis-widget-column-classes' ) . ':</strong> ';
				$message .= sprintf( __( 'The %s is recommended to ensure that Genesis Widget Column Classes will work properly', 'genesis-widget-column-classes' ), '<a href="http://my.studiopress.com/themes/genesis/" target="_blank">Genesis Framework</a>' );
				$ignore = '<a id="' . $this->noticeKey . '" href="?' . $this->noticeKey . '=1" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'genesis-widget-column-classes' ) . '</span></a>';
				$script = '<script>(function($) { $(document).on("click", "#' . $this->noticeKey . '", function(e){e.preventDefault();$.post(ajaxurl, {\'action\': \'' . $this->noticeKey . '\'});}) })( jQuery );</script>';
				echo '<div id="' . $this->noticeKey . '" class="' . $class . '"> <p>' . $message . '</p> ' . $ignore . $script . '</div>';
			}
		}
	}

	/**
	 * AJAX handler.
	 * Stores plugin version.
	 *
	 * Store format: Boolean.
	 *
	 * @since   1.1
	 * @access  public
	 */
	public function ignore_genesis_notice() {
		update_user_meta( $this->curUser->ID, $this->noticeKey, $this->version );
		wp_die();
	}

	/**
	 * Add options to the widgets.
	 *
	 * @since   0.1
	 * @access  public
	 * @param   array   $instance
	 * @param   object  $widget
	 * @return  array   $instance
	 */
	public function widget_form_extend( $instance, $widget ) {

		$instance = wp_parse_args( (array) $instance,
			array(
				'column-classes' => '',
				'column-classes-first' => '',
			)
		);

		$row = '<p style="border: 1px solid #eee; padding: 5px; background: #f5f5f5;">';
		$row .= '<label for="' . $widget->get_field_id( 'column-classes' ) . '">' . __( 'Width', 'genesis-widget-column-classes' ) . ':</label> ';
		$row .= '<select name="' . $widget->get_field_name( 'column-classes' ) . '" id="' . $widget->get_field_id( 'column-classes' ) . '">';

		$row .= '<option value="">- ' . __( 'none', 'genesis-widget-column-classes' ) . ' -</option>';

		foreach ( $this->column_classes as $class_name ) {
			if ( ! empty( $class_name ) ) {
				$class_label = $class_name;
				$row .= '<option value="' . $class_name . '" ' . selected( $instance['column-classes'], $class_name, false ) . '>' . $class_label . '</option>';
			}
		}

		$row .= '</select>';
		$row .= ' <label for="' . $widget->get_field_id( 'column-classes-first' ) . '">' . __( 'First', 'genesis-widget-column-classes' ) . ':</label>';
		$row .= ' <input type="checkbox" value="1" name="' . $widget->get_field_name( 'column-classes-first' ) . '" id="' . $widget->get_field_id( 'column-classes-first' ) . '" ' . checked( $instance['column-classes-first'], 1, false ) . '>';
		$row .= '</p>';

		echo $row;
		return $instance;
	}

	/**
	 * Add the new fields to the update instance.
	 *
	 * @since   0.1
	 * @access  public
	 * @param   array   $instance
	 * @param   array   $new_instance
	 * @return  array   $instance
	 */
	public function widget_update( $instance, $new_instance ) {
		$instance['column-classes'] = '';
		$instance['column-classes-first'] = '';

		if ( isset( $new_instance['column-classes'] ) ) {
			$instance['column-classes'] = esc_attr( $new_instance['column-classes'] );
		}
		if ( isset( $new_instance['column-classes-first'] ) ) {
			$instance['column-classes-first'] = '1';
		}

		return $instance;
	}

	/**
	 * Add classes to the widget.
	 *
	 * @since   0.1
	 * @access  public
	 * @param   array   $params
	 * @return  array   $params
	 */
	public function sidebar_params( $params ) {
		global $wp_registered_widgets;

		if ( empty( $params[0] ) ) {
			return $params;
		}
		$widget_id  = $params[0]['widget_id'];

		if ( empty( $wp_registered_widgets[ $widget_id ] ) ) {
			return $params;
		}
		$widget_obj = $wp_registered_widgets[ $widget_id ];

		if ( empty( $widget_obj['callback'][0]->option_name ) ) {
			return $params;
		}
		$widget_opt = get_option( $widget_obj['callback'][0]->option_name );

		if ( empty( $widget_obj['params'][0]['number'] ) ) {
			return $params;
		}
		$widget_num = $widget_obj['params'][0]['number'];

		if ( empty( $widget_opt[ $widget_num ] ) ) {
			return $params;
		}

		/**
		 * Compat with plugins that filter the display callback
		 *
		 * @see https://developer.wordpress.org/reference/hooks/widget_display_callback/
		 *
		 * @since 1.2
		 *
		 * @param array     $instance The current widget instance's settings.
		 * @param WP_Widget $this     The current widget instance.
		 * @param array     $args     An array of default widget arguments.
		 */
		$widget_opt[ $widget_num ] = apply_filters( 'widget_display_callback', $widget_opt[ $widget_num ], $widget_obj['callback'][0], $params[0] );

		if ( ! is_array( $widget_opt[ $widget_num ] ) ) {
			return $params;
		}

		$classes_extra = '';
		if ( ! empty( $widget_opt[ $widget_num ]['column-classes'] ) ) {
			$classes_extra .= $widget_opt[ $widget_num ]['column-classes'] . ' ';
		}
		if ( isset( $widget_opt[ $widget_num ]['column-classes-first'] ) && 1 === (int) $widget_opt[ $widget_num ]['column-classes-first'] ) {
			$classes_extra .= 'first ';
		}

		if ( ! empty( $classes_extra ) ) {

			if ( ! empty( $params[0]['before_widget'] ) ) {
				// Add the classes.
				// @todo What if the before_widget tag doesn't have a `class` attribute?
				$params[0]['before_widget'] = $this->append_to_attribute( $params[0]['before_widget'], 'class', $classes_extra, true );
			}

			// $params[0]['before_widget'] = str_replace( 'class="', 'class="'.$classes_extra , $params[0]['before_widget'] );
		}

		return $params;
	}

	/**
	 * Find the class attribute and add the classes in a HTML string.
	 *
	 * @since 1.2
	 *
	 * @param  string  $str            The HTML string.
	 * @param  string  $attr           The attribute to find.
	 * @param  string  $content_extra  The content that needs to be appended.
	 * @param  bool    $unique         Do we need to filter for unique values?
	 *
	 * @return string
	 */
	public function append_to_attribute( $str, $attr, $content_extra, $unique = false ) {

		// Check if attribute has single or double quotes.
		if ( $start = stripos( $str, $attr . '="' ) ) {
			// Double.
			$quote = '"';
		} elseif ( $start = stripos( $str, $attr . "='" ) ) {
			// Single.
			$quote = "'";
		} else {
			// Not found
			return $str;
		}

		// Add quote (for filtering purposes).
		$attr .= '=' . $quote;

		$content_extra = trim( $content_extra );

		if ( $unique ) {

			// Set start pointer to after ".
			$start += strlen( $attr );
			// Find first " after the start pointer.
			$end = strpos( $str, $quote, $start );
			// Get the current content.
			$content = explode( ' ', substr( $str, $start, $end - $start ) );
			// Get our extra content.
			$content_extra = explode( ' ', $content_extra );
			foreach ( $content_extra as $class ) {
				if ( ! empty( $class ) && ! in_array( $class, $content, true ) ) {
					// This one can be added!
					$content[] = $class;
				}
			}
			// Remove duplicates.
			$content = array_unique( $content );
			// Convert to space separated string.
			$content = implode( ' ', $content );
			// Get HTML before content.
			$before_content = substr( $str, 0, $start );
			// Get HTML after content.
			$after_content = substr( $str, $end );

			// Combine the string again.
			$str = $before_content . $content . $after_content;

		} else {
			$str = str_replace( $attr, $attr . $content_extra . ' ' , $str );
		}

		// Return full HTML string.
		return $str;
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since   1.1.3
	 * @access  public
	 * @return  void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'genesis-widget-column-classes', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since   1.1.3
	 * @access  public
	 * @return  string
	 */
	public function __toString() {
		return get_class( $this );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since   1.1.3
	 * @access  public
	 * @return  void
	 */
	public function __clone() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to be cloned', 'genesis-widget-column-classes' ) ),
			null
		);
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since   1.1.3
	 * @access  public
	 * @return  void
	 */
	public function __wakeup() {
		_doing_it_wrong(
			__FUNCTION__,
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to wake up', 'genesis-widget-column-classes' ) ),
			null
		);
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since   1.1.3
	 * @access  public
	 * @param   string  $method  The method name.
	 * @param   array   $args    The method arguments.
	 * @return  null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong(
			esc_html( get_class( $this ) . "::{$method}" ),
			esc_html__( 'Method does not exist.', 'genesis-widget-column-classes' ),
			null
		);
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
function genesis_widget_column_classes() {
	return WCC_Genesis_Widget_Column_Classes::get_instance();
}
genesis_widget_column_classes();

} // end if class_exists
