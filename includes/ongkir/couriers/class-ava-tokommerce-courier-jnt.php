<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ava_Tokommerce_Courier_JNT extends Ava_Tokommerce_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'jnt';

	/**
	 * API Response ID
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $response_code = 'J&T';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'J&T Express';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://www.jet.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic() {
		return array(
			'EZ'  => 'Regular Service',
			'JSD' => 'Same Day Service',
		);
	}

	/**
	 * Get courier account for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_account_domestic() {
		return array(
			'pro',
		);
	}
}
