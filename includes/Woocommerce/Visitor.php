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
    public function change_woocommerce_currency_based_on_country( $currency ) {
        $countries = $this->get_countries();
        $visitor_country = $this->get_visitor_country();
        $selectedCountries = get_option( 'adswcs_selected_countries_option', [] );
        $default_currency = $this->get_currency_by_country( get_option( 'adswcs_default_country', [] ) );

        if( in_array(  $visitor_country , $selectedCountries ) ) {
            foreach ( $countries as $countryInfo ) {
                if ($countryInfo['code'] === $visitor_country && isset($countryInfo['currency'])) {
                    return $countryInfo['currency'];
                }
            }
        }else{
            return $default_currency;
        }

        return $currency;
    }

    public function set_currency_based_on_country() {
        $visitor_country = $this->get_visitor_country();
        $countries = $this->get_countries();
        $selectedCountries = get_option( 'adswcs_selected_countries_option', [] );
        $currency = $this->get_currency_by_country( get_option( 'adswcs_default_country', [] ) ); 

        if( in_array(  $visitor_country , $selectedCountries ) ) {
            foreach ($countries as $country) {
                if ($country['code'] === $visitor_country) {
                    $currency = $country['currency'];
                    break;
                }
            }
        }
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
        $default_country = get_option( 'adswcs_default_country', [] );
        $visitor_country = $this->get_visitor_country();
        $price_field_mapping = $this->get_country_price_field_mapping($visitor_country);
        $default_mapping = $this->get_country_price_field_mapping($default_country);
        
        $selectedCountries = get_option( 'adswcs_selected_countries_option', [] );

        if ($price_field_mapping && in_array(  $visitor_country , $selectedCountries ) ) {
            $regular_price_field = $price_field_mapping['regular_price_field'];
            $sale_price_field = $price_field_mapping['sale_price_field'];
    
            // Get the values of the custom price fields
            $custom_regular_price = get_post_meta($product->get_id(), $regular_price_field, true);
            $custom_sale_price = get_post_meta($product->get_id(), $sale_price_field, true);
    
            // Get the values of the default WooCommerce price fields
            $default_regular_price = $product->get_regular_price();
            $default_sale_price = $product->get_sale_price();
    
            // Check if the custom price fields are different from the default fields
            if ($custom_regular_price !== $default_regular_price) {
                // Update the product's prices with the custom fields
                if ($custom_regular_price !== '') {
                    try {
                        $product->set_regular_price($custom_regular_price);
                        $product->save();
                    } catch (\Exception $e) {
                        echo 'Error updating regular price: ' . $e->getMessage();
                    }
                }
            }
            if($custom_sale_price !== $default_sale_price ) {
                if ($custom_sale_price !== '') {
                    try {
                        $product->set_sale_price($custom_sale_price);
                        $product->save();
                    } catch (\Exception $e) {
                        echo 'Error updating sale price: ' . $e->getMessage();
                    }
                }
            }
        
        }else{

            if ($default_mapping) {
                $regular_price_field = $default_mapping['regular_price_field'];
                $sale_price_field = $default_mapping['sale_price_field'];
        
                // Get the values of the custom price fields
                $custom_regular_price = get_post_meta($product->get_id(), $regular_price_field, true);
                $custom_sale_price = get_post_meta($product->get_id(), $sale_price_field, true);
                // Get the values of the default WooCommerce price fields
                $default_regular_price = $product->get_regular_price();
                $default_sale_price = $product->get_sale_price();
        
                // Check if the custom price fields are different from the default fields
                if ($custom_regular_price !== $default_regular_price ) {
                    if ($custom_regular_price !== '') {
                        try {
                            $product->set_regular_price($custom_regular_price);
                            $product->save();
                        } catch (\Exception $e) {
                            echo 'Error updating regular price: ' . $e->getMessage();
                        }
                    }
                }
                    if($custom_sale_price !== $default_sale_price){
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
        }
    
        return $price;
    }

    /**
     * Get the mapping between a user's country code and the corresponding price field.
     *
     * This method retrieves a mapping between a user's country code (in uppercase) and
     * the corresponding price field. The mapping is based on the result returned by the
     * get_map method.
     *
     * @param string $user_country The uppercase ISO country code of the user.
     *
     * @return mixed|array|false The mapping between the user's country and the price field,
     *                           or false if no mapping is found.
     *
     * @since 1.0
     */
    private function get_country_price_field_mapping($user_country) {
        $mapping = $this->get_map();
    
        if (is_array($mapping) && array_key_exists($user_country, $mapping)) {
            return $mapping[$user_country];
        } else {
            // Handle the case where the country mapping is not found.
            return null; // You can return false, null, or any other appropriate value here.
        }
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

    /**
     * Get the currency code associated with a specific country.
     *
     * This function searches for the currency code of the provided country code
     * within the list of supported countries and their corresponding currencies.
     *
     * @param string $country The ISO country code for which to retrieve the currency.
     *
     * @return string The currency code associated with the provided country code.
     *                If the country code is not found, a default message is returned.
     *
     * @since 1.0
     */
    public function get_currency_by_country( $country = '' ) {
        $all_countries = $this->get_countries();
        $default = __( 'No country is found sorry', 'ads-currency-switcher' );

        foreach( $all_countries as $count ) {
            if( $count['code']  === $country ) {
                return $count['currency'];
            }
        }

        return $default;
    }

}