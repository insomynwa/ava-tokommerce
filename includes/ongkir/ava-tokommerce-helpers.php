<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ava_tokommerce_sort_by_priority' ) ) :
	/**
	 * Sort data by priority
	 *
	 * @param array $a Item to compare.
	 * @param array $b Item to compare.
	 *
	 * @return int
	 */
	function ava_tokommerce_sort_by_priority( $a, $b ) {
		$a_priority = 0;

		if ( is_object( $a ) && is_callable( array( $a, 'get_priority' ) ) ) {
			$a_priority = $a->get_priority();
		} elseif ( isset( $a['priority'] ) ) {
			$a_priority = $a['priority'];
		}

		$b_priority = 0;

		if ( is_object( $b ) && is_callable( array( $b, 'get_priority' ) ) ) {
			$b_priority = $b->get_priority();
		} elseif ( isset( $b['priority'] ) ) {
			$b_priority = $b['priority'];
		}

		return strcasecmp( $a_priority, $b_priority );
	}
endif;

if ( ! function_exists( 'ava_tokommerce_get_json_data' ) ) :
	/**
	 * Get json file data.
	 *
	 * @since 1.0.0
	 * @param array $file_name File name for the json data.
	 * @param array $search Serach keyword data.
	 * @throws  Exception If WordPress Filesystem Abstraction classes is not available.
	 * @return array
	 */
	function ava_tokommerce_get_json_data( $file_name, $search = array() ) {
		global $wp_filesystem;

		$file_url  = AVA_TOKOMMERCE_URL . 'assets/data/' . $file_name . '.json';
		$file_path = AVA_TOKOMMERCE_PATH . 'assets/data/' . $file_name . '.json';

		try {
			require_once ABSPATH . 'wp-admin/includes/file.php';

			if ( is_null( $wp_filesystem ) ) {
				WP_Filesystem();
			}

			if ( ! $wp_filesystem instanceof WP_Filesystem_Base || ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) ) {
				throw new Exception( 'WordPress Filesystem Abstraction classes is not available', 1 );
			}

			if ( ! $wp_filesystem->exists( $file_path ) ) {
				throw new Exception( 'JSON file is not exists or unreadable', 1 );
			}

			$json = $wp_filesystem->get_contents( $file_path );
		} catch ( Exception $e ) {
			// Get JSON data by HTTP if the WP_Filesystem API procedure failed.
			$json = wp_remote_retrieve_body( wp_remote_get( esc_url_raw( $file_url ) ) );
		}

		if ( ! $json ) {
			return false;
		}

		$json_data  = json_decode( $json, true );
		$json_error = json_last_error_msg();

		if ( ! $json_data || ( $json_error && 'no error' !== strtolower( $json_error ) ) ) {
			return false;
		}

		// Search JSON data by associative array. Return the match row or false if not found.
		if ( $search ) {
			foreach ( $json_data as $row ) {
				if ( array_intersect_assoc( $search, $row ) === $search ) {
					return $row;
				}
			}

			return false;
		}

		return $json_data;
	}
endif;

if ( ! function_exists( 'ava_tokommerce_scripts_params' ) ) :
	/**
	 * Get localized scripts parameters.
	 *
	 * @since 1.2.11
	 *
	 * @param array $params Custom localized scripts parameters.
	 *
	 * @return array
	 */
	function ava_tokommerce_scripts_params( $params = array() ) {
		return wp_parse_args(
			$params,
			array(
				'ajax_url'      => admin_url( 'ajax.php' ),
				'json'          => array(
					'country_url'     => add_query_arg( array( 't' => current_time( 'timestamp' ) ), AVA_TOKOMMERCE_URL . 'assets/data/country.json' ),
					'country_key'     => 'ava_tokommerce_country_data',
					'province_url'    => add_query_arg( array( 't' => current_time( 'timestamp' ) ), AVA_TOKOMMERCE_URL . 'assets/data/province.json' ),
					'province_key'    => 'ava_tokommerce_province_data',
					'city_url'        => add_query_arg( array( 't' => current_time( 'timestamp' ) ), AVA_TOKOMMERCE_URL . 'assets/data/city.json' ),
					'city_key'        => 'ava_tokommerce_city_data',
					'subdistrict_url' => add_query_arg( array( 't' => current_time( 'timestamp' ) ), AVA_TOKOMMERCE_URL . 'assets/data/subdistrict.json' ),
					'subdistrict_key' => 'ava_tokommerce_subdistrict_data',
				),
				'text'          => array(
					'placeholder' => array(
						'state'     => __( 'Provinsi', 'ava-tokommerce' ),
						'city'      => __( 'Kota/Kabupaten', 'ava-tokommerce' ),
						'address_2' => __( 'Kecamatan', 'ava-tokommerce' ),
					),
					'label'       => array(
						'state'     => __( 'Provinsi', 'ava-tokommerce' ),
						'city'      => __( 'Kota/Kabupaten', 'ava-tokommerce' ),
						'address_2' => __( 'Kecamatan', 'ava-tokommerce' ),
					),
				),
				'debug'         => ( 'yes' === get_option( 'woocommerce_shipping_debug_mode', 'no' ) ),
				'show_settings' => isset( $_GET['ava_tokommerce_settings'] ) && is_admin(), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'method_id'         => 'ava_tokommerce',
			'method_title'      => 'Ava Tokommerce',
			)
		);
	}
endif;