<?php 
namespace ADSWCS\Frontend;

use ADSWCS\Traits\Trait_Country;
use ADSWCS\Traits\Trait_Utility;

/**
 * Class WC_Price_Shortcode
 * Handles the WooCommerce price shortcode.
 */
class WC_Price_Shortcode {

    use Trait_Country;
    use Trait_Utility;

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('init', [ $this, 'register_shortcode' ]);
    }

    /**
     * Register the shortcode.
     */
    public function register_shortcode() {
        add_shortcode('ads_get_price_by_id', [ $this, 'wc_price_shortcode_callback' ]);
    }

    /**
     * Callback function for the WooCommerce price shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string Rendered output for the shortcode.
     */
    public function wc_price_shortcode_callback( $atts ) {
        $atts = shortcode_atts( array(
            'id' => null,
        ), $atts, 'bartag' );

        // Use the currency symbol from your Trait_Country trait
        $country_data = $this->get_country_data(); 
        $currency_symbol = isset($country_data['symbol']) ? $country_data['symbol'] : '$';

        $cache_buster = time(); // Use current timestamp as cache buster

        ob_start();

        if ( intval( $atts['id'] ) > 0 && function_exists( 'wc_get_product' ) ) {
            $_product = wc_get_product( $atts['id'] );
            $regular_price = wc_format_decimal( $_product->get_regular_price(), 0 );
            $sale_price = wc_format_decimal( $_product->get_price(), 0 );
            ?>
            <p class="adswcs-custom-price">
                <span class="price price-regular"><?php echo $currency_symbol . $regular_price; ?></span>
                <span class="price price-sale"><?php echo $currency_symbol . $sale_price; ?></span>
            </p>
            <script>
                var cacheBuster = <?php echo $cache_buster; ?>;
            </script>
            <?php
        }

        $output = ob_get_clean();
        return $output;
    }

    /**
     * Get country data from the Trait_Country trait based on the provided country code.
     *
     * @param string $country_code The country code.
     * @return array|false The country data or false if not found.
     */
    private function get_country_data() {
        $country_code = $this->get_visitor_country();
        $countries = $this->get_countries();

        foreach ($countries as $country) {
            if ($country['code'] === $country_code) {
                return $country;
            }
        }

        return false;
    }
}

