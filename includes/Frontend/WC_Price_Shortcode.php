<?php 
namespace ADSWCS\Frontend;

/**
 * Class WC_Price_Shortcode
 * Handles the WooCommerce price shortcode.
 */
class WC_Price_Shortcode {
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

        $cache_buster = time(); // Use current timestamp as cache buster

        ob_start();

        if ( intval( $atts['id'] ) > 0 && function_exists( 'wc_get_product' ) ) {
            $_product = wc_get_product( $atts['id'] );
            $regular_price = wc_price( $_product->get_regular_price());

            $_price = $_product->get_price();
            $sale_price = $_product->get_sale_price();
            $regular_price_formated = 
            $_price_formatted = number_format($_price, ($_price == intval($_price)) ? 0 : 2);
            $sale_price_formatted = number_format($sale_price, ($sale_price == intval($sale_price)) ? 0 : 2);

            // Format the price
            $_price = number_format($_price, ($_price == intval($_price)) ? 0 : 2);

            // Format the sale price
            $sale_price = number_format($sale_price, ($sale_price == intval($sale_price)) ? 0 : 2);
            ?>
            <p class="adswcs-custom-price">
                <?php if( ! empty( $regular_price ) ) : ?>
                    <span class="price price-regular"><?php echo $regular_price; ?></span>
                <?php endif; ?>
                
                <?php if( !empty( $sale_price ) ) : ?>
                    <span class="price price-sale"><?php echo $sale_price; ?></span>
                <?php endif; ?>
                
                <?php if( ! empty( $_price ) ): ?>
                    <span class="price price-sale"><?php echo $_price; ?></span>
                <?php endif; ?>
            </p>
            <script>
                var cacheBuster = <?php echo $cache_buster; ?>;
            </script>
            <?php
        }

        $output = ob_get_clean();
        return $output;
    }
}
