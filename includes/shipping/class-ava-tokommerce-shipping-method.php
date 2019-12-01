<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ava_Tokommerce_Shipping_Method' ) ) {
	class Ava_Tokommerce_Shipping_Method extends WC_Shipping_Method {

		private $api;
		private $posted_field_values;

		public function __construct( $instance_id = 0 ) {
			$this->api = new Ava_Tokommerce_Ongkir_API();
			$this->id = ava_tokommerce_shipping()::$shipping_method_id;
			$this->instance_id = absint( $instance_id );
			$this->method_title = ava_tokommerce_shipping()::$shipping_method_label;
			$this->method_description = __( 'Ava Tokommerce Description', 'ava-tokommerce' );
			$this->title = ava_tokommerce_shipping()::$shipping_method_label;

			$this->supports = [
				// 'settings',
				'shipping-zones',
				'instance-settings',
				'instance-settings-modal',
			];

			$this->init();
		}

		public function init() {
			$this->init_form_fields();
			$this->init_settings();

			foreach ( $this->instance_form_fields as $field_id => $field ) {
				$type = array_key_exists( 'type', $field ) ? $field['type'] : false;
				if ( ! $type || in_array( $type, array( 'title' ), true ) ) {
					continue;
				}

				$default = array_key_exists( 'default', $field ) ? $field['default'] : null;
				$option  = $this->get_option( $field_id, $default );
				// var_dump($option);
				$this->{$field_id} = $option;
			}
			// var_dump($this->instance_form_fields);die;
			$api_key = isset( $this->api_key ) ? $this->api_key : '';
			$this->api->set_option( 'api_key', $api_key );

			$account_type = isset( $this->account_type ) ? $this->account_type : '';
			$this->api->set_option( 'account_type', $account_type );

			// add_action( 'woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ] );
		}

		public function init_form_fields(){
			if( 'ID' !== WC()->countries->get_base_country() ) {
				$this->instance_form_fields = [
					'error' => [
						'title'			=> __( 'Error', 'ava-tokommerce' ),
						'type'			=> 'title',
						'description'	=> __( 'Plugin ini hanya untuk toko di Indonesia', 'ava-tokommerce' )
					],
				];

				return;
			}

			$settings = array(
				'origin_province'       => array(
					'title' => __( 'Provinsi Pengirim', 'ava-tokommerce' ),
					'type'  => 'origin',
				),
				'origin_city'           => array(
					'title' => __( 'Kota/Kab. Pengirim', 'ava-tokommerce' ),
					'type'  => 'origin',
				),
				'origin_subdistrict'    => array(
					'title' => __( 'Kecamatan Pengirim', 'ava-tokommerce' ),
					'type'  => 'origin',
				),
				'tax_status'            => array(
					'title'   => __( 'Tax Status', 'ava-tokommerce' ),
					'type'    => 'select',
					'default' => 'none',
					'options' => array(
						'taxable' => __( 'Taxable', 'ava-tokommerce' ),
						'none'    => _x( 'None', 'Tax status', 'ava-tokommerce' ),
					),
				),
				'show_eta'              => array(
					'title'       => __( 'Show ETA', 'ava-tokommerce' ),
					'label'       => __( 'Yes', 'ava-tokommerce' ),
					'type'        => 'checkbox',
					'description' => __( 'Show estimated time of arrival during checkout.', 'ava-tokommerce' ),
				),
				'base_weight'           => array(
					'title'             => __( 'Base Cart Contents Weight (gram)', 'ava-tokommerce' ),
					'type'              => 'number',
					'description'       => __( 'The base cart contents weight will be calculated. If the value is blank or zero, the couriers list will not displayed when the actual cart contents weight is empty.', 'ava-tokommerce' ),
					'custom_attributes' => array(
						'min'  => '0',
						'step' => '100',
					),
				),
				'api_key'               => array(
					'title'       => __( 'RajaOngkir API Key', 'ava-tokommerce' ),
					'type'        => 'text',
					'placeholder' => '',
					'description' => __( '<a href="http://www.rajaongkir.com" target="_blank">Click here</a> to get RajaOngkir.com API Key. It is FREE.', 'ava-tokommerce' ),
					'default'     => '',
				),
				'account_type'          => array(
					'title'             => __( 'RajaOngkir Account Type', 'ava-tokommerce' ),
					'type'              => 'account_type',
					'default'           => 'starter',
					'options'           => array(),
					'custom_attributes' => array(
						'data-accounts' => wp_json_encode( $this->api->get_accounts( true ) ),
						'data-couriers' => wp_json_encode(
							array(
								'domestic'      => $this->api->get_couriers( 'domestic', 'all', true ),
								'international' => $this->api->get_couriers( 'international', 'all', true ),
							)
						),
					),
				),
				'volumetric_calculator' => array(
					'title'       => __( 'Volumetric Converter', 'ava-tokommerce' ),
					'label'       => __( 'Enable', 'ava-tokommerce' ),
					'type'        => 'checkbox',
					'description' => __( 'Convert volumetric to weight before send request to API server.', 'ava-tokommerce' ),
				),
				'volumetric_divider'    => array(
					'title'             => __( 'Volumetric Converter Divider', 'ava-tokommerce' ),
					'type'              => 'number',
					'description'       => __( 'The formula to convert volumetric to weight: Width x Length x Height in centimetres / Divider', 'ava-tokommerce' ),
					'custom_attributes' => array(
						'min'  => '0',
						'step' => '100',
					),
					'default'           => '6000',
				),
				'domestic'              => array(
					'title' => __( 'Domestic Shipping', 'ava-tokommerce' ),
					'type'  => 'couriers_list',
				),
				'international'         => array(
					'title' => __( 'International Shipping', 'ava-tokommerce' ),
					'type'  => 'couriers_list',
				),
			);

			$features = array(
				'domestic'          => __( 'Domestic Couriers', 'ava-tokommerce' ),
				'international'     => __( 'International Couriers', 'ava-tokommerce' ),
				'multiple_couriers' => __( 'Multiple Couriers', 'ava-tokommerce' ),
				'subdistrict'       => __( 'Calculate Subdistrict', 'ava-tokommerce' ),
				'volumetric'        => __( 'Calculate Volumetric', 'ava-tokommerce' ),
				'weight_over_30kg'  => __( 'Weight Over 30kg', 'ava-tokommerce' ),
				'dedicated_server'  => __( 'Dedicated Server', 'ava-tokommerce' ),
			);

			$accounts = $this->api->get_accounts();

			foreach ( $features as $feature_key => $feature_label ) {
				$settings['account_type']['features'][ $feature_key ]['label'] = $feature_label;

				foreach ( $accounts as $type => $account ) {
					if ( in_array( $feature_key, array( 'domestic', 'international' ), true ) ) {
						$settings['account_type']['features'][ $feature_key ]['value'][ $type ] = count( $this->api->get_couriers( $feature_key, $type ) );
					} else {
						$settings['account_type']['features'][ $feature_key ]['value'][ $type ] = $account->feature_enable( $feature_key ) ? __( 'Yes', 'ava-tokommerce' ) : __( 'No', 'ava-tokommerce' );
					}
				}
			}

			$this->instance_form_fields = $settings;
		}

		public function generate_origin_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array(),
			);

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
				</th>
				<td class="forminp">
					<fieldset style="max-width: 50%;min-width: 250px;">
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
						<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</fieldset>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		public function generate_account_type_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array(),
				'options'           => array(),
				'features'          => array(),
			);

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
				</th>
				<td class="forminp">
					<input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
					<div class="ava-tokommerce-account-features-wrap">
						<table id="ava-tokommerce-account-features" class="ava-tokommerce-account-features form-table">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<?php foreach ( $this->api->get_accounts() as $account ) { ?>
										<th class="ava-tokommerce-account-features-col-<?php echo esc_attr( $account->get_type() ); ?>"><a href="https://rajaongkir.com/dokumentasi" target="_blank"><?php echo esc_html( $account->get_label() ); ?></a></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( (array) $data['features'] as $feature_key => $feature ) : ?>
								<tr>
									<th><?php echo esc_html( $feature['label'] ); ?></th>
									<?php foreach ( $feature['value'] as $account_type => $feature_value ) : ?>
										<td class="ava-tokommerce-account-features-col-<?php echo esc_attr( $account_type ); ?>"><?php echo esc_html( $feature_value ); ?></td>
									<?php endforeach; ?>
								</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<?php foreach ( array_keys( $feature['value'] ) as $account_type ) : ?>
										<td class="ava-tokommerce-account-features-col-<?php echo esc_attr( $account_type ); ?>" data-title="<?php echo esc_attr( $this->api->get_account( $account_type )->get_label() ); ?>">
											<input type="checkbox" value="<?php echo esc_attr( $account_type ); ?>" id="<?php echo esc_attr( $field_key ); ?>--<?php echo esc_attr( $account_type ); ?>" class="ava-tokommerce-account-type" <?php checked( $account_type, $this->get_option( $key ) ); ?> <?php disabled( $account_type, $this->get_option( $key ) ); ?>>
										</td>
									<?php endforeach; ?>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}

		public function generate_couriers_list_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title' => '',
				'class' => '',
			);

			$data = wp_parse_args( $data, $defaults );

			$couriers = $this->api->get_couriers( $key, 'all', true );

			uasort( $couriers, array( $this, 'sort_couriers_list_' . $key ) );

			$selected = $this->{$key};

			ob_start();
			?>
			<?php if ( 'domestic' === $key ) : ?>
			</table>
			<table class="form-table">
				</tr>
				<?php endif; ?>
				<td class="ava-tokommerce-couriers-wrap ava-tokommerce-couriers-wrap--<?php echo esc_attr( $key ); ?>">
					<h2 class="wc-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h2>
					<ul class="ava-tokommerce-couriers">
						<?php
						$i = 0;
						foreach ( $couriers as $courier_id => $courier ) :
							if ( empty( $courier['services'] ) ) :
								continue;
							endif;
							?>
							<li class="ava-tokommerce-couriers-item ava-tokommerce-couriers-item--<?php echo esc_attr( $key ); ?>--<?php echo esc_attr( $courier_id ); ?>" data-id="<?php echo esc_attr( $courier_id ); ?>" data-zone="<?php echo esc_attr( $key ); ?>">
								<div class="ava-tokommerce-couriers-item-inner">
									<div class="ava-tokommerce-couriers-item-info">
										<label>
											<input type="checkbox" id="<?php echo esc_attr( $field_key ); ?>_<?php echo esc_attr( $courier_id ); ?>_toggle" class="ava-tokommerce-service ava-tokommerce-service--bulk" <?php checked( ( isset( $selected[ $courier_id ] ) && count( $selected[ $courier_id ] ) ? 1 : 0 ), 1 ); ?>>
											<?php echo wp_kses_post( $courier['label'] ); ?> (<span class="ava-tokommerce-couriers--selected"><?php echo esc_html( ( isset( $selected[ $courier_id ] ) ? count( $selected[ $courier_id ] ) : 0 ) ); ?></span> / <span class="ava-tokommerce-couriers--availabe"><?php echo esc_html( count( $courier['services'] ) ); ?></span>)
										</label>
										<div class="ava-tokommerce-couriers-item-info-toggle">
											<a href="#" class="ava-tokommerce-couriers-toggle" title="<?php esc_attr_e( 'Toggle', 'ava-tokommerce' ); ?>"><span class="dashicons dashicons-admin-generic"></span></a>
										</div>
										<?php
										$courier_website = wp_parse_url( $courier['website'] );

										if ( isset( $courier_website['host'] ) ) {
											?>
										<div class="ava-tokommerce-couriers-item-info-link"><a href="<?php echo esc_attr( $courier['website'] ); ?>" target="blank"><?php echo esc_html( $courier_website['host'] ); ?></a></div>
											<?php
										}
										?>
									</div>
									<ul class="ava-tokommerce-services">
										<?php
										foreach ( $courier['services'] as $index => $service ) :
											$service_label = $index !== $service ? wp_sprintf( '%1$s - %2$s', $index, $service ) : $service;
											?>
										<li class="ava-tokommerce-services-item">
											<label>
												<input type="checkbox" class="ava-tokommerce-service ava-tokommerce-service--single" id="<?php echo esc_attr( $field_key ); ?>_<?php echo esc_attr( $courier_id ); ?>_<?php echo esc_attr( $index ); ?>" name="<?php echo esc_attr( $field_key ); ?>[]" value="<?php echo esc_attr( $courier_id ); ?>_<?php echo esc_attr( $index ); ?>" <?php checked( ( isset( $selected[ $courier_id ] ) && in_array( $index, $selected[ $courier_id ], true ) ? $index : false ), $index ); ?>><?php echo wp_kses_post( $service_label ); ?>
											</label>
										</li>
											<?php
										endforeach;
										?>
									</ul>
								</div>
							</li>
							<?php
							$i++;
						endforeach;
						?>
					</ul>
				</td>
				<?php if ( 'international' === $key ) : ?>
				</tr>
			</table>
			<table class="form-table">
			<?php endif; ?>
			<?php
			return ob_get_clean();
		}

		public function validate_api_key_field( $key, $value ) {
			if ( empty( $value ) ) {
				throw new Exception( __( 'API Key is required.', 'ava-tokommerce' ) );
			}

			$account_type = $this->validate_account_type_field( 'account_type', $this->posted_field_value( 'account_type' ) );
			if ( $account_type ) {
				$this->api->set_option( 'api_key', $value );
				$this->api->set_option( 'account_type', $account_type );

				$results = $this->api->validate_account();

				if ( ! $results ) {
					throw new Exception( __( 'API Key or Account type is invalid.', 'ava-tokommerce' ), 1 );
				}

				foreach ( $results as $result ) {
					if ( is_wp_error( $result ) ) {
						throw new Exception( $result->get_error_message(), 1 );
					}
				}
			}

			return $value;
		}

		public function validate_account_type_field( $key, $value ) {
			if ( empty( $value ) ) {
				throw new Exception( __( 'Account type field is required.', 'ava-tokommerce' ) );
			}

			if ( ! $this->api->get_account( $value ) ) {
				throw new Exception( __( 'Account type field is invalid.', 'ava-tokommerce' ) );
			}

			return $value;
		}

		public function validate_origin_field( $key, $value ) {
			if ( empty( $value ) ) {
				// Translators: Shipping origin location type.
				throw new Exception( wp_sprintf( __( 'Shipping origin %s field is required.', 'ava-tokommerce' ), str_replace( 'origin_', '', $key ) ) );
			}
			return $value;
		}

		public function validate_couriers_list_field( $key, $value ) {
			if ( is_string( $value ) ) {
				$value = array_map( 'trim', explode( ',', $value ) );
			}

			// Format the value as associative array courier => services.
			if ( $value && is_array( $value ) ) {
				$format_value = array();

				foreach ( $value as $val ) {
					$parts = explode( '_', $val );

					if ( count( $parts ) === 2 ) {
						$format_value[ $parts[0] ][] = $parts[1];
					}
				}

				$value = $format_value;
			}

			if ( $value ) {
				$field   = $this->instance_form_fields[ $key ];
				$account = $this->api->get_account( $this->posted_field_value( 'account_type' ) );

				if ( ! $account ) {
					throw new Exception( __( 'Account type field is invalid.', 'ava-tokommerce' ) );
				}

				if ( ! $account->feature_enable( 'multiple_couriers' ) && count( $value ) > 1 ) {
					// Translators: %1$s Shipping zone name, %2$s Account label.
					throw new Exception( wp_sprintf( __( '%1$s Shipping: Account type %2$s is not allowed to select multiple couriers.', 'ava-tokommerce' ), $field['title'], $account->get_label( 'label' ) ) );
				}

				$not_allowed = array_diff_key( $value, $this->api->get_couriers( $key, $account->get_type() ) );

				if ( ! empty( $not_allowed ) ) {
					// Translators: %1$s Shipping zone name, %2$s Account label, %3$s Couriers name.
					throw new Exception( wp_sprintf( __( '%1$s Shipping: Account type %2$s is not allowed to select courier %3$s.', 'ava-tokommerce' ), $field['title'], $account->get_label( 'label' ), strtoupper( implode( ', ', array_keys( $not_allowed ) ) ) ) );
				}
			}

			return $value;
		}

		public function calculate_shipping( $package = array() ) {
			try {
				$api_request_params = $this->calculate_shipping_api_request_params( $package );

				if ( is_wp_error( $api_request_params ) ) {
					throw new Exception( $api_request_params->get_error_message() );
				}

				$cache_key = $this->generate_cache_key( $api_request_params );

				if ( $this->is_enable_cache() ) {
					$this->show_debug(
						wp_json_encode(
							array(
								'calculate_shipping.$cache_key' => $cache_key,
							)
						)
					);
				}

				$results = $this->is_enable_cache() ? get_transient( $cache_key ) : false;

				if ( false === $results ) {
					if ( 'domestic' === $api_request_params['zone'] ) {
						$results = $this->api->calculate_shipping( $api_request_params );
					} else {
						$results = $this->api->calculate_shipping_international( $api_request_params );
					}

					$results = apply_filters( 'ava_tokommerce_shipping_results', $results, $package, $this );

					if ( $results && ! is_wp_error( $results ) && $this->is_enable_cache() ) {
						set_transient( $cache_key, $results, HOUR_IN_SECONDS ); // Store response data for 1 hour.
					}
				}

				$this->show_debug(
					wp_json_encode(
						array(
							'calculate_shipping.$results' => $results,
						)
					)
				);

				if ( is_wp_error( $results ) ) {
					throw new Exception( $results->get_error_message() );
				}

				if ( ! $results ) {
					throw new Exception( __( 'No couriers data found', 'ava-tokommerce' ) );
				}

				if ( ! is_array( $results ) ) {
					// translators: %s Encoded data response.
					throw new Exception( wp_sprintf( __( 'Couriers data is invalid: %s', 'ava-tokommerce' ), wp_json_encode( $results ) ) );
				}

				$allowed_services = isset( $this->{$api_request_params['zone']} ) ? $this->{$api_request_params['zone']} : array();

				$this->show_debug(
					wp_json_encode(
						array(
							'calculate_shipping.$allowed_services' => $allowed_services,
						)
					)
				);

				foreach ( $results['parsed'] as $result_key => $result ) {
					if ( ! isset( $allowed_services[ $result['courier'] ] ) ) {
						continue;
					}

					if ( ! in_array( $result['service'], $allowed_services[ $result['courier'] ], true ) ) {
						continue;
					}

					$rate_label = wp_sprintf( '%s - %s', strtoupper( $result['courier'] ), $result['service'] );

					if ( 'yes' === $this->show_eta && $result['etd'] ) {
						$rate_label = wp_sprintf( '%1$s (%2$s)', $rate_label, $result['etd'] );
					}

					$rate_label = apply_filters( 'ava_tokommerce_shipping_rate_label', $rate_label, $result, $package, $this );

					$this->add_rate(
						array(
							'id'        => $this->get_rate_id( $result['courier'] . ':' . $result['service'] ),
							'label'     => $rate_label,
							'cost'      => $result['cost'],
							'meta_data' => array(
								'_ava_tokommerce_data' => $result,
							),
						)
					);
				}
			} catch ( Exception $e ) {
				$this->show_debug( $e->getMessage() );
			}
		}

		private function get_origin_info( $shipping_address = array() ) {
			if ( ! isset( $shipping_address['country'] ) ) {
				return false;
			}

			$domestic = 'ID' === $shipping_address['country'];

			if ( $domestic ) {
				$account = $this->api->get_account( $this->account_type );

				return array(
					'origin'     => $account && $account->feature_enable( 'subdistrict' ) ? $this->origin_subdistrict : $this->origin_city,
					'originType' => $account && $account->feature_enable( 'subdistrict' ) ? 'subdistrict' : 'city',
				);
			}

			return array(
				'origin' => $this->origin_city,
			);
		}

		private function calculate_shipping_api_request_params( $package = array() ) {
			try {
				$domestic = isset( $package['destination']['country'] ) && 'ID' === $package['destination']['country'];

				/**
				 * Shipping origin info.
				 *
				 * @since 1.2.9
				 *
				 * @param array $origin_info Original origin info.
				 * @param array $package Current order package data.
				 *
				 * @return array
				 */
				$origin_info = apply_filters( 'ava_tokommerce_shipping_origin_info', $this->get_origin_info( $package['destination'] ), $package );

				$this->show_debug(
					wp_json_encode(
						array(
							'api_request_params.$origin_info' => $origin_info,
						)
					)
				);

				if ( empty( $origin_info ) ) {
					throw new Exception( __( 'Shipping origin info is empty or invalid', 'ava-tokommerce' ) );
				}

				/**
				 * Shipping destination info.
				 *
				 * @since 1.2.9
				 *
				 * @param array $destination_info Original destination info.
				 * @param array $package Current order package data.
				 *
				 * @return array
				 */
				$destination_info = apply_filters( 'ava_tokommerce_shipping_destination_info', $this->get_destination_info( $package['destination'] ), $package );

				$this->show_debug(
					wp_json_encode(
						array(
							'api_request_params.$destination_info' => $destination_info,
						)
					)
				);

				if ( ! $destination_info || ! array_filter( $destination_info ) ) {
					throw new Exception( __( 'Shipping destination info is empty or invalid', 'ava-tokommerce' ) );
				}

				/**
				 * Shipping dimension & weight info.
				 *
				 * @since 1.2.9
				 *
				 * @param array $dimension_weight Original dimension & weight info.
				 * @param array $package Current order package data.
				 *
				 * @return array
				 */
				$dimension_weight = apply_filters( 'ava_tokommerce_shipping_dimension_weight', $this->get_dimension_weight( $package['contents'] ), $package );

				$this->show_debug(
					wp_json_encode(
						array(
							'api_request_params.$dimension_weight' => $dimension_weight,
						)
					)
				);

				if ( ! $dimension_weight || ! array_filter( $dimension_weight ) ) {
					throw new Exception( __( 'Cart weight pr dimension is empty or invalid', 'ava-tokommerce' ) );
				}

				$courier = $domestic ? array_keys( (array) $this->domestic ) : array_keys( (array) $this->international );

				$this->show_debug(
					wp_json_encode(
						array(
							'api_request_params.$courier' => $courier,
						)
					)
				);

				if ( ! $courier || ! array_filter( $courier ) ) {
					throw new Exception( __( 'No couriers selected', 'ava-tokommerce' ) );
				}

				return array_merge(
					$origin_info,
					$destination_info,
					$dimension_weight,
					array(
						'courier' => $courier,
						'zone'    => $domestic ? 'domestic' : 'international',
					)
				);
			} catch ( Exception $e ) {
				return new WP_Error( 'api_request_params_error', $e->getMessage() );
			}
		}

		private function get_destination_info( $shipping_address = array() ) {
			if ( empty( $shipping_address['country'] ) ) {
				return false;
			}

			$domestic = 'ID' === $shipping_address['country'];

			if ( ! $domestic ) {
				$country = ava_tokommerce_get_json_data(
					'country',
					array(
						'country_code' => $shipping_address['country'],
					)
				);

				if ( ! $country ) {
					return false;
				}

				return array(
					'destination' => $country['country_id'],
				);
			}

			// Bail early when the state or city info is empty.
			if ( empty( $shipping_address['country'] ) || empty( $shipping_address['city'] ) ) {
				return false;
			}

			// Get province ID data.
			$province = ava_tokommerce_get_json_data(
				'province',
				array(
					'code' => $shipping_address['state'],
				)
			);

			if ( ! $province || ! isset( $province['province_id'] ) ) {
				return false;
			}

			// Get city ID data.
			$city_parts = explode( ' ', $shipping_address['city'] );
			$city_type  = $city_parts[0];
			$city_name  = implode( ' ', array_slice( $city_parts, 1 ) );

			$city = ava_tokommerce_get_json_data(
				'city',
				array(
					'type'        => $city_type,
					'city_name'   => $city_name,
					'province_id' => $province['province_id'],
				)
			);

			if ( ! $city || ! isset( $city['city_id'] ) ) {
				return false;
			}

			// Get current API account.
			$account = $this->api->get_account( $this->account_type );

			if ( $account && $account->feature_enable( 'subdistrict' ) && ! empty( $shipping_address['address_2'] ) ) {
				// Get subdistrict ID data.
				$subdistrict = ava_tokommerce_get_json_data(
					'subdistrict',
					array(
						'subdistrict_name' => $shipping_address['address_2'],
						'city_id'          => $city['city_id'],
						'province_id'      => $province['province_id'],
					)
				);

				if ( $subdistrict && isset( $subdistrict['subdistrict_id'] ) ) {
					return array(
						'destination'     => $subdistrict['subdistrict_id'],
						'destinationType' => 'subdistrict',
					);
				}
			}

			return array(
				'destination'     => $city['city_id'],
				'destinationType' => 'city',
			);
		}

		private function get_dimension_weight( $contents ) {
			$data = array(
				'width'  => 0,
				'length' => 0,
				'height' => 0,
				'weight' => 0,
			);

			$length = array();
			$width  = array();
			$height = array();
			$weight = array();

			foreach ( $contents as $item ) {
				// Validate cart item quantity value.
				$item_quantity = absint( $item['quantity'] );
				if ( ! $item_quantity ) {
					continue;
				}

				// Validate cart item weight value.
				$item_weight = is_numeric( $item['data']->get_weight() ) ? $item['data']->get_weight() : 0;
				array_push( $weight, $item_weight * $item_quantity );

				// Validate cart item width value.
				$item_width = is_numeric( $item['data']->get_width() ) ? $item['data']->get_width() : 0;
				array_push( $width, $item_width * 1 );

				// Validate cart item length value.
				$item_length = is_numeric( $item['data']->get_length() ) ? $item['data']->get_length() : 0;
				array_push( $length, $item_length * 1 );

				// Validate cart item height value.
				$item_height = is_numeric( $item['data']->get_height() ) ? $item['data']->get_height() : 0;
				array_push( $height, $item_height * $item_quantity );
			}

			$data['weight'] = wc_get_weight( array_sum( $weight ), 'g' );

			// Convert the volumetric to weight.
			$account = $this->api->get_account( $this->account_type );

			if ( $account && $account->feature_enable( 'volumetric' ) ) {
				$width  = wc_get_dimension( max( $width ), 'cm' );
				$length = wc_get_dimension( max( $length ), 'cm' );
				$height = wc_get_dimension( array_sum( $height ), 'cm' );

				$data['width']  = $width;
				$data['length'] = $length;
				$data['height'] = $height;

				if ( 'yes' === $this->volumetric_calculator && $this->volumetric_divider ) {
					$data['weight'] = max( $data['weight'], $this->convert_volumetric( $width, $length, $height ) );
				}
			}

			// Set the package weight to based on base_weight setting value.
			if ( absint( $this->base_weight ) && $data['weight'] < absint( $this->base_weight ) ) {
				$data['weight'] = absint( $this->base_weight );
			}

			return $data;
		}

		public function convert_volumetric( $width, $length, $height ) {
			return ceil( ( ( $width * $length * $height ) / $this->volumetric_divider ) * 1000 );
		}

		private function is_enable_cache() {
			return defined( 'AVA_TOKOMMERCE_ENABLE_CACHE' ) ? AVA_TOKOMMERCE_ENABLE_CACHE : true;
		}

		private function generate_cache_key( $api_request_params = array() ) {
			$cache_keys = array();

			foreach ( array_keys( $this->instance_form_fields ) as $cache_key ) {
				$cache_keys[ $cache_key ] = $this->get_option( $cache_key );
			}

			return $this->id . '_' . $this->instance_id . '_' . WC()->cart->get_cart_hash() . '_' . md5(
				wp_json_encode(
					array_merge(
						$api_request_params,
						$cache_keys
					)
				)
			);
		}

		protected function sort_couriers_list_domestic( $a, $b ) {
			$priority = array();

			$letter_index = range( 'a', 'z' );
			$a_code_index = is_numeric( $a['code'][0] ) ? $a['code'][0] : ( array_search( strtolower( $a['code'][0] ), $letter_index, true ) + 10 );
			$b_code_index = is_numeric( $b['code'][0] ) ? $b['code'][0] : ( array_search( strtolower( $b['code'][0] ), $letter_index, true ) + 10 );

			if ( empty( $this->domestic ) ) {
				if ( $a_code_index === $b_code_index ) {
					return 0;
				}

				return ( $a_code_index > $b_code_index ) ? 1 : -1;
			}

			foreach ( array_keys( $this->domestic ) as $index => $courier ) {
				$priority[ $courier ] = $index;
			}

			$al = isset( $priority[ $a['code'] ] ) ? $priority[ $a['code'] ] : ( count( $this->domestic ) + $a_code_index );
			$bl = isset( $priority[ $b['code'] ] ) ? $priority[ $b['code'] ] : ( count( $this->domestic ) + $b_code_index );

			if ( $al === $bl ) {
				return 0;
			}

			return ( $al > $bl ) ? 1 : -1;
		}

		protected function sort_couriers_list_international( $a, $b ) {
			$priority = array();

			$letter_index = range( 'a', 'z' );
			$a_code_index = is_numeric( $a['code'][0] ) ? $a['code'][0] : ( array_search( strtolower( $a['code'][0] ), $letter_index, true ) + 10 );
			$b_code_index = is_numeric( $b['code'][0] ) ? $b['code'][0] : ( array_search( strtolower( $b['code'][0] ), $letter_index, true ) + 10 );

			if ( empty( $this->international ) ) {
				if ( $a_code_index === $b_code_index ) {
					return 0;
				}

				return ( $a_code_index > $b_code_index ) ? 1 : -1;
			}

			foreach ( array_keys( $this->international ) as $index => $courier ) {
				$priority[ $courier ] = $index;
			}

			$al = isset( $priority[ $a['code'] ] ) ? $priority[ $a['code'] ] : ( count( $this->international ) + $a_code_index );
			$bl = isset( $priority[ $b['code'] ] ) ? $priority[ $b['code'] ] : ( count( $this->international ) + $b_code_index );

			if ( $al === $bl ) {
				return 0;
			}

			return ( $al > $bl ) ? 1 : -1;
		}

		private function posted_field_value( $key, $default = null ) {
			if ( is_null( $this->posted_field_values ) ) {
				$this->posted_field_values = $this->get_post_data();
			}

			$field_key = $this->get_field_key( $key );

			return array_key_exists( $field_key, $this->posted_field_values ) ? $this->posted_field_values[ $field_key ] : $default;
		}

		private function show_debug( $message, $notice_type = 'notice' ) {
			$debug_mode = 'yes' === get_option( 'woocommerce_shipping_debug_mode', 'no' );

			if ( ! $debug_mode || ! current_user_can( 'manage_options' ) || wc_has_notice( $message ) || ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX ) ) {
				return;
			}

			wc_add_notice( ( 'ava_tokommerce' . ' : ' . $message ), $notice_type );
		}

	}
}
