<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ava_Tokommerce_Shipping' ) ) {

    class Ava_Tokommerce_Shipping {

        private static $instance = null;
		public static $shipping_method_id = 'ava_tokommerce';
		public static $shipping_method_label = 'Ava Tokommerce';

        public function init() {

            // Hook to enqueue scripts & styles assets.
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_assets' ), 999 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ), 999 );

            // Hook to check if this shipping method is availbale for current order.
            add_filter( 'woocommerce_shipping_' . self::$shipping_method_id . '_is_available', [ $this, 'check_is_available' ], 10, 2 );

            // Hook to modify billing and shipping address fields position priority.
            add_filter( 'woocommerce_default_address_fields', array( $this, 'ava_tokommerce_custom_address_fields' ) );
            add_filter( 'woocommerce_billing_fields', array( $this, 'billing_fields_priority' ), 10, 2 );
            add_filter( 'woocommerce_shipping_fields', array( $this, 'shipping_fields_priority' ), 10, 2 );

            // Hook to woocommerce_cart_shipping_packages to inject field address_2.
            add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'inject_cart_shipping_packages' ), 10 );

            // Hook to  print hidden element for the hidden address 2 field after the shipping calculator form.
            add_action( 'woocommerce_after_shipping_calculator', array( $this, 'after_shipping_calculator' ) );

            // Hook to enable city field in the shipping calculator form.
            add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true' );

            add_action( 'woocommerce_shipping_init', [ $this, 'register_ava_tokommerce_shipping_method_init' ] );
            add_filter( 'woocommerce_shipping_methods', [ $this, 'register_ava_tokommerce_shipping_method' ] );
        }

        public function enqueue_backend_assets( $hook = null ) {
            if ( ! is_admin() || 'woocommerce_page_wc-settings' !== $hook ) {
                return;
            }

            wp_enqueue_style(
                'ava-tokommerce-admin-ongkir', // Give the script a unique ID.
                ava_tokommerce()->plugin_url( 'assets/admin/css/admin-ongkir.css' ), // Define the path to the JS file.
                array(), // Define dependencies.
                ava_tokommerce()->get_version(), // Define a version (optional).
                false // Specify whether to put in footer (leave this false).
            );

            wp_register_script(
				'lockr.js',
				ava_tokommerce()->plugin_url( 'assets/libs/js/lockr.js' ),
				array( 'jquery' ),
				ava_tokommerce()->get_version(),
				true
            );

            wp_enqueue_script(
				'ava-tokommerce-admin-ongkir',
				ava_tokommerce()->plugin_url( 'assets/admin/js/admin-ongkir.js' ),
				array( 'jquery', 'wp-util', 'select2', 'selectWoo', 'lockr.js' ),
				ava_tokommerce()->get_version(),
				true
            );

            wp_localize_script( 'ava-tokommerce-admin-ongkir', 'ava_tokommerce_params', ava_tokommerce_scripts_params() );
        }

        public function check_is_available( $available, $package ) {
            if ( WC()->countries->get_base_country() !== 'ID' ) {
                return false;
            }

            if ( empty( $package ) || empty( $package['contents'] ) || empty( $package['destination'] ) ) {
                return false;
            }

            return $available;
        }

        public function enqueue_frontend_assets() {
            if ( is_admin() ) {
                return;
            }

            wp_register_script(
                'lockr.js', // Give the script a unique ID.
                ava_tokommerce()->plugin_url( 'assets/libs/js/lockr.js' ), // Define the path to the JS file.
                array(), // Define dependencies.
                ava_tokommerce()->get_version(), // Define a version (optional).
                true // Specify whether to put in footer (leave this true).
            );

            wp_enqueue_script(
                'ava-tokommerce-ongkir-frontend', // Give the script a unique ID.
                ava_tokommerce()->plugin_url( 'assets/js/frontend-ongkir.js' ), // Define the path to the JS file.
                array( 'jquery', 'wp-util', 'select2', 'selectWoo', 'lockr.js' ), // Define dependencies.
                ava_tokommerce()->get_version(), // Define a version (optional).
                true // Specify whether to put in footer (leave this true).
            );

            wp_localize_script( 'ava-tokommerce-ongkir-frontend', 'ava_tokommerce_params', ava_tokommerce_scripts_params() );
        }

        public function ava_tokommerce_custom_address_fields( $fields ) {
            if ( isset( $fields['state'] ) ) {
                $fields['state']['priority'] = 41;
            }

            if ( isset( $fields['city'] ) ) {
                $fields['city']['priority'] = 42;
            }

            $fields['country']['label'] = esc_html__( 'Negara', 'ava-tokommerce' );
            $fields['first_name']['label'] = esc_html__( 'Nama depan', 'ava-tokommerce' );
            $fields['last_name']['label'] = esc_html__( 'Nama belakang', 'ava-tokommerce' );
            $fields['city']['label'] = esc_html__( 'Kota/Kabupaten', 'ava-tokommerce' );
            $fields['state']['label'] = esc_html__( 'Provinsi', 'ava-tokommerce' );
            $fields['postcode']['label'] = esc_html__( 'Kode Pos', 'ava-tokommerce' );
            $fields['address_2']['label'] = esc_html__( 'Kecamatan', 'ava-tokommerce' );
            $fields['address_2']['required'] = true;
            $fields['address_2']['placeholder'] = esc_html__( 'Kecamatan', 'ava-tokommerce' );

            return $fields;
        }

        public function billing_fields_priority( $fields, $country ) {
            if ( 'ID' !== $country ) {
                return $fields;
            }

            $need_sort = false;

            if ( isset( $fields['billing_state'] ) ) {
                $fields['billing_state']['priority'] = 41;
                $need_sort                           = true;
            }

            if ( isset( $fields['billing_city'] ) ) {
                $fields['billing_city']['priority'] = 42;
                $need_sort                          = true;
            }

            if ( ! $need_sort ) {
                return $fields;
            }

            $priority_offset = count( $fields ) * 10;
            $billing_fields  = array();

            foreach ( $fields as $key => $value ) {
                $billing_fields[ $key ] = isset( $value['priority'] ) ? $value['priority'] : $priority_offset;
                $priority_offset       += 10;
            }

            // Sort fields by priority.
            asort( $billing_fields );

            $billing_field_keys = array_keys( $billing_fields );

            foreach ( $billing_field_keys as $billing_field_key ) {
                $billing_fields[ $billing_field_key ] = $fields[ $billing_field_key ];
            }

            return $billing_fields;
        }

        public function shipping_fields_priority( $fields, $country ) {
            if ( 'ID' !== $country ) {
                return $fields;
            }

            $need_sort = false;

            if ( isset( $fields['shipping_state'] ) ) {
                $fields['shipping_state']['priority'] = 41;

                $need_sort = true;
            }

            if ( isset( $fields['shipping_city'] ) ) {
                $fields['shipping_city']['priority'] = 42;

                $need_sort = true;
            }

            if ( ! $need_sort ) {
                return $fields;
            }

            $priority_offset = count( $fields ) * 10;
            $shipping_fields = array();

            foreach ( $fields as $key => $value ) {
                $shipping_fields[ $key ] = isset( $value['priority'] ) ? $value['priority'] : $priority_offset;
                $priority_offset        += 10;
            }

            // Sort fields by priority.
            asort( $shipping_fields );

            $shipping_field_keys = array_keys( $shipping_fields );

            foreach ( $shipping_field_keys as $shipping_field_key ) {
                $shipping_fields[ $shipping_field_key ] = $fields[ $shipping_field_key ];
            }

            return $shipping_fields;
        }

        public function inject_cart_shipping_packages( $packages ) {
            $nonce_action    = 'woocommerce-shipping-calculator';
            $nonce_name      = 'woocommerce-shipping-calculator-nonce';
            $address_2_field = 'calc_shipping_address_2';

            if ( isset( $_POST[ $nonce_name ], $_POST[ $address_2_field ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), $nonce_action ) ) {
                $address_2 = sanitize_text_field( wp_unslash( $_POST[ $address_2_field ] ) );

                if ( empty( $address_2 ) ) {
                    return $packages;
                }

                foreach ( array_keys( $packages ) as $key ) {
                    WC()->customer->set_billing_address_2( $address_2 );
                    WC()->customer->set_shipping_address_2( $address_2 );
                    $packages[ $key ]['destination']['address_2'] = $address_2;
                }
            }

            return $packages;
        }

        public function after_shipping_calculator() {
            ?>
            <input type="hidden" id="calc_shipping_address_2_dummy" value="<?php echo esc_attr( WC()->cart->get_customer()->get_shipping_address_2() ); ?>" />
            <?php
        }

        public function register_ava_tokommerce_shipping_method_init() {
            require ava_tokommerce()->plugin_path( 'includes/shipping/class-ava-tokommerce-shipping-method.php' );
        }

        public function register_ava_tokommerce_shipping_method( $methods ) {
            // $method contains available shipping methods
            $methods[ self::$shipping_method_id ] = 'Ava_Tokommerce_Shipping_Method';

            return $methods;
        }

        public static function get_instance() {

            if ( null == self::$instance ){
                self::$instance = new self;
            }
            return self::$instance;
        }
    }
}

function ava_tokommerce_shipping() {
    return Ava_Tokommerce_Shipping::get_instance();
}