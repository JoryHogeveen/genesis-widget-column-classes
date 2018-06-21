<?php
/**
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Genesis_Widget_Column_Classes
 * @since   0.1.0
 * @version 1.2.4.1
 * @licence GPL-2.0+
 * @link    https://github.com/JoryHogeveen/genesis-widget-column-classes
 *
 * @wordpress-plugin
 * Plugin Name:       Genesis Widget Column Classes
 * Plugin URI:        https://wordpress.org/plugins/genesis-widget-column-classes/
 * Description:       Add Genesis (old Bootstrap) column classes to widgets
 * Version:           1.2.4.1
 * Author:            Jory Hogeveen
 * Author URI:        http://www.keraweb.nl
 * Text Domain:       genesis-widget-column-classes
 * Domain Path:       /languages/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/JoryHogeveen/genesis-widget-column-classes
 *
 * @copyright 2015-2018 Jory Hogeveen
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
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'WCC_Genesis_Widget_Column_Classes' ) ) {

/**
 * Plugin initializer class
 *
 * @author  Jory Hogeveen <info@keraweb.nl>
 * @package Genesis_Widget_Column_Classes
 * @since   0.1.0
 * @version 1.2.4.1
 */
final class WCC_Genesis_Widget_Column_Classes
{
	/**
	 * The single instance of the class.
	 *
	 * @since  1.1.3
	 * @var    \WCC_Genesis_Widget_Column_Classes
	 */
	private static $_instance = null;

	/**
	 * The plugin basename.
	 *
	 * @since  1.2.4
	 * @var    string
	 */
	public static $_basename = '';

	/**
	 * The plugin i18n domain.
	 *
	 * @since  1.2.4.1
	 * @var    string
	 */
	public static $_domain = 'genesis-widget-column-classes';

	/**
	 * Plugin version.
	 *
	 * @since  1.1.0
	 * @var    string
	 */
	private $version = '1.2.4.1';

	/**
	 * User ignore nag key.
	 *
	 * @since  1.1.0
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
		'one-half'      => 'one-half',
		'one-third'     => 'one-third',
		'one-fourth'    => 'one-fourth',
		'one-sixth'     => 'one-sixth',
		'two-thirds'    => 'two-thirds',
		'two-fourths'   => 'two-fourths',
		'two-sixths'    => 'two-sixths',
		'three-fourths' => 'three-fourths',
		'three-sixths'  => 'three-sixths',
		'four-sixths'   => 'four-sixths',
		'five-sixths'   => 'five-sixths',
	);

	/**
	 * Current user object.
	 *
	 * @since  1.1.0
	 * @var    \WP_User
	 */
	private $curUser = null;

	/**
	 * Capability required to use this plugin.
	 *
	 * @since  1.2.2
	 * @var    string
	 */
	private $cap = 'edit_theme_options';

