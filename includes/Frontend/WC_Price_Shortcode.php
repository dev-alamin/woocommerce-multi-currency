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

            $_price = wc_price( $_product->get_price() );
            $sale_price = $_product->get_sale_price() ? wc_price( $_product->get_sale_price() ) : '';
            ?>

            <p class="adswcs-custom-price">
                <?php if( ! empty( $regular_price ) && ! empty( $sale_price ) ) : ?>
                    <span class="price price-regular"><?php echo wp_kses_post($regular_price); ?></span>
                <?php endif; ?>

                <?php if( ! empty( $_price ) ): ?>
                    <span class="price price-sale"><?php echo wp_kses_post($_price); ?></span>
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
