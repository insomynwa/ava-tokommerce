<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Tokommerce_Raja_Ongkir' ) ) {
    class Ava_Tokommerce_Raja_Ongkir {

        private static $instance = null;

        private $api_key = '';
        // private $origin_province = null;
        // private $origin_city = null;
        // private $origin_subdistrict = null;
        // private $weigth_base = 0;

        public function set_api_key( $api_key ) {
            $this->api_key = $api_key;
        }

        public static function get_instance() {
            if ( null === self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        public function get_provinces() {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [ "key: {$this->api_key}" ],
                    //CURLOPT_HTTPHEADER => [ "key: f0af91af29253d03e9c31f4ee68bb439" ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                // var_dump("cURL Error #:" , $err);
            } else {
                $results_array = json_decode( $response, true );
                $rajaongkir = $results_array['rajaongkir'];
                $provinces = $this->formating_result( 'province', $rajaongkir['results'] );
                return $provinces;
            }
        }

        public function get_cities_by_province_id( $province_id ) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.rajaongkir.com/starter/city?province={$province_id}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [ "key: {$this->api_key}" ],
                    //CURLOPT_HTTPHEADER => [ "key: f0af91af29253d03e9c31f4ee68bb439" ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                // var_dump("cURL Error #:" , $err);
            } else {
                $results_array = json_decode( $response, true );
                $rajaongkir = $results_array[ 'rajaongkir' ];
                $cities = $this->formating_result( 'city', $rajaongkir[ 'results' ] );
                return $cities;
            }
        }

        /**
         * Get subdistricts by city id
         *
         * @param number $city
         * @return array
         */
        public function get_subdistricts_by_city_id( $city_id ) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city={$city_id}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [ "key: {$this->api_key}" ],
                    //CURLOPT_HTTPHEADER => [ "key: f0af91af29253d03e9c31f4ee68bb439" ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                // var_dump("cURL Error #:" , $err);
            } else {
                $results_array = json_decode( $response, true );
                $rajaongkir = $results_array[ 'rajaongkir' ];
                $cities = $this->formating_result( 'subdistrict', $rajaongkir[ 'results' ] );
                return $cities;
            }
        }

        private function formating_result( $req_type, $args = [] ){
            $init_label = '';
            $init_key = '';
            $init_val_label = '';

            if ( $req_type == 'province' ) {
                $init_label = esc_html__( 'Provinsi', 'ava-tokommerce' );
                $init_key = 'province_id';
                $init_val_label = 'province';
            }else if ( $req_type == 'city' ) {
                $init_label = esc_html__( 'Kota/Kabupaten', 'ava-tokommerce' );
                $init_key = 'city_id';
                $init_val_label = 'city_name';
            }else if ( $req_type == 'subdistrict' ) {
                $init_label = esc_html__( 'Kecamatan', 'ava-tokommerce' );
                $init_key = 'subdistrict_id';
                $init_val_label = 'subdistrict_name';
            }
            $arr_result = [
                '0' => $init_label
            ];
            if ( null !== $args ) {
                foreach ( $args as $arg => $val ) {
                    // var_dump($val['province']);die;
                    $arr_result[ $val[ $init_key ] ] = $req_type == 'city' ? $val[ 'type' ] . ' ' . $val[ $init_val_label ] : $val[ $init_val_label ];
                }
            }

            return $arr_result;
        }
    }
}

if ( ! function_exists( 'ava_tokommerce_raja_ongkir' ) ) {
    function ava_tokommerce_raja_ongkir() {
        return Ava_Tokommerce_Raja_Ongkir::get_instance();
    }
}