	/**
	 * Init function to register plugin hook
	 *
	 * @since   1.1.0
	 * @access  private
	 */
	private function __construct() {
		self::$_instance = $this;
		self::$_basename = plugin_basename( __FILE__ );

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
	 * @since   1.1.0
	 * @access  public
	 * @return  void
	 */
	public function init() {

		/**
		 * Change the capability required to use this plugin.
		 * Default: `edit_theme_options`.
		 *
		 * @since  1.2.2
		 * @param  string  $cap  The capability.
		 * @return string
		 */
		$this->cap = apply_filters( 'genesis_widget_column_classes_capability', $this->cap );

		// Get the current user.
		$this->curUser = wp_get_current_user();

		if ( is_admin() && current_user_can( $this->cap ) ) {
			add_action( 'init', array( $this, 'action_load_textdomain' ) );

			if ( isset( $this->curUser->ID ) ) {
				add_action( 'admin_notices', array( $this, 'action_genesis_notice' ) );
				add_action( 'wp_ajax_' . $this->noticeKey, array( $this, 'action_ignore_genesis_notice' ) );
			}
		}

		// Add links to plugins page.
		add_action( 'plugin_row_meta', array( $this, 'action_plugin_row_meta' ), 10, 2 );

		// widget_form_callback instead of in_widget_form because we want these fields to show BEFORE the other fields.
		add_filter( 'widget_form_callback', array( $this, 'filter_widget_form_extend' ), 1, 2 );
		add_filter( 'widget_update_callback', array( $this, 'filter_widget_update_callback' ), 10, 2 );
		add_filter( 'dynamic_sidebar_params', array( $this, 'filter_dynamic_sidebar_params' ), 99999 ); // Make sure to be the last one.
	}

	/**
	 * Add notice when theme is nog based on the Genesis Framework.
	 * Checks for version in the notice ignore meta value. If the version is the same (user has clicked ignore), then hide it.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @return  void
	 */
	public function action_genesis_notice() {
		if ( 'genesis' !== get_template() ) {
			if ( get_user_meta( $this->curUser->ID, $this->noticeKey, true ) !== $this->version ) {
				$class = 'notice notice-warning is-dismissible';
				$message = '<strong>' . __( 'Genesis Widget Column Classes', 'genesis-widget-column-classes' ) . ':</strong> ';
				// Translators: %s stands for "Genesis Framework".
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
	 * @since   1.1.0
	 * @access  public
	 */
	public function action_ignore_genesis_notice() {
		update_user_meta( $this->curUser->ID, $this->noticeKey, $this->version );
		wp_die();
	}

	/**
	 * Add options to the widgets.
	 *
	 * @since   0.1.0
	 * @access  public
	 * @param   array       $instance
	 * @param   \WP_Widget  $widget
	 * @return  array       $instance
	 */
	public function filter_widget_form_extend( $instance, $widget ) {

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'column-classes' => '',
				'column-classes-first' => '',
			)
		);

		if ( ! current_user_can( $this->cap ) ) {
			?>
			<input type="hidden" name="<?php echo $widget->get_field_name( 'column-classes' ); ?>" value="<?php echo $instance['column-classes']; ?>"/>
			<input type="hidden" name="<?php echo $widget->get_field_name( 'column-classes-first' ); ?>" value="<?php echo $instance['column-classes-first']; ?>"/>
			<?php
			return $instance;
		}

		$row = '<p style="border: 1px solid #eee; padding: 5px 10px; background: #f5f5f5;">';
		$row .= '<label for="' . $widget->get_field_id( 'column-classes' ) . '">' . __( 'Width', self::$_domain ) . ': &nbsp;</label>';
		$row .= '<select name="' . $widget->get_field_name( 'column-classes' ) . '" id="' . $widget->get_field_id( 'column-classes' ) . '">';

		$row .= '<option value="">- ' . __( 'none', self::$_domain ) . ' -</option>';

		foreach ( $this->get_column_classes() as $class_name ) {
			if ( ! empty( $class_name ) ) {
				$class_label = $class_name;
				$row .= '<option value="' . $class_name . '" ' . selected( $instance['column-classes'], $class_name, false ) . '>' . $class_label . '</option>';
			}
		}

		$row .= '</select> &nbsp; ';
		$row .= '<label for="' . $widget->get_field_id( 'column-classes-first' ) . '">' . __( 'First', self::$_domain ) . ': &nbsp;</label>';
		$row .= '<input type="checkbox" value="1" name="' . $widget->get_field_name( 'column-classes-first' ) . '" id="' . $widget->get_field_id( 'column-classes-first' ) . '" ' . checked( $instance['column-classes-first'], 1, false ) . '>';
		$row .= '</p>';

		echo $row;
		return $instance;
	}

	/**
	 * Add the new fields to the update instance.
	 *
	 * @since   0.1.0
	 * @since   0.2.2   Do not save empty data.
	 * @access  public
	 * @param   array   $instance
	 * @param   array   $new_instance
	 * @return  array   $instance
	 */
	public function filter_widget_update_callback( $instance, $new_instance ) {
		unset( $instance['column-classes'] );
		unset( $instance['column-classes-first'] );

		if ( ! empty( $new_instance['column-classes'] ) ) {
			$instance['column-classes'] = esc_attr( $new_instance['column-classes'] );
		}
		if ( ! empty( $new_instance['column-classes-first'] ) ) {
			$instance['column-classes-first'] = true;
		}

		return $instance;
	}

	/**
	 * Add classes to the widget.
	 *
	 * // Disable variable check because of global $wp_registered_widgets.
	 * @SuppressWarnings(PHPMD.LongVariables)
	 *
	 * @since   0.1.0
	 * @access  public
	 * @param   array   $params
	 * @return  array   $params
	 */
	public function filter_dynamic_sidebar_params( $params ) {
		global $wp_registered_widgets;

		if ( empty( $params[0]['widget_id'] ) ) {
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
		 * Compat with plugins that filter the display callback.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/widget_display_callback/
		 *
		 * @since  1.2.0
		 *
		 * @param  array       $instance  The current widget instance's settings.
		 * @param  \WP_Widget  $this      The current widget instance.
		 * @param  array       $args      An array of default widget arguments.
		 */
		$widget_opt[ $widget_num ] = apply_filters( 'widget_display_callback', $widget_opt[ $widget_num ], $widget_obj['callback'][0], $params[0] );

		if ( ! is_array( $widget_opt[ $widget_num ] ) ) {
			return $params;
		}

		$classes = $this->get_widget_classes( $widget_opt[ $widget_num ], array() );
		$params[0] = $this->add_widget_classes( $params[0], $classes );
		// $params[0]['before_widget'] = str_replace( 'class="', 'class="'.$classes_extra , $params[0]['before_widget'] );

		return $params;
	}

	/**
	 * Add the classes to the widget parameters.
	 *
	 * @since   1.2.2
	 * @since   1.2.3  Refactor: Remove first $widget_instance parameter.
	 * @param   array  $params           The widget (sidebar) params.
	 * @param   array  $classes          (optional) Append to existing classes.
	 * @return  array
	 */
	public function add_widget_classes( $params, $classes = array() ) {

		if ( empty( $classes ) ) {
			return $params;
		}

		$classes = implode( ' ', $classes );

		if ( ! empty( $params['before_widget'] ) ) {
			// Add the classes.
			// @todo What if the before_widget tag doesn't have a `class` attribute?
			$params['before_widget'] = $this->append_to_attribute( $params['before_widget'], 'class', $classes, true );

		} else {
			// Add our own div that will add the classes.
			$params['before_widget'] = '<div class="' . $classes . '">';

			if ( ! empty( $params['after_widget'] ) ) {
				$params['after_widget'] .= '</div>';
			} else {
				$params['after_widget'] = '</div>';
			}
		}

		return $params;
	}

	/**
	 * Find an attribute and add the data as a HTML string.
	 *
	 * @since   1.2.0
	 *
	 * @param   string  $str            The HTML string.
	 * @param   string  $attr           The attribute to find.
	 * @param   string  $content_extra  The content that needs to be appended.
	 * @param   bool    $unique         Do we need to filter for unique values?
	 *
	 * @return string
	 */
	public function append_to_attribute( $str, $attr, $content_extra, $unique = false ) {

		// Check if attribute has single or double quotes.
		// @codingStandardsIgnoreLine
		if ( $start = stripos( $str, $attr . '="' ) ) {
			// Double.
			$quote = '"';

		// @codingStandardsIgnoreLine
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

			// Set start pointer to after the quote.
			$start += strlen( $attr );
			// Find first quote after the start pointer.
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
			// Remove duplicates and empty values.
			$content = array_filter( array_unique( $content ) );
			// Convert to space separated string.
			$content = implode( ' ', $content );
			// Get HTML before content.
			$before_content = substr( $str, 0, $start );
			// Get HTML after content.
			$after_content = substr( $str, $end );

			// Combine the string again.
			$str = $before_content . $content . $after_content;

		} else {
			$str = preg_replace(
				'/' . preg_quote( $attr, '/' ) . '/',
				$attr . $content_extra . ' ' ,
				$str,
				1
			);
		} // End if().

		// Return full HTML string.
		return $str;
	}

	/**
	 * Get the classes from a widget instance.
	 *
	 * @since   1.2.3
	 * @param   array  $widget_instance  The widget instance.
	 * @param   array  $classes          (optional) Extra classes.
	 * @return  array
	 */
	public function get_widget_classes( $widget_instance, $classes = array() ) {

		if ( ! empty( $widget_instance['column-classes'] ) ) {
			$classes[] = $widget_instance['column-classes'];
		}
		if ( ! empty( $widget_instance['column-classes-first'] ) ) {
			$classes[] = 'first';
		}

		return $classes;
	}

	/**
	 * Get the available column classes.
	 *
	 * @since   1.2.2
	 * @return  array
	 */
	public function get_column_classes() {
		static $done = false;

		if ( $done ) {
			return $this->column_classes;
		}

		/**
		 * Change the default column classes.
		 *
		 * @since  1.1.4
		 * @param  array  $column_classes  The column classes.
		 */
		$this->column_classes = apply_filters( 'genesis_widget_column_classes', $this->column_classes );

		$done = true;

		return $this->column_classes;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @since   1.2.4
	 * @see     \WP_Plugins_List_Table::single_row()
	 * @param   array[]  $links  The existing links.
	 * @param   string   $file   The plugin file.
	 * @return  array
	 */
	public function action_plugin_row_meta( $links, $file ) {
		if ( self::$_basename === $file ) {
			foreach ( $this->get_links() as $id => $link ) {
				$icon = '<span class="dashicons ' . $link['icon'] . '" style="font-size: inherit; line-height: inherit; display: inline; vertical-align: text-top;"></span>';
				$title = $icon . ' ' . esc_html( $link['title'] );
				$links[ $id ] = '<a href="' . esc_url( $link['url'] ) . '" target="_blank">' . $title . '</a>';
			}
		}
		return $links;
	}

	/**
	 * Plugin links.
	 *
	 * @since   1.2.4
	 * @return  array[]
	 */
	public function get_links() {
		static $links;
		if ( ! empty( $links ) ) {
			return $links;
		}

		$links = array(
			'support' => array(
				'title' => __( 'Support', self::$_domain ),
				'description' => __( 'Need support?', self::$_domain ),
				'icon'  => 'dashicons-sos',
				'url'   => 'https://wordpress.org/support/plugin/genesis-widget-column-classes/',
			),
			'slack' => array(
				'title' => __( 'Slack', self::$_domain ),
				'description' => __( 'Quick help via Slack', self::$_domain ),
				'icon'  => 'dashicons-format-chat',
				'url'   => 'https://keraweb.slack.com/messages/plugin-gwcc/',
			),
			'review' => array(
				'title' => __( 'Review', self::$_domain ),
				'description' => __( 'Give 5 stars on WordPress.org!', self::$_domain ),
				'icon'  => 'dashicons-star-filled',
				'url'   => 'https://wordpress.org/support/plugin/genesis-widget-column-classes/reviews/',
			),
			'translate' => array(
				'title' => __( 'Translate', self::$_domain ),
				'description' => __( 'Help translating this plugin!', self::$_domain ),
				'icon'  => 'dashicons-translation',
				'url'   => 'https://translate.wordpress.org/projects/wp-plugins/genesis-widget-column-classes',
			),
			'issue' => array(
				'title' => __( 'Report issue', self::$_domain ),
				'description' => __( 'Have ideas or a bug report?', self::$_domain ),
				'icon'  => 'dashicons-lightbulb',
				'url'   => 'https://github.com/JoryHogeveen/genesis-widget-column-classes/issues',
			),
			'docs' => array(
				'title' => __( 'Documentation', self::$_domain ),
				'description' => __( 'Documentation', self::$_domain ),
				'icon'  => 'dashicons-book-alt',
				'url'   => 'https://github.com/JoryHogeveen/genesis-widget-column-classes/', //wiki
			),
			'github' => array(
				'title' => __( 'GitHub', self::$_domain ),
				'description' => __( 'Follow and/or contribute on GitHub', self::$_domain ),
				'icon'  => 'dashicons-editor-code',
				'url'   => 'https://github.com/JoryHogeveen/genesis-widget-column-classes/tree/dev',
			),
			'donate' => array(
				'title' => __( 'Donate', self::$_domain ),
				'description' => __( 'Buy me a coffee!', self::$_domain ),
				'icon'  => 'dashicons-smiley',
				'url'   => 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=YGPLMLU7XQ9E8&lc=NL&item_name=Genesis%20Widget%20Column%20Classes&item_number=JWPP%2dGWCC&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted',
			),
			'plugins' => array(
				'title' => __( 'Plugins', self::$_domain ),
				'description' => __( 'Check out my other WordPress plugins', self::$_domain ),
				'icon'  => 'dashicons-admin-plugins',
				'url'   => 'https://profiles.wordpress.org/keraweb/#content-plugins',
			),
		);

		return $links;
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since   1.1.3
	 * @access  public
	 * @return  void
	 */
	public function action_load_textdomain() {
		load_plugin_textdomain( self::$_domain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
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
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to be cloned', self::$_domain ) ),
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
			esc_html( get_class( $this ) . ': ' . __( 'This class does not want to wake up', self::$_domain ) ),
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
			esc_html__( 'Method does not exist.', self::$_domain ),
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
 * @since   1.1.3
 * @return  \WCC_Genesis_Widget_Column_Classes
 */
function genesis_widget_column_classes() {
	return WCC_Genesis_Widget_Column_Classes::get_instance();
}
genesis_widget_column_classes();

} // End if().
