<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ava_Tokommerce_Courier_POS extends Ava_Tokommerce_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'pos';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'POS Indonesia';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://www.posindonesia.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic() {
		return array(
			'Surat Kilat Khusus'       => 'Surat Kilat Khusus',
			'Paketpos Biasa'           => 'Paketpos Biasa',
			'Paket Kilat Khusus'       => 'Paket Kilat Khusus',
			'Express Samedat Barang'   => 'Express Samedat Barang',
			'Express Samedat Dokumen'  => 'Express Samedat Dokumen',
			'Express Next Day Barang'  => 'Express Next Day Barang',
			'Express Next Day Dokumen' => 'Express Next Day Dokumen',
			'Paketpos Dangerous Goods' => 'Paketpos Dangerous Goods',
			'Paketpos Valuable Goods'  => 'Paketpos Valuable Goods',
			'Kargopos Ritel Train'     => 'Kargopos Ritel Train',
			'Kargopos Ritel Udara Dn'  => 'Kargopos Ritel Udara Dn',
			'Paket Jumbo Ekonomi'      => 'Paket Jumbo Ekonomi',
		);
	}

	/**
	 * Get courier services for international shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_international() {
		return array(
			'R LN'              => 'R LN',
			'EMS BARANG'        => 'EMS BARANG',
			'PAKETPOS CEPAT LN' => 'PAKETPOS CEPAT LN',
			'PAKETPOS BIASA LN' => 'PAKETPOS BIASA LN',
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

	/**
	 * Get courier account for international shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_account_international() {
		return array(
			'basic',
			'pro',
		);
	}
}
