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

if ( ! class_exists( 'Ava_Tokommerce_Settings' ) ) {

	/**
	 * Define Ava_Tokommerce_Settings class
	 */
	class Ava_Tokommerce_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * [$key description]
		 * @var string
		 */
		public $key = 'ava-tokommerce-settings';

		/**
		 * [$builder description]
		 * @var null
		 */
		public $builder  = null;

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * Available Widgets array
		 *
		 * @var array
		 */
		public $avaliable_widgets = [];

		/**
		 * [$default_avaliable_extensions description]
		 * @var [type]
		 */
		public $default_avaliable_extensions = [
			'section_parallax'  => 'true',
		];

		/**
		 * [$settings_page_config description]
		 * @var [type]
		 */
		public $settings_page_config = [];

		/**
		 * Init page
		 */
		public function init() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 0 );

			add_action( 'admin_menu', array( $this, 'register_page' ), 99 );

			foreach ( glob( ava_tokommerce()->plugin_path( 'includes/addons/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class'=>'Class', 'name' => 'Name', 'slug'=>'Slug' ) );

				$slug = basename( $file, '.php' );
				$this->avaliable_widgets[ $slug ] = $data['name'];
			}

			$this->generate_frontend_config_data();

			add_action(
				'ava-styles-manager/compatibility/register-plugin',
				array( $this, 'register_for_styles_manager' )
			);
		}

		/**
		 * Register ava-tokommerce plugin for styles manager
		 *
		 * @param  object $compatibility_manager AvaStyleManager->compatibility instance
		 * @return void
		 */
		public function register_for_styles_manager( $compatibility_manager ) {
			$compatibility_manager->register_plugin( 'ava-tokommerce', (int) $this->get( 'widgets_load_level', 100 ) );
		}

		/**
		 * [generate_frontend_config_data description]
		 * @return [type] [description]
		 */
		public function generate_frontend_config_data() {

			$default_active_widgets = [];

			foreach ( $this->avaliable_widgets as $slug => $name ) {

				$avaliable_widgets[] = [
					'label' => $name,
					'value' => $slug,
				];

				$default_active_widgets[ $slug ] = 'true';
			}

			$active_widgets = $this->get( 'avaliable_widgets', $default_active_widgets );

			$avaliable_extensions = [
				[
					'label' => esc_html__( 'Section Parallax Extension', 'ava-tokommerce' ),
					'value' => 'section_parallax',
				],
			];

			$active_extensions = $this->get( 'avaliable_extensions', $this->default_avaliable_extensions );

			$rest_api_url = apply_filters( 'ava-tokommerce/rest/frontend/url', get_rest_url() );

			$this->settings_page_config = [
				'messages' => [
					'saveSuccess' => esc_html__( 'Saved', 'ava-tokommerce' ),
					'saveError'   => esc_html__( 'Error', 'ava-tokommerce' ),
				],
				'settingsApiUrl' => $rest_api_url . 'ava-tokommerce-api/v1/plugin-settings',
				'settingsData' => [
					'svg_uploads'             => [
						'value' => $this->get( 'svg_uploads', 'enabled' ),
					],
					'ava_templates'           => [
						'value' => $this->get( 'ava_templates', 'enabled' ),
					],
					'widgets_load_level'      => [
						'value'   => $this->get( 'widgets_load_level', 100 ),
						'options' => [
							[
								'label' => 'None',
								'value' => 0,
							],
							[
								'label' => 'Low',
								'value' => 25,
							],
							[
								'label' => 'Medium',
								'value' => 50,
							],
							[
								'label' => 'Advanced',
								'value' => 75,
							],
							[
								'label' => 'Full',
								'value' => 100,
							],
						],
					],
					'api_key'                 => [
						'value' => $this->get( 'api_key', '' ),
					],
					'disable_api_js'          => [
						'value' => $this->get( 'disable_api_js', [ 'disable' => 'false' ] ),
					],
					'mailchimp-api-key'       => [
						'value' => $this->get( 'mailchimp-api-key', '' ),
					],
					'mailchimp-list-id'       => [
						'value' => $this->get( 'mailchimp-list-id', '' ),
					],
					'mailchimp-double-opt-in' => [
						'value' => $this->get( 'mailchimp-double-opt-in', false ),
					],
					'insta_access_token'      => [
						'value' => $this->get( 'insta_access_token', '' ),
					],
					'weather_api_key'         => [
						'value' => $this->get( 'weather_api_key', '' ),
					],
					'avaliable_widgets'       => [
						'value'   => $active_widgets,
						'options' => $avaliable_widgets,
					],
					'avaliable_extensions'    => [
						'value'   => $active_extensions,
						'options' => $avaliable_extensions,
					],
				],
			];
		}

		/**
		 * Initialize page builder module if required
		 *
		 * @return void
		 */
		public function admin_enqueue_scripts() {

			if ( isset( $_REQUEST['page'] ) && $this->key === $_REQUEST['page'] ) {

				$module_data = ava_tokommerce()->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );
				$ui          = new CX_Vue_UI( $module_data );

				$ui->enqueue_assets();

				wp_enqueue_script(
					'ava-tokommerce-admin-script',
					ava_tokommerce()->plugin_url( 'assets/js/ava-tokommerce-admin.js' ),
					array( 'cx-vue-ui' ),
					ava_tokommerce()->get_version(),
					true
				);

				wp_localize_script(
					'ava-tokommerce-admin-script',
					'AvaTokommerceSettingsPageConfig',
					apply_filters( 'ava-tokommerce/admin/settings-page-config', $this->settings_page_config )
				);
			}
		}

		/**
		 * Return settings page URL
		 *
		 * @return string
		 */
		public function get_settings_page_link() {
			return add_query_arg(
				array(
					'page' => $this->key,
				),
				esc_url( admin_url( 'admin.php' ) )
			);
		}

		/**
		 * [get description]
		 * @param  [type]  $setting [description]
		 * @param  boolean $default [description]
		 * @return [type]           [description]
		 */
		public function get( $setting, $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, array() );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;

		}

		/**
		 * Register add/edit page
		 *
		 * @return void
		 */
		public function register_page() {

			add_menu_page(
				esc_html__( 'Ava Tokommerce', 'ava-tokommerce' ),
				esc_html__( 'Ava Tokommerce', 'ava-tokommerce' ),
				'manage_options',
				$this->key,
				array( $this, 'render_page' ),
				'',
				100
			);
			// add_submenu_page(
			// 	'elementor',
			// 	esc_html__( 'AvaTokommerce Settings', 'ava-tokommerce' ),
			// 	esc_html__( 'AvaTokommerce Settings', 'ava-tokommerce' ),
			// 	'manage_options',
			// 	$this->key,
			// 	array( $this, 'render_page' )
			// );

		}

		/**
		 * Render settings page
		 *
		 * @return void
		 */
		public function render_page() {
			include ava_tokommerce()->get_template( 'admin-templates/settings-page.php' );
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

/**
 * Returns instance of Ava_Tokommerce_Settings
 *
 * @return object
 */
function ava_tokommerce_settings() {
	return Ava_Tokommerce_Settings::get_instance();
}

ava_tokommerce_settings()->init();
