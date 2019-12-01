<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ava_Tokommerce_Account_Raja_Ongkir_Starter extends Ava_Tokommerce_Account {

	/**
	 * Account priority
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $priority = 1;

	/**
	 * Account type
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $type = 'starter';

	/**
	 * Account label
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $label = 'Starter';

	/**
	 * Account API URL
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $api_url = 'http://api.rajaongkir.com/starter';

	/**
	 * Account features
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $features = array(
		'subdistrict'       => false,
		'multiple_couriers' => false,
		'volumetric'        => false,
		'weight_over_30kg'  => false,
		'dedicated_server'  => false,
	);

	/**
	 * Parse API request parameters.
	 *
	 * @since 1.0.0
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
		}

		return parent::api_request_parser( $params );
	}
}
