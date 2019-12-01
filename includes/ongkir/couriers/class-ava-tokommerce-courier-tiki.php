<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ava_Tokommerce_Courier_TIKI extends Ava_Tokommerce_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'tiki';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'TIKI';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://tiki.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic() {
		return array(
			'REG' => 'Regular Service',
			'ECO' => 'Economy Service',
			'ONS' => 'Over Night Service',
			'SDS' => 'Same Day Service',
			'HDS' => 'Holiday Services',
			'TRC' => 'Trucking Service',
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
			'starter',
			'basic',
			'pro',
		);
	}
}
