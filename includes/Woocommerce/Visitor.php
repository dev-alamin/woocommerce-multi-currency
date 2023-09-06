<?php 
namespace ADSWCS\Woocommerce;
use ADSWCS\Traits\Trait_Meta_Mapping;
use ADSWCS\Traits\Trait_Country;
use ADSWCS\Traits\Trait_Utility;
/**
 * Class Visitor
 * Handles visitor-related functionality in WooCommerce.
 */
class Visitor {
    
    use Trait_Meta_Mapping;
    use Trait_Country;
    use Trait_Utility;

    public function __construct(){
        add_filter('woocommerce_currency', [ $this, 'change_woocommerce_currency_based_on_country' ], 10, 1);
        add_filter('woocommerce_product_get_price', [ $this, 'adjust_product_prices_based_on_country' ], 10, 2);
    }

    /**
     * Change the WooCommerce currency based on the visitor's country.
     *
     * @param string $currency The current WooCommerce currency.
     * @return string The updated currency based on the visitor's country.
     */
    public function change_woocommerce_currency_based_on_country($currency) {
        $countries = $this->get_countries();
        $country = $this->get_visitor_country();
        
        foreach ($countries as $countryInfo) {
            if ($countryInfo['code'] === $country && isset($countryInfo['currency'])) {
                return $countryInfo['currency']; // Return the corresponding currency if found
            }
        }
        
        return $currency;
    }
    
    
    public function set_currency_based_on_country() {
        $visitor_country = $this->get_visitor_country();
        $countries = $this->get_countries();
        $currency = $visitor_country; 
        
        foreach ($countries as $country) {
            if ($country['code'] === $visitor_country) {
                $currency = $country['currency']; // Set currency based on the visitor's country code
                break; // Stop looping once a match is found
            }
        }
    
        // Update the WooCommerce currency option
        update_option('woocommerce_currency', $currency);
    }
    

    /**
     * Adjust product prices based on the visitor's country.
     *
     * @param float $price The product price.
     * @param object $product The WooCommerce product object.
     * @return float The updated product price.
     */
    public function adjust_product_prices_based_on_country($price, $product) {
        $user_country = strtolower($this->get_visitor_country());
        $price_field_mapping = $this->get_country_price_field_mapping($user_country);
    
        if ($price_field_mapping) {
            $regular_price_field = $price_field_mapping['regular_price_field'];
            $sale_price_field = $price_field_mapping['sale_price_field'];
    
            // Get the values of the custom price fields
            $custom_regular_price = get_post_meta($product->get_id(), $regular_price_field, true);
            $custom_sale_price = get_post_meta($product->get_id(), $sale_price_field, true);
    
            // Get the values of the default WooCommerce price fields
            $default_regular_price = $product->get_regular_price();
            $default_sale_price = $product->get_sale_price();
    
            // Check if the custom price fields are different from the default fields
            if ($custom_regular_price !== $default_regular_price || $custom_sale_price !== $default_sale_price) {
                // Update the product's prices with the custom fields
                if ($custom_regular_price !== '') {
                    try {
                        $product->set_regular_price($custom_regular_price);
                        $product->save();
                    } catch (\Exception $e) {
                        echo 'Error updating regular price: ' . $e->getMessage();
                    }
                }
    
                if ($custom_sale_price !== '') {
                    try {
                        $product->set_sale_price($custom_sale_price);
                        $product->save();
                    } catch (\Exception $e) {
                        echo 'Error updating sale price: ' . $e->getMessage();
                    }
                }
            }
        }
    
        return $price;
    }
    
    /**
     * Get the mapping of country codes to custom price field names.
     *
     * @param string $user_country The user's country code.
     * @return array|false The mapping of price fields or false if not found.
     */
    private function get_country_price_field_mapping($user_country) {
        $user_country = strtoupper( $user_country );
        $mapping = $this->get_map();

        return isset($mapping[$user_country]) ? $mapping[$user_country] : false;
    }
    
    /**
     * Register custom meta fields for WooCommerce products.
     */
    public function register_meta(){
        $countries = [];

        if( $countries > 0 ) {
                foreach ($countries as $country_code => $fields) {
                    register_post_meta('product', $fields['regular_price_field'], array(
                        'show_in_rest' => true,
                        'single' => true,
                        'type' => 'string',
                    ));
                    
                    register_post_meta('product', $fields['sale_price_field'], array(
                    'show_in_rest' => true,
                    'single' => true,
                    'type' => 'string',
                ));
            }
        }
    }
}