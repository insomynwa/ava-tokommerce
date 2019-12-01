<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Tokommerce_Assets' ) ) {

	/**
	 * Define Ava_Tokommerce_Assets class
	 */
	class Ava_Tokommerce_Assets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;
		public $localize_data = array();

		/**
		 * Constructor for the class
		 */
		public function init() {
			add_action( 'elementor/frontend/before_register_styles', array( $this, 'register_styles' ) );
			add_action( 'elementor/frontend/before_enqueue_styles',   array( $this, 'enqueue_styles' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );

			add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

			add_action( 'elementor/frontend/before_register_scripts', array( $this, 'register_scripts' ) );
			add_action( 'elementor/frontend/before_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );

			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ) );

			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'icons_font_styles' ) );
			add_action( 'elementor/preview/enqueue_styles',      array( $this, 'icons_font_styles' ) );

			$rest_api_url = apply_filters( 'ava-tokommerce/rest/frontend/url', get_rest_url() );

			$this->localize_data = array(
				'ajaxUrl'        => esc_url( admin_url( 'admin-ajax.php' ) ),
				'isMobile'       => filter_var( wp_is_mobile(), FILTER_VALIDATE_BOOLEAN ) ? 'true' : 'false',
				'templateApiUrl' => $rest_api_url . 'ava-tokommerce-api/v1/elementor-template',
				'devMode'        => is_user_logged_in() ? 'true' : 'false',
				'messages'       => array(
					'invalidMail' => esc_html__( 'Please specify a valid e-mail', 'ava-tokommerce' ),
				)
			);
		}

		public function register_styles() {
			// Register vendor slider-pro.css styles (https://github.com/bqworks/slider-pro)
			// wp_register_style(
			// 	'jet-slider-pro-css',
			// 	jet_elements()->plugin_url( 'assets/css/lib/slider-pro/slider-pro.min.css' ),
			// 	false,
			// 	'1.3.0'
			// );

			// // Register vendor juxtapose-css styles
			// wp_register_style(
			// 	'jet-juxtapose-css',
			// 	jet_elements()->plugin_url( 'assets/css/lib/juxtapose/juxtapose.min.css' ),
			// 	false,
			// 	'1.3.0'
			// );

			// wp_register_style(
			// 	'peel-css',
			// 	jet_elements()->plugin_url( 'assets/css/lib/peel/peel.css' ),
			// 	false,
			// 	'1.0.0'
			// );
		}

		public function enqueue_styles() {

			// wp_enqueue_style(
			// 	'ava-tokommerce',
			// 	jet_elements()->plugin_url( 'assets/css/ava-tokommerce.css' ),
			// 	false,
			// 	jet_elements()->get_version()
			// );

			// if ( is_rtl() ) {
			// 	wp_enqueue_style(
			// 		'ava-tokommerce-rtl',
			// 		jet_elements()->plugin_url( 'assets/css/ava-tokommerce-rtl.css' ),
			// 		false,
			// 		jet_elements()->get_version()
			// 	);
			// }

			// $default_theme_enabled = apply_filters( 'ava-tokommerce/assets/css/default-theme-enabled', true );

			// if ( $default_theme_enabled ) {
			// 	wp_enqueue_style(
			// 		'ava-tokommerce-skin',
			// 		jet_elements()->plugin_url( 'assets/css/ava-tokommerce-skin.css' ),
			// 		false,
			// 		jet_elements()->get_version()
			// 	);
			// }
		}

		public function admin_enqueue_styles() {
			// $screen = get_current_screen();

			// // Jet setting page check
			// if ( 'elementor_page_ava-tokommerce-settings' === $screen->base ) {
			// 	wp_enqueue_style(
			// 		'ava-tokommerce-admin-css',
			// 		jet_elements()->plugin_url( 'assets/css/ava-tokommerce-admin.css' ),
			// 		false,
			// 		jet_elements()->get_version()
			// 	);
			// }
		}

		public function enqueue_preview_styles() {

			// if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.6.7', '>=' ) ) {
			// 	return;
			// }

			// $avaliable_widgets = jet_elements_settings()->get( 'avaliable_widgets' );

			// $styles_map = array(
			// 	'ava-tokommerce-video'            => array( 'mediaelement' ),
			// 	'ava-tokommerce-audio'            => array( 'mediaelement' ),
			// 	'ava-tokommerce-slider'           => array( 'jet-slider-pro-css' ),
			// 	'ava-tokommerce-image-comparison' => array( 'jet-juxtapose-css' ),
			// );

			// foreach ( $styles_map as $widget => $styles_list ) {
			// 	$enabled = isset( $avaliable_widgets[ $widget ] ) ? $avaliable_widgets[ $widget ] : '';

			// 	if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $avaliable_widgets ) {

			// 		foreach ( $styles_list as $style ) {
			// 			wp_enqueue_style( $style );
			// 		}
			// 	}
			// }
		}

		public function register_scripts() {

			// $api_disabled = jet_elements_settings()->get( 'disable_api_js', [ 'disable' => 'false' ] );
			// $key          = jet_elements_settings()->get( 'api_key' );

			// if ( ! empty( $key ) && ( empty( $api_disabled ) || 'true' !== $api_disabled['disable'] ) ) {

			// 	wp_register_script(
			// 		'google-maps-api',
			// 		add_query_arg(
			// 			array( 'key' => jet_elements_settings()->get( 'api_key' ), ),
			// 			'https://maps.googleapis.com/maps/api/js'
			// 		),
			// 		false,
			// 		false,
			// 		true
			// 	);
			// }

			// // Register vendor anime.js script (https://github.com/juliangarnier/anime)
			// wp_register_script(
			// 	'jet-anime-js',
			// 	jet_elements()->plugin_url( 'assets/js/lib/anime-js/anime.min.js' ),
			// 	array(),
			// 	'2.2.0',
			// 	true
			// );

			// wp_register_script(
			// 	'jet-tween-js',
			// 	jet_elements()->plugin_url( 'assets/js/lib/tweenjs/tweenjs.min.js' ),
			// 	array(),
			// 	'2.0.2',
			// 	true
			// );


			// // Register vendor salvattore.js script (https://github.com/rnmp/salvattore)
			// wp_register_script(
			// 	'jet-salvattore',
			// 	jet_elements()->plugin_url( 'assets/js/lib/salvattore/salvattore.min.js' ),
			// 	array(),
			// 	'1.0.9',
			// 	true
			// );

			// // Register vendor masonry.pkgd.min.js script
			// wp_register_script(
			// 	'jet-masonry-js',
			// 	jet_elements()->plugin_url( 'assets/js/lib/masonry-js/masonry.pkgd.min.js' ),
			// 	array( 'jquery' ),
			// 	'4.2.1',
			// 	true
			// );

			// // Register vendor slider-pro.js script (https://github.com/bqworks/slider-pro)
			// wp_register_script(
			// 	'jet-slider-pro',
			// 	jet_elements()->plugin_url( 'assets/js/lib/slider-pro/jquery.sliderPro.min.js' ),
			// 	array(),
			// 	'1.3.0',
			// 	true
			// );

			// // Register vendor juxtapose.js script
			// wp_register_script(
			// 	'jet-juxtapose',
			// 	jet_elements()->plugin_url( 'assets/js/lib/juxtapose/juxtapose.min.js' ),
			// 	array(),
			// 	'1.3.0',
			// 	true
			// );

			// // Register vendor tablesorter.js script (https://github.com/Mottie/tablesorter)
			// wp_register_script(
			// 	'jquery-tablesorter',
			// 	jet_elements()->plugin_url( 'assets/js/lib/tablesorter/jquery.tablesorter.min.js' ),
			// 	array( 'jquery' ),
			// 	'2.30.7',
			// 	true
			// );

			// // Register vendor chart.js script (http://www.chartjs.org)
			// wp_register_script(
			// 	'chart-js',
			// 	jet_elements()->plugin_url( 'assets/js/lib/chart-js/chart.min.js' ),
			// 	array(),
			// 	'2.7.3',
			// 	true
			// );

			// // Register vendor html2canvas.js script (https://github.com/niklasvh/html2canvas)
			// wp_register_script(
			// 	'html2canvas',
			// 	jet_elements()->plugin_url( 'assets/js/lib/html2canvas/html2canvas.min.js' ),
			// 	array(),
			// 	'1.0.0-rc.5',
			// 	true
			// );

			// // Register vendor oriDomi.js script (https://github.com/dmotz/oriDomi)
			// wp_register_script(
			// 	'oridomi',
			// 	jet_elements()->plugin_url( 'assets/js/lib/oridomi/oridomi.js' ),
			// 	array(),
			// 	'1.10.0',
			// 	true
			// );

			// wp_register_script(
			// 	'peel-js',
			// 	jet_elements()->plugin_url( 'assets/js/lib/peeljs/peeljs.js' ),
			// 	array(),
			// 	'1.0.0',
			// 	true
			// );
		}

		public function enqueue_scripts() {

			// $min_suffix = jet_elements_tools()->is_script_debug() ? '' : '.min';

			// wp_enqueue_script(
			// 	'ava-tokommerce',
			// 	jet_elements()->plugin_url( 'assets/js/ava-tokommerce' . $min_suffix . '.js' ),
			// 	array( 'jquery', 'elementor-frontend' ),
			// 	jet_elements()->get_version(),
			// 	true
			// );

			// wp_localize_script(
			// 	'ava-tokommerce',
			// 	'jetElements',
			// 	apply_filters( 'ava-tokommerce/frontend/localize-data', $this->localize_data )
			// );
		}

		public function editor_scripts() {

			// $min_suffix = jet_elements_tools()->is_script_debug() ? '' : '.min';

			// wp_enqueue_script(
			// 	'ava-tokommerce-editor',
			// 	jet_elements()->plugin_url( 'assets/js/ava-tokommerce-editor' . $min_suffix . '.js' ),
			// 	array( 'jquery' ),
			// 	jet_elements()->get_version(),
			// 	true
			// );
		}

		public function icons_font_styles() {

			// wp_enqueue_style(
			// 	'ava-tokommerce-font',
			// 	jet_elements()->plugin_url( 'assets/css/ava-tokommerce-icons.css' ),
			// 	array(),
			// 	jet_elements()->get_version()
			// );

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
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

/**
 * Returns instance of Ava_Tokommerce_Assets
 *
 * @return object
 */
function ava_tokommerce_assets() {
	return Ava_Tokommerce_Assets::get_instance();
}
