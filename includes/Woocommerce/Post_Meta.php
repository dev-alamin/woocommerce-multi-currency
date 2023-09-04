<?php 
namespace ADSWCS\Woocommerce;
use ADSWCS\Admin\Country;

class Post_Meta{
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [ $this, 'add_custom_product_fields'] );
        add_action('woocommerce_process_product_meta', [ $this, 'save_custom_product_fields' ] );
    }

    public function add_custom_product_fields(){
        global $post, $woocommerce;
        $selectedCountries = get_option( 'selected_countries_option', [] );
        // Add a new tab for country-specific prices
        echo '<div id="country_prices" class="woocommerce_options_panel">';

        // Define the labels and descriptions for your fields in an associative array
        $all_countries = new Country();
        $get_all_countries = $all_countries->get_countries();
        
        $field_labels = [];
        
        foreach ($get_all_countries as $country) {
            // Use the country code as the key and the currency symbol as the value
            $field_labels[$country['code']] = $country['symbol'];
        }
        
        foreach ($selectedCountries as $countryCode) {
            if (isset($field_labels[$countryCode])) {
                $currencySymbol = $field_labels[$countryCode];
        
                // Regular Price Field
                woocommerce_wp_text_input(
                    array(
                        'id' => '_regular_price_' . $countryCode,
                        'label' => __('Regular Price ' . $countryCode . ' (' . $currencySymbol . ')', 'ads-currency-switcher'),
                        'desc_tip' => 'true',
                        'description' => __('Enter the regular price for ' . $countryCode, 'ads-currency-switcher'),
                        'type' => 'text',
                    )
                );
        
                // Sale Price Field
                woocommerce_wp_text_input(
                    array(
                        'id' => '_sale_price_' . $countryCode,
                        'label' => __('Sale Price ' . $countryCode . ' (' . $currencySymbol . ')', 'ads-currency-switcher'),
                        'desc_tip' => 'true',
                        'description' => __('Enter the sale price for ' . $countryCode, 'ads-currency-switcher'),
                        'type' => 'text',
                    )
                );
            }
        }
        

    
        echo '</div>';
    }

    public function save_custom_product_fields($post_id){
        // Get the selected countries for which prices need to be saved
        $selectedCountries = get_option('selected_countries_option');
    
        foreach ($selectedCountries as $countryCode) {
            // Define the post meta keys for regular and sale prices based on the country code
            $regularPriceMetaKey = '_regular_price_' . $countryCode;
            $salePriceMetaKey = '_sale_price_' . $countryCode;
    
            // Check if the regular price for this country is set in the POST data
            if (isset($_POST[$regularPriceMetaKey])) {
                // Update the post meta for regular price
                update_post_meta($post_id, $regularPriceMetaKey, sanitize_text_field($_POST[$regularPriceMetaKey]));
            }
    
            // Check if the sale price for this country is set in the POST data
            if (isset($_POST[$salePriceMetaKey])) {
                // Update the post meta for sale price
                update_post_meta($post_id, $salePriceMetaKey, sanitize_text_field($_POST[$salePriceMetaKey]));
            }
        }
    }
    
}