<?php
/*
Plugin Name: GeotargetingWP custom switcher
Plugin URI: https://geotargetingwp.com
Description: Custom dropdown widget for cities in GeotargetingWP
Author: Damian Logghe
Version: 1.0
*/

use GeotWP\Record\GeotRecord;

/**
 * Class GeotSwitcher
 * will create a dropdown widget for changing cities
 */
class GeotSwitcher {
	/**
	 * GeotSwitcher constructor.
	 */
	public function __construct() {
		// check main plugin exists
		If ( ! class_exists( 'Geot' ) ) {
			return;
		}

		// required files and assets
		$this->includes();
		add_action( 'wp_enqueue_scripts', [ $this, 'assets' ] );

		//register widget
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
		// capture value
		add_filter( 'geot/cancel_query', [ $this, 'set_custom_data' ] );
	}

	/**
	 * Files includes
	 */
	private function includes() {
		require_once 'class-geot-dropdown-widget.php';
	}

	/**
	 * Enqueue assets
	 */
	private function assets() {
		wp_enqueue_script( 'geot-switcher', plugin_dir_url( __FILE__ ) . 'switcher.js', [ 'jquery', 'geot-js' ], '1.0', true );
	}

	/**
	 * Register widget into WP
	 */
	function register_widget() {
		register_widget( 'GeotS_Widget' );
	}

	/**
	 * Check if switcher cookie exists and modify data
	 */
	function set_custom_data() {

		// if no cookie or not a valid city continue with request to API
		if ( empty( $_COOKIE['geot_switcher'] ) || ! in_array( $_COOKIE['geot_switcher'], array_keys( GeotSwitcher::cities() ) ) ) {
			return false;
		}

		$city = $_COOKIE['geot_switcher'];

		// on this example we hardcoded the country and states but you can make it conditional based on your city
		$data = [
			'country'     => 'United States',
			'country_iso' => 'US',
			'state'       => 'Florida',
			'state_iso'   => 'FL',
			'city'        => $city,
			'zip'         => GeotSwitcher::cities()[ $city ],
		];
		// return formatted object to the plugin
		return $this->formatter($data);
	}

	/**
	 * Valid cities used in dropdown
	 * @return array of cities and zip codes
	 */
	public static function cities() {
		return [
			'Miami'      => '33166',
			'Orlando'    => '33167',
			'Tampa'      => '33168',
			'Biscayne'   => '33169',
			'Palm Beach' => '33170',
		];
	}

	private function formatter( $data ) {

		$state           = new \stdClass;
		$state->names    = [ $data['state'] ];
		$state->iso_code = $data['state_iso'];

		$country           = new \stdClass;
		$country->names    = [ $data['country'] ];
		$country->iso_code = $data['country_iso'];

		$continent        = new \stdClass;
		$continent->names = '';

		$city        = new \stdClass;
		$city->names = [ $data['city'] ];
		$city->zip   = $data['zip'];

		$geolocation                  = new \stdClass();
		$geolocation->accuracy_radius = '';
		$geolocation->longitude       = '';
		$geolocation->latitude        = '';
		$geolocation->time_zone       = '';

		return (object) [
			'country'     => $country,
			'city'        => $city,
			'state'       => $state,
			'continent'   => $continent,
			'geolocation' => $geolocation,
		];
	}
}
add_action( 'plugins_loaded', function () {
	new GeotSwitcher();
});
