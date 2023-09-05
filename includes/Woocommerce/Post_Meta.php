<?php 
namespace ADSWCS\Woocommerce;
use ADSWCS\Traits\Trait_Country;
/**
 * Class Post_Meta
 * Handles custom product meta fields for WooCommerce products.
 */
class Post_Meta{
    use Trait_Country;

    /**
     * Constructor.
     * Adds actions for adding and saving custom product fields.
     */
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [ $this, 'add_custom_product_fields'] );
        add_action('woocommerce_process_product_meta', [ $this, 'save_custom_product_fields' ] );
    }

    /**
     * Add custom product fields to the WooCommerce product data panel.
     */
    public function add_custom_product_fields(){
        global $post, $woocommerce;
        $selectedCountries = get_option( 'selected_countries_option', [] );
        echo '<div id="country_prices" class="woocommerce_options_panel">';
        $get_all_countries = $this->get_countries();
        
        $field_labels = [];
        
        foreach ($get_all_countries as $country) {
            $field_labels[$country['code']] = $country['symbol'];
        }
        
        foreach ($selectedCountries as $countryCode) {
            if (isset($field_labels[$countryCode])) {
                $currencySymbol = $field_labels[$countryCode];
        
                woocommerce_wp_text_input(
                    array(
                        'id' => '_regular_price_' . strtolower( $countryCode ),
                        'label' => __('Regular Price ' . $countryCode . ' (' . $currencySymbol . ')', 'ads-currency-switcher'),
                        'desc_tip' => 'true',
                        'description' => __('Enter the regular price for ' . $countryCode, 'ads-currency-switcher'),
                        'type' => 'text',
                    )
                );
        
                woocommerce_wp_text_input(
                    array(
                        'id' => '_sale_price_' . strtolower( $countryCode ),
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

    /**
     * Save custom product fields when a product is updated.
     *
     * @param int $post_id The ID of the product being updated.
     */
    public function save_custom_product_fields($post_id){
        $selectedCountries = get_option('selected_countries_option');
    
        foreach ($selectedCountries as $countryCode) {
            $regularPriceMetaKey = '_regular_price_' . strtolower(  $countryCode );
            $salePriceMetaKey = '_sale_price_' . strtolower(  $countryCode );
    
            if (isset($_POST[$regularPriceMetaKey])) {
                update_post_meta($post_id, $regularPriceMetaKey, sanitize_text_field($_POST[$regularPriceMetaKey]));
            }
    
            if (isset($_POST[$salePriceMetaKey])) {
                update_post_meta($post_id, $salePriceMetaKey, sanitize_text_field($_POST[$salePriceMetaKey] ) );
            }
        }
    }
}
