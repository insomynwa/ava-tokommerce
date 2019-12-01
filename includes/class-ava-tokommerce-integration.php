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

if ( ! class_exists( 'Ava_Tokommerce_Integration' ) ) {

	/**
	 * Define Ava_Tokommerce_Integration class
	 */
	class Ava_Tokommerce_Integration {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Check if processing elementor widget
		 *
		 * @var boolean
		 */
		private $is_elementor_ajax = false;

		/**
		 * Initalize integration hooks
		 *
		 * @return void
		 */
		public function init() {

			add_action( 'elementor/init', array( $this, 'register_category' ) );

			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_addons' ), 10 );

			// Log Out without confirmation
			add_action( 'template_redirect', [ $this, 'bypass_logout_confirmation' ] );

			// add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_vendor_addons' ), 20 );

			// add_action( 'elementor/controls/controls_registered', array( $this, 'rewrite_controls' ), 10 );

			// add_action( 'elementor/controls/controls_registered', array( $this, 'add_controls' ), 10 );

			add_action( 'wp_ajax_elementor_render_widget', array( $this, 'set_elementor_ajax' ), 10, -1 );

			// Registration Form elementor-avator hook
			add_action( 'elementor_avator/forms/new_record',  [ $this, 'custom_registration_form' ] , 10, 2 );

			// Visibility Section
			// Add section for settings
			add_action('elementor/element/common/_section_style/after_section_end', [ $this, 'register_section' ] );
			add_action('elementor/element/section/section_advanced/after_section_end', [ $this, 'register_section' ] );

			add_action( 'elementor/element/common/atv_section/before_section_end', [ $this, 'register_controls' ], 10, 2 );
			add_action( 'elementor/element/section/atv_section/before_section_end', [ $this, 'register_controls' ], 10, 2 );

			add_filter('elementor/widget/render_content', [ $this, 'content_change' ], 999, 2 );
			add_filter('elementor/section/render_content', [ $this, 'content_change' ], 999, 2 );

			add_filter( 'elementor/frontend/section/should_render', [ $this, 'section_should_render' ] , 10, 2 );
			add_filter( 'elementor/frontend/widget/should_render', [ $this, 'section_should_render' ] , 10, 2 );
			add_filter( 'elementor/frontend/repeater/should_render', [ $this, 'section_should_render' ] , 10, 2 );
		}

		/**
		 * Set $this->is_elementor_ajax to true on Elementor AJAX processing
		 *
		 * @return  void
		 */
		public function set_elementor_ajax() {
			$this->is_elementor_ajax = true;
		}

		/**
		 * Check if we currently in Elementor mode
		 *
		 * @return void
		 */
		public function in_elementor() {

			$result = false;

			if ( wp_doing_ajax() ) {
				$result = $this->is_elementor_ajax;
			} elseif ( Elementor\Plugin::instance()->editor->is_edit_mode()
				|| Elementor\Plugin::instance()->preview->is_preview_mode() ) {
				$result = true;
			}

			/**
			 * Allow to filter result before return
			 *
			 * @var bool $result
			 */
			return apply_filters( 'ava-tokommerce/in-elementor', $result );
		}

		/**
		 * Register plugin addons
		 *
		 * @param  object $widgets_manager Elementor widgets manager instance.
		 * @return void
		 */
		public function register_addons( $widgets_manager ) {

			$avaliable_widgets = ava_tokommerce_settings()->get( 'avaliable_widgets' );

			require ava_tokommerce()->plugin_path( 'includes/base/class-ava-tokommerce-base.php' );

			foreach ( glob( ava_tokommerce()->plugin_path( 'includes/addons/' ) . '*.php' ) as $file ) {
				$slug = basename( $file, '.php' );

				$enabled = isset( $avaliable_widgets[ $slug ] ) ? $avaliable_widgets[ $slug ] : false;

				if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $avaliable_widgets ) {
					$this->register_addon( $file, $widgets_manager );
				}
			}
		}

		/**
		 * Register vendor addons
		 *
		 * @param  object $widgets_manager Elementor widgets manager instance.
		 * @return void
		 */
		public function register_vendor_addons( $widgets_manager ) {

			// $woo_conditional = array(
			// 	'cb'  => 'class_exists',
			// 	'arg' => 'WooCommerce',
			// );

			// $allowed_vendors = apply_filters(
			// 	'ava-tokommerce/allowed-vendor-addons',
			// 	array(
			// 		'smartslider3' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-smartslider3.php'
			// 			),
			// 			'conditional' => array(
			// 				'cb'  => 'class_exists',
			// 				'arg' => 'SmartSlider3',
			// 			),
			// 		),
			// 		'woo_recent_products' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-woo-recent-products.php'
			// 			),
			// 			'conditional' => $woo_conditional,
			// 		),
			// 		'woo_featured_products' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-woo-featured-products.php'
			// 			),
			// 			'conditional' => $woo_conditional,
			// 		),
			// 		'woo_sale_products' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-woo-sale-products.php'
			// 			),
			// 			'conditional' => $woo_conditional,
			// 		),
			// 		'woo_best_selling_products' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-woo-best-selling-products.php'
			// 			),
			// 			'conditional' => $woo_conditional,
			// 		),
			// 		'woo_top_rated_products' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-woo-top-rated-products.php'
			// 			),
			// 			'conditional' => $woo_conditional,
			// 		),
			// 		'woo_product' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-woo-product.php'
			// 			),
			// 			'conditional' => $woo_conditional,
			// 		),
			// 		'contact_form7' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-contact-form7.php'
			// 			),
			// 			'conditional' => array(
			// 				'cb'  => 'defined',
			// 				'arg' => 'WPCF7_PLUGIN_URL',
			// 			),
			// 		),
			// 		'mp_timetable' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-mp-timetable.php'
			// 			),
			// 			'conditional' => array(
			// 				'cb'  => 'defined',
			// 				'arg' => 'MP_TT_PLUGIN_NAME',
			// 			),
			// 		),
			// 		'booked_calendar' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-booked-calendar.php'
			// 			),
			// 			'conditional' => array(
			// 				'cb'  => 'class_exists',
			// 				'arg' => 'booked_plugin',
			// 			),
			// 		),
			// 		'booked_appointments' => array(
			// 			'file' => ava_tokommerce()->plugin_path(
			// 				'includes/addons/vendor/ava-tokommerce-booked-appointments.php'
			// 			),
			// 			'conditional' => array(
			// 				'cb'  => 'class_exists',
			// 				'arg' => 'booked_plugin',
			// 			),
			// 		),
			// 	)
			// );

			// foreach ( $allowed_vendors as $vendor ) {
			// 	if ( is_callable( $vendor['conditional']['cb'] )
			// 		&& true === call_user_func( $vendor['conditional']['cb'], $vendor['conditional']['arg'] ) ) {
			// 		$this->register_addon( $vendor['file'], $widgets_manager );
			// 	}
			// }
		}

		/**
		 * Rewrite core controls.
		 *
		 * @param  object $controls_manager Controls manager instance.
		 * @return void
		 */
		public function rewrite_controls( $controls_manager ) {

			// $controls = array(
			// 	$controls_manager::ICON => 'Ava_Tokommerce_Control_Icon',
			// );

			// foreach ( $controls as $control_id => $class_name ) {

			// 	if ( $this->include_control( $class_name ) ) {
			// 		$controls_manager->unregister_control( $control_id );
			// 		$controls_manager->register_control( $control_id, new $class_name() );
			// 	}
			// }

		}

		/**
		 * Add new controls.
		 *
		 * @param  object $controls_manager Controls manager instance.
		 * @return void
		 */
		public function add_controls( $controls_manager ) {

			// $controls = array(
			// 	'jet_dynamic_date_time' => array(
			// 		'class' => 'Ava_Tokommerce_Control_Date_Time',
			// 	),
			// 	'jet-box-style' => array(
			// 		'class'   => 'Jet_Group_Control_Box_Style',
			// 		'grouped' => true,
			// 	)
			// );

			// foreach ( $controls as $control_id => $args ) {

			// 	if ( ! isset( $args['class'] ) ) {
			// 		continue;
			// 	}

			// 	$class_name = $args['class'];
			// 	$grouped    = isset( $args['grouped'] ) && $args['grouped'];

			// 	if ( $this->include_control( $class_name, $grouped ) ) {

			// 		if ( $grouped ) {
			// 			$controls_manager->add_group_control( $control_id, new $class_name() );
			// 		} else {
			// 			$controls_manager->register_control( $control_id, new $class_name() );
			// 		}
			// 	}
			// }
		}

		/**
		 * Include control file by class name.
		 *
		 * @param  [type] $class_name [description]
		 * @return [type]             [description]
		 */
		public function include_control( $class_name, $grouped = false ) {

			$filename = sprintf(
				'includes/controls/%2$sclass-%1$s.php',
				str_replace( '_', '-', strtolower( $class_name ) ),
				( true === $grouped ? 'groups/' : '' )
			);

			if ( ! file_exists( ava_tokommerce()->plugin_path( $filename ) ) ) {
				return false;
			}

			require ava_tokommerce()->plugin_path( $filename );

			return true;
		}

		/**
		 * Register addon by file name
		 *
		 * @param  string $file            File name.
		 * @param  object $widgets_manager Widgets manager instance.
		 * @return void
		 */
		public function register_addon( $file, $widgets_manager ) {

			$base  = basename( str_replace( '.php', '', $file ) );
			$class = ucwords( str_replace( '-', ' ', $base ) );
			$class = str_replace( ' ', '_', $class );
			$class = sprintf( 'Elementor\%s', $class );

			require $file;

			if ( class_exists( $class ) ) {
				$widgets_manager->register_widget_type( new $class );
			}
		}

		/**
		 * Register cherry category for elementor if not exists
		 *
		 * @return void
		 */
		public function register_category() {

			$elements_manager = Elementor\Plugin::instance()->elements_manager;
			$cherry_cat       = 'ava-tokommerce';

			$elements_manager->add_category(
				$cherry_cat,
				array(
					'title' => esc_html__( 'Tokommerce', 'ava-tokommerce' ),
					'icon'  => 'font',
				),
				1
			);
		}

		public function custom_registration_form( $record, $ajax_handler ) {
			$form_name = $record->get_form_settings('form_name');
			//Check that the form is the "create new user form" if not - stop and return;
			if ('Register Form' !== $form_name) {
				return;
			}
			$form_data = $record->get_formatted_data();
			$username=$form_data['Username']; //Get tne value of the input with the label "User Name"
			$password = $form_data['Password']; //Get tne value of the input with the label "Password"
			$email=$form_data['Email'];  //Get tne value of the input with the label "Email"
			$user = wp_create_user($username,$password,$email); // Create a new user, on success return the user_id no failure return an error object
			if (is_wp_error($user)){ // if there was an error creating a new user
				$ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); //add the message
				$ajax_handler->is_success = false;
				return;
			}
			$first_name=$form_data["Nama Depan"]; //Get tne value of the input with the label "First Name"
			$last_name=$form_data["Nama Belakang"]; //Get tne value of the input with the label "Last Name"
			wp_update_user(array("ID"=>$user,"first_name"=>$first_name,"last_name"=>$last_name)); // Update the user with the first name and last name

			/* Automatically log in the user and redirect the user to the home page */
			$creds= array( // credientials for newley created user
				"user_login"=>$username,
				"user_password"=>$password,
				"remember"=>true
			);
			$signon = wp_signon($creds); //sign in the new user
			// if ($signon)
			// 	$ajax_handler->add_response_data( 'redirect_url', get_home_url() );
		}

		public function bypass_logout_confirmation() {
			global $wp;

			if ( isset( $wp->query_vars[ 'customer-logout' ] ) ) {
				wp_redirect( str_replace( '&amp;', '&', wp_logout_url( home_url() ) ) );
				exit;
			}
		}

		public function register_section( $element ) {
			$element->start_controls_section(
				'atv_section',
				[
					'tab' 	=> \Elementor\Controls_Manager::TAB_ADVANCED,
					'label' => __( 'Visibility Control', 'ava-tokommerce' ),
				]
			);
			$element->end_controls_section();
		}

		public function register_controls( $element, $args ) {

			$element->add_control(
				'atv_enabled', [
					'label' => __('Enable Conditions', 'ava-tokommerce'),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => __('Yes', 'ava-tokommerce'),
					'label_off' => __('No', 'ava-tokommerce'),
					'return_value' => 'yes',
				]
			);

			$element->add_control(
				'atv_role_visible',
				[
					'type' => \Elementor\Controls_Manager::SELECT2,
					'label' => __( 'Visible for:', 'ava-tokommerce' ),
					'options' => $this->get_roles(),
					'default' => [],
					'multiple' => true,
					'condition' => [
						'atv_enabled' => 'yes',
						'atv_role_hidden' => [],
					],
				]
			);

			$element->add_control(
				'atv_role_hidden',
				[
					'type' => \Elementor\Controls_Manager::SELECT2,
					'label' => __( 'Hidden for:', 'ava-tokommerce' ),
					'options' => $this->get_roles(),
					'default' => [],
					'multiple' => true,
					'condition' => [
						'atv_enabled' => 'yes',
						'atv_role_visible' => [],
					],
				]
			);

		}

		private function get_roles() {
			global $wp_roles;

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new \WP_Roles();
			}
			$all_roles = $wp_roles->roles;
			$editable_roles = apply_filters('editable_roles', $all_roles);

			$data = [ 'ecl-guest' => 'Guests', 'ecl-user' => 'Logged in users' ];

			foreach ( $editable_roles as $k => $role ) {
				$data[$k] = $role['name'];
			}

			return $data;
		}

		public function content_change( $content, $widget ) {

			if (\Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				return $content;
			}

			// Get the settings
			$settings = $widget->get_settings();

			if ( ! $this->should_render( $settings ) ) {
				return '';
			}

			return $content;

		}

		public function section_should_render( $should_render, $section ) {
			// Get the settings
			$settings = $section->get_settings();

			if ( ! $this->should_render( $settings ) ) {
				return false;
			}

			return $should_render;

		}

		private function should_render( $settings ) {
			$user_state = is_user_logged_in();

			if( $settings['atv_enabled'] == 'yes' ) {

				//visible for
				if( ! empty( $settings['atv_role_visible'] ) ) {
					if ( in_array( 'ecl-guest', $settings['atv_role_visible'] ) ) {
						if ( $user_state == true ) {
							return false;
						}
					} elseif ( in_array( 'ecl-user', $settings['atv_role_visible'] ) ) {
						if ( $user_state == false) {
							return false;
						}
					} else {
						if ( $user_state == false ) {
							return false;
						}
						$user = wp_get_current_user();

						$has_role = false;
						foreach ( $settings['atv_role_visible'] as $setting ) {
							if ( in_array( $setting, (array) $user->roles ) ) {
								$has_role = true;
							}
						}
						if ( $has_role === false ) {
							return false;
						}
					}

				}
				//hidden for
				elseif( ! empty( $settings['atv_role_hidden'] ) ) {

					if ( in_array( 'ecl-guest', $settings['atv_role_hidden'] ) ) {

						if ( $user_state == false) {
							return false;
						}
					} elseif ( in_array( 'ecl-user', $settings['atv_role_hidden'] ) ) {
						if ( $user_state == true) {
							return false;
						}
					} else {
						if ( $user_state == false ) {
							return true;
						}
						$user = wp_get_current_user();

						foreach ( $settings['atv_role_hidden'] as $setting ) {
							if ( in_array( $setting, (array) $user->roles ) ) {
								return false;
							}
						}
					}
				}
			}

			return true;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Ava_Tokommerce_Integration
 *
 * @return object
 */
function ava_tokommerce_integration() {
	return Ava_Tokommerce_Integration::get_instance();
}
