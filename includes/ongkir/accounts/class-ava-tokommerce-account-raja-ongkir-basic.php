<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ava_Tokommerce_Account_Raja_Ongkir_Basic extends Ava_Tokommerce_Account {

	/**
	 * Account priority
	 *
	 * @since 1.2.12
	 *
	 * @var int
	 */
	public $priority = 2;

	/**
	 * Account type
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $type = 'basic';

	/**
	 * Account label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'Basic';

	/**
	 * Account API URL
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $api_url = 'http://api.rajaongkir.com/basic';

	/**
	 * Account features
	 *
	 * @since 1.2.12
	 *
	 * @var array
	 */
	public $features = array(
		'subdistrict'       => false,
		'multiple_couriers' => true,
		'volumetric'        => false,
		'weight_over_30kg'  => false,
		'dedicated_server'  => false,
	);

	/**
	 * Parse API request parameters.
	 *
	 * @since 1.2.12
	 *
	 * @param array  $params   API request parameters to parse.
	 * @param string $endpoint API request endpoint.
	 *
	 * @return (array|WP_Error)
	 */
	public function api_request_parser( $params = array(), $endpoint = '' ) {
		if ( '/cost' === $endpoint ) {
			$this->api_request_params_requireds = array(
				'origin',
				'destination',
				'weight',
				'courier',
			);
		} elseif ( '/v2/internationalCost' === $endpoint ) {
			$this->api_request_params_requireds = array(
				'origin',
				'destination',
				'weight',
				'courier',
			);
		}

		return parent::api_request_parser( $params );
	}
}
