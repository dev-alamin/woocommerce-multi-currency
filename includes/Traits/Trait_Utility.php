<?php 
namespace ADSWCS\Traits;
use \GeoIp2\Database\Reader;

trait Trait_Utility{

    private $store_location = null;
    
    /**
    * Get the visitor's country based on IP address.
    *
    * @return string The visitor's country code.
    */
    public function get_visitor_country() {
        
        if( function_exists( 'wc_get_base_location' ) ) {
            $this->store_location = wc_get_base_location()['country']; // Woocommerce settings country\state
        }

        $default_country = $this->store_location ? $this->store_location : 'US';

        // Get the visitor's IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Create a GeoIP2 reader
        $reader = new Reader( ADSWCS_PLUGIN_PATH . '/GeoLite2-Country/GeoLite2-Country.mmdb' ); // Replace with the actual path to the GeoIP2 database file
        try {
            // Perform a geolocation lookup
            $record = $reader->country($ip_address);
        
            $country_code = $record->country->isoCode;
        
            return $country_code;
        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            return 'AM';
        }
    }
}