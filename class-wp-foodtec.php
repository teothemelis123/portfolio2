<?php
/**
 * Main
 *
 * @category Wodrpress-Plugins
 * @package  WP-FoodTec
 * @author   FoodTec Solutions <info@foodtecsolutions.com>
 * @license  GPLv2 or later
 * @link     https://gitlab.foodtecsolutions.com/fts/wordpress/plugins/wp-foodtec
 * @since    1.0.0
 */

namespace WP_FoodTec\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class WP_FoodTec {
	/**
	 * The zoho object
	 *
	 * @var    Libraries\Zoho
	 * @access public
	 * @since  1.0.0
	 */
	public $zoho;
	/**
	 * The single instance of WP_FoodTec.
	 *
	 * @var    WP_FoodTec
	 * @access private
	 * @since  1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 *
	 * @var    object
	 * @access public
	 * @since  1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 *
	 * @var    string
	 * @access public
	 * @since  1.0.0
	 */
	public $script_suffix;

	/**
	 * The google recaptcha object.
	 *
	 * @var Libraries\Google_Recaptcha
	 */
	public $recaptcha;

	/**
	 * The admnin API.
	 *
	 * @var Libraries\Admin_API
	 */
	public $admin;

	/**
	 * Constructor function.
	 *
	 * @param string $file    The file.
	 * @param string $version The plugin version.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'fts';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'dist';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/dist/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );

		// Load admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new Libraries\Admin_API();
		}

		$update              = new Update( $this );
		$this->recaptcha     = new Libraries\Google_Recaptcha();
		$this->zoho          = new Libraries\Zoho();
		$zoho                = new Libraries\Site_Options();
		$mailer              = new Libraries\Mailer();
		$enqueue_options     = new Libraries\Enqueue_Options();
		$login_customizer    = new Libraries\Login_Customizer();
		$emoji               = new Libraries\Emoji();
		$attachment_url      = new Libraries\Attachment_Url();
		$allowed_hosts       = new Libraries\Allowed_Hosts();
		$redirection         = new Libraries\Redirection();
		$hubspot             = new Libraries\HubSpot();
		$hotjar              = new Libraries\HotJar();
		$geocoder            = new Libraries\Geocoding\Geocoder();
		$google_remarketing  = new Libraries\Google_Remarketing();
		$forgot_password     = new Libraries\Forgot_Password();
		$tag_manager         = new Libraries\Tag_Manager();
		$store_post_type     = new Libraries\Store_Post_Type( $this );
		$head_meta           = new Libraries\Head_Meta();
		$store_meta          = new Libraries\Store_Meta( $this );
		$store_title         = new Libraries\Store_Title();
		$obfuscator          = new Libraries\Obfuscator();
		$headers             = new Libraries\Headers();
		$roles               = new Libraries\Roles();
		$widget_background   = new Libraries\Widget_Background();
		$minify              = new Libraries\Minify();
		$embedded_ordering   = new Libraries\Embedded_Ordering();
		$preorder            = new Libraries\Preorder();
		$default_options     = new Libraries\Default_Options();
		$css_classes         = new Libraries\Css_Classes();
		$suggested_plugins   = new Libraries\Suggested_Plugins();
		$image_custom_fields = new Libraries\Image_Custom_Fields();
		$adroll              = new Libraries\Adroll();
		$loading             = new Libraries\Loading();
		$jquery              = new Libraries\JQuery();
		$facebook_pixel      = new Libraries\Facebook_Pixel();
		$auth_service        = new Libraries\Auth_Service();
		$default_widgets     = new Libraries\Default_Widgets();
		$canonical_url       = new Libraries\Canonical_Url();
		$store_sub_pages     = new Libraries\Store_Sub_Pages();
		$addresses           = new Libraries\Addresses();

		$this->register_shortcodes();
		$this->register_widgets();

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	}

	/**
	 * Registers all shortcodes.
	 *
	 * @return void
	 */
	private function register_shortcodes() {
		$shortcodes = array(
			'Contact_Form',
			
		);

		$registered_shortcodes = array();

		foreach ( $shortcodes as $shortcode ) {
			$shortcode_class                     = '\WP_FoodTec\Includes\Shortcodes\\' . $shortcode;
			$registered_shortcodes[ $shortcode ] = new $shortcode_class();
		}
	}

	/**
	 * Registers all widgets.
	 *
	 * @return void
	 */
	private function register_widgets() {
		$widgets = array(
			'Contact_Form',
		);

		foreach ( $widgets as $class_name ) {
			add_action(
				'widgets_init',
				function () use ( $class_name ) {
					return register_widget( '\WP_FoodTec\Includes\Widgets\\' . $class_name );
				}
			);
		}
	}

	/**
	 * Load frontend Javascript.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'scripts/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'styles/styles' . $this->script_suffix . '.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	}

	/**
	 * Load admin CSS.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_enqueue_styles() {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'styles/admin' . $this->script_suffix . '.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	}


	/**
	 * Load admin Javascript.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'scripts/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	}

	/**
	 * Load plugin localisation
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'wp-foodtec', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin textdomain
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$domain = 'wp-foodtec';
		// phpcs:disable
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		// phpcs:enable

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Main WP_FoodTec Instance
	 *
	 * @static
	 * @see wp_foodTec()
	 *
	 * @param string $file    The file.
	 * @param string $version The plugin version.
	 *
	 * @return WP_FoodTec     The WP_FoodTec instance.
	 */
	public static function instance( $file = '', $version = '1.0.0' ): self {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-foodtec' ), $this->_version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-foodtec' ), $this->_version );
	}

	/**
	 * Installation. Runs on activation.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	}
}
