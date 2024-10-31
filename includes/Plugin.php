<?php

namespace PostShowcase;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The main plugin class.
 *
 * @since 1.0.0
 * @package PostShowcase
 */
class Plugin {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $file;

	/**
	 * Plugin version.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since 1.0.0
	 */
	public static $instance;

	/**
	 * Gets the single instance of the class.
	 * This method is used to create a new instance of the class.
	 *
	 * @param string $file The plugin file path.
	 * @param string $version The plugin version.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	final public static function create( $file, $version = '1.0.0' ) {
		if ( null === self::$instance ) {
			self::$instance = new static( $file, $version );
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param string $file The plugin file path.
	 * @param string $version The plugin version.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $file, $version ) {
		$this->file    = $file;
		$this->version = $version;
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function define_constants() {
		// Define the plugin version.
		if ( ! defined( 'PSHOWCASE_VERSION' ) ) {
			define( 'PSHOWCASE_VERSION', $this->version );
		}

		// Define the plugin file.
		if ( ! defined( 'PSHOWCASE_FILE' ) ) {
			define( 'PSHOWCASE_FILE', $this->file );
		}

		// Define the plugin path.
		if ( ! defined( 'PSHOWCASE_PATH' ) ) {
			define( 'PSHOWCASE_PATH', plugin_dir_path( PSHOWCASE_FILE ) );
		}

		// Define the plugin URL.
		if ( ! defined( 'PSHOWCASE_URL' ) ) {
			define( 'PSHOWCASE_URL', plugin_dir_url( PSHOWCASE_FILE ) );
		}

		// Define the plugin assets path.
		if ( ! defined( 'PSHOWCASE_ASSETS_PATH' ) ) {
			define( 'PSHOWCASE_ASSETS_PATH', PSHOWCASE_PATH . 'assets/' );
		}

		// Define the plugin assets URL.
		if ( ! defined( 'PSHOWCASE_ASSETS_URL' ) ) {
			define( 'PSHOWCASE_ASSETS_URL', PSHOWCASE_URL . 'assets/' );
		}
	}

	/**
	 * Include the required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function includes() {
		require_once __DIR__ . '/functions.php';
	}

	/**
	 * Initialize the plugin hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function init_hooks() {
		register_activation_hook( PSHOWCASE_FILE, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'display_flash_notices' ), 12 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 * This method is called when the plugin is activated.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function activate() {
		update_option( 'pshowcase_version', PSHOWCASE_VERSION );

		// Default settings.
		$default_settings = array(
			'shortcode_is_enabled' => 'yes',
			'post_type'            => 'post',
			'post_limit'           => 6,
			'display_columns'      => 3,
			'posts_orderby'        => 'date',
			'posts_order'          => 'DESC',
		);
		// Update the default settings.
		update_option( 'pshowcase_settings', $default_settings );
	}

	/**
	 * Load the plugin text domain.
	 * This method is used to load the plugin text domain.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'post-showcase', false, dirname( plugin_basename( PSHOWCASE_FILE ) ) . '/languages' );
	}

	/**
	 * Add a flash notice.
	 *
	 * @param string  $notice Notice message.
	 * @param string  $type This can be "info", "warning", "error" or "success", "success" as default.
	 * @param boolean $dismissible Whether the notice is-dismissible or not.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function flash_notice( $notice = '', $type = 'success', $dismissible = true ) {
		$notices          = get_option( 'pshowcase_flash_notices', array() );
		$dismissible_text = ( $dismissible ) ? 'is-dismissible' : '';

		// Add new notice.
		array_push(
			$notices,
			array(
				'notice'      => wp_kses_post( $notice ),
				'type'        => sanitize_key( $type ),
				'dismissible' => $dismissible_text,
			)
		);

		// Update the notices array.
		update_option( 'pshowcase_flash_notices', $notices );
	}

	/**
	 * Display flash notices after that, remove the option to prevent notices being displayed forever.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function display_flash_notices() {
		$notices = get_option( 'pshowcase_flash_notices', array() );

		foreach ( $notices as $notice ) {
			echo wp_kses_post(
				sprintf(
					'<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
					esc_attr( $notice['type'] ),
					esc_attr( $notice['dismissible'] ),
					esc_html( $notice['notice'] ),
				)
			);
		}

		// Reset options to prevent notices being displayed forever.
		if ( ! empty( $notices ) ) {
			delete_option( 'pshowcase_flash_notices', array() );
		}
	}

	/**
	 * Enqueue the plugin scripts and styles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		// Enqueue the frontend styles. Only if it's a single post or page.
		if ( is_singular() ) {
			wp_enqueue_style( 'pshowcase-frontend', PSHOWCASE_ASSETS_URL . 'css/pshowcase-frontend.css', array(), PSHOWCASE_VERSION );
		}
	}

	/**
	 * Initialize the plugin.
	 * This method is used to initialize the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		// Check if the plugin is enabled. If enabled, load the plugin classes.
		if ( 'yes' === pshowcase_get_settings( 'shortcode_is_enabled', 'yes' ) ) {
			new Controllers\Showcase();
		}

		// Load the admin classes if it's an admin area.
		if ( is_admin() ) {
			new Admin\Admin();
		}

		// Plugin init action.
		do_action( 'post_showcase_init' );
	}
}
