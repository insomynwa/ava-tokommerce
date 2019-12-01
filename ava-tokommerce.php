<?php
/**
 * Plugin Name: Avator Tokommerce
 * Description: Elementor Woocommerce add-on plugin
 * Author: Mr.Lorem
 * Version: 1.0.0
 *
 * Text Domain: ava-tokommerce
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

define( 'AVA_TOKOMMERCE_VERSION', '1.0.0' );
define( 'AVA_TOKOMMERCE__FILE__', __FILE__ );
define( 'AVA_TOKOMMERCE_PATH', plugin_dir_path( AVA_TOKOMMERCE__FILE__ ) );
define( 'AVA_TOKOMMERCE_URL', plugin_dir_url( AVA_TOKOMMERCE__FILE__ ) );

// If class `Ava_Tokommerce` doesn't exists yet.
if ( ! class_exists( 'Ava_Tokommerce' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Ava_Tokommerce {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private $core = null;

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '1.0.0';

		/**
		 * Plugin slug
		 *
		 * @var string
		 */
		public $plugin_slug = 'ava-tokommerce';

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * UI elements instance
		 *
		 * @var object
		 */
		private $ui = null;

		/**
		 * Dynamic CSS module instance
		 *
		 * @var object
		 */
		private $dynamic_css = null;

		/**
		 * Customizer module instance
		 *
		 * @var object
		 */
		private $customizer = null;

		/**
		 * Dirname holder for plugins integration loader
		 *
		 * @var string
		 */
		private $dir = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load the CX Loader.
			add_action( 'after_setup_theme', array( $this, 'module_loader' ), -20 );

			// Load the installer core.
			// add_action( 'after_setup_theme', require( dirname( __FILE__ ) . '/cherry-framework/setup.php' ), 0 );

			// Load the core functions/classes required by the rest of the plugin.
			// add_action( 'after_setup_theme', array( $this, 'get_core' ), 1 );
			// Load the modules.
			// add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );
			// Load files.
			add_action( 'init', array( $this, 'init' ), -999 );

			// Register activation and deactivation hook.
			// register_activation_hook( __FILE__, array( $this, 'activation' ) );
			// register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		public function module_loader() {
			require $this->plugin_path( 'includes/modules/loader.php' );

			$this->module_loader = new Ava_Tokommerce_CX_Loader(
				array(
					$this->plugin_path( 'includes/modules/vue-ui/cherry-x-vue-ui.php' ),
					$this->plugin_path( 'includes/modules/db-updater/cx-db-updater.php' ),
				)
			);
		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init() {
			if ( ! $this->has_elementor() ) {
				add_action( 'admin_notices', array( $this, 'required_plugins_notice' ) );
				return;
			}

			$this->load_files();

			// $this->dynamic_css = $this->get_core()->init_module( 'cherry-dynamic-css' );
			// $this->customizer  = $this->get_core()->init_module( 'cherry-customizer', array( 'just_fonts' => true ) );

			// $this->customizer->init_fonts();

			ava_tokommerce_assets()->init();
			// ava_tokommerce_post_type()->init();
			// ava_tokommerce_css_file()->init();
			// ava_tokommerce_public_manager()->init();
			ava_tokommerce_integration()->init();

			ava_tokommerce_shipping()->init();
			// ava_tokommerce_option_page();
			// ava_tokommerce_options_presets()->init();

			// $this->include_integration_theme_file();
			// $this->include_integration_plugin_file();

			// if ( is_admin() ) {

			// 	ava_tokommerce_settings_item()->init();
			// 	ava_tokommerce_settings_nav()->init();

			// 	add_action( 'admin_init', array( $this, 'init_ui' ) );

			// 	require $this->plugin_path( 'includes/updater/class-ava-tokommerce-plugin-update.php' );

			// 	ava_tokommerce_updater()->init( array(
			// 		'version' => $this->get_version(),
			// 		'slug'    => 'ava-tokommerce',
			// 	) );

			// 	// Init plugin changelog
			// 	require $this->plugin_path( 'includes/updater/class-ava-tokommerce-plugin-changelog.php' );

			// 	ava_tokommerce_plugin_changelog()->init( array(
			// 		'name'     => 'AvaMenu',
			// 		'slug'     => 'ava-tokommerce',
			// 		'version'  => $this->get_version(),
			// 		'author'   => '<a href="https://zemez.io/zemezava/">Zemez</a>',
			// 		'homepage' => 'http://avamenu.zemez.io/',
			// 		'banners'  => array(
			// 			'high' => $this->plugin_url( 'assets/admin/images/banner.png' ),
			// 			'low'  => $this->plugin_url( 'assets/admin/images/banner.png' ),
			// 		),
			// 	) );

			// 	if ( ! $this->has_elementor() ) {
			// 		$this->required_plugins_notice();
			// 	}

			// }

		}

		/**
		 * Show recommended plugins notice.
		 *
		 * @return void
		 */
		public function required_plugins_notice() {
			$screen = get_current_screen();

			if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
				return;
			}

			$plugin = 'elementor/elementor.php';

			$installed_plugins      = get_plugins();
			$is_elementor_installed = isset( $installed_plugins[ $plugin ] );

			if ( $is_elementor_installed ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

				$message = sprintf( '<p>%s</p>', esc_html__( 'Ava Tokommerce requires Elementor to be activated.', 'ava-tokommerce' ) );
				$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, esc_html__( 'Activate Elementor Now', 'ava-tokommerce' ) );
			} else {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}

				$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

				$message = sprintf( '<p>%s</p>', esc_html__( 'Ava Tokommerce requires Elementor to be installed.', 'ava-tokommerce' ) );
				$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $install_url, esc_html__( 'Install Elementor Now', 'ava-tokommerce' ) );
			}

			printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
		}

		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return did_action( 'elementor/loaded' );
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/class-ava-tokommerce-assets.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-dynamic-css.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-settings-item.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-settings-nav.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-post-type.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-tools.php' );
			require $this->plugin_path( 'includes/class-ava-tokommerce-integration.php' );
			// require $this->plugin_path( 'includes/walkers/class-ava-tokommerce-main-walker.php' );
			// require $this->plugin_path( 'includes/walkers/class-ava-tokommerce-widget-walker.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-public-manager.php' );

			require $this->plugin_path( 'includes/ongkir/ava-tokommerce-helpers.php' );
			require $this->plugin_path( 'includes/ongkir/class-ava-tokommerce-ongkir-api.php' );
			require $this->plugin_path( 'includes/ongkir/class-ava-tokommerce-raja-ongkir.php' );
			require $this->plugin_path( 'includes/ongkir/class-ava-tokommerce-account.php' );
			require $this->plugin_path( 'includes/ongkir/class-ava-tokommerce-courier.php' );
			require $this->plugin_path( 'includes/ongkir/accounts/class-ava-tokommerce-account-raja-ongkir-starter.php' );
			require $this->plugin_path( 'includes/ongkir/accounts/class-ava-tokommerce-account-raja-ongkir-basic.php' );
			require $this->plugin_path( 'includes/ongkir/accounts/class-ava-tokommerce-account-raja-ongkir-pro.php' );
			require $this->plugin_path( 'includes/ongkir/couriers/class-ava-tokommerce-courier-jne.php' );
			require $this->plugin_path( 'includes/ongkir/couriers/class-ava-tokommerce-courier-tiki.php' );
			require $this->plugin_path( 'includes/ongkir/couriers/class-ava-tokommerce-courier-pos.php' );
			require $this->plugin_path( 'includes/ongkir/couriers/class-ava-tokommerce-courier-jnt.php' );
			require $this->plugin_path( 'includes/ongkir/couriers/class-ava-tokommerce-courier-sicepat.php' );

			require $this->plugin_path( 'includes/shipping/class-ava-tokommerce-shipping.php' );

			require $this->plugin_path( 'includes/class-ava-tokommerce-settings.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-options-page.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-options-presets.php' );
			// require $this->plugin_path( 'includes/class-ava-tokommerce-css-file.php' );
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}
		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'ava-tokommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'ava-tokommerce/template-path', 'ava-tokommerce/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
			// require $this->plugin_path( 'includes/class-ava-tokommerce-post-type.php' );
			// ava_tokommerce_post_type()->init();
			flush_rewrite_rules();
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
			flush_rewrite_rules();
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

if ( ! function_exists( 'ava_tokommerce' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function ava_tokommerce() {
		return Ava_Tokommerce::get_instance();
	}
}

ava_tokommerce();
