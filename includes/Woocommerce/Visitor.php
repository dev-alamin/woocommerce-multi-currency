<?php 
namespace ADSWCS\Woocommerce;
use ADSWCS\Admin\Country;

class Visitor {
    public function __construct(){
        add_filter('woocommerce_currency', [ $this, 'change_woocommerce_currency_based_on_country' ], 10, 1);
    }

    private function get_visitor_country() {
        // Get the visitor's IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Create a GeoIP2 reader
        $reader = new \GeoIp2\Database\Reader( ADSWCS_PLUGIN_PATH . '/GeoLite2-Country/GeoLite2-Country.mmdb' ); // Replace with the actual path to the GeoIP2 database file

        try {
            // Perform a geolocation lookup
            $record = $reader->country($ip_address);

            // Extract the country code from the lookup result
            $country_code = $record->country->isoCode;

            return $country_code;
        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            // Handle the exception when IP address is not found in the database
            return 'USA'; // Default country code
        }
    }

    public function change_woocommerce_currency_based_on_country($currency) {
        // Get visitor's country
        $all_countries = new Country();
        $get_all_countries = $all_countries->get_countries();
    
        $country = $this->get_visitor_country();
    
        // Initialize the default currency
        $default_currency = 'BDT';
    
        // Search for the visitor's country code in the array and get the corresponding currency
        foreach ($get_all_countries as $countryInfo) {
            if ($countryInfo['code'] === $country && isset($countryInfo['currency'])) {
                $currency = $countryInfo['currency'];
                break;
            }
        }
    
        // If no matching currency is found, use the default currency
        if ($currency === $default_currency) {
            $currency = $default_currency;
        }
    
        return $currency;
    }
    
    public function set_currency_based_on_country() {
        // Get visitor country
        $visitor_country = $this->get_visitor_country(); // Use the variable instead of calling the function again
    
        $all_countries = new Country();
        $get_all_countries = $all_countries->get_countries();
    
        // Default currency in case the visitor's country is not in the mapping
        $default_currency = 'BDT';
    
        // Iterate through the array to find the matching currency
        $currency = $default_currency; // Initialize with the default currency
    
        foreach ($get_all_countries as $country) {
            if ($country['code'] === $visitor_country) {
                $currency = $country['currency']; // Set currency based on the visitor's country code
                break; // Stop looping once a match is found
            }
        }
    
        // Update the WooCommerce currency option
        update_option('woocommerce_currency', $currency);
    }
    
    
    
}