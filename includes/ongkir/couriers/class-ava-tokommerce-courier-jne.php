<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ava_Tokommerce_Courier_JNE extends Ava_Tokommerce_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $code = 'jne';

	/**
	 * Courier Label
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $label = 'JNE';

	/**
	 * Courier Website
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $website = 'http://www.jne.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_services_domestic() {
		return array(
			'CTC'    => 'City Courier',
			'CTCYES' => 'City Courier YES',
			'OKE'    => 'Ongkos Kirim Ekonomis',
			'REG'    => 'Layanan Reguler',
			'YES'    => 'Yakin Esok Sampai',
		);
	}

	/**
	 * Get courier services for international shipping
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_services_international() {
		return array(
			'INTL' => 'INTL',
		);
	}

	/**
	 * Get courier account for domestic shipping
	 *
	 * @since 1.0.0
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

	/**
	 * Get courier account for international shipping
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_account_international() {
		return array(
			'pro',
		);
	}
}
