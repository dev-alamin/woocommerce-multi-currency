<?php 
/**
 * Plugin Name: WC currency switcher
 * Plugin URI:  https://almn.me/onpoint-learning
 * Description: This plugin is a helper for WC currency switcher.
 * Version:     1.0
 * Author:      Al Amin
 * Author URI:  https://almn.me
 * Text Domain: ADSWCS
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Requires at least: 5.4
 * Requires PHP: 7.0
 * Requires Plugins: Required plugins
 *
 * @package     ADSWCS
 * @author      Al Amin
 * @copyright   2023 awesomedigitalsolution
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 *
 * Prefix:      ADSWCS
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}else{
    wp_die('File does not exists');
}

// require_once __DIR__ . '/admin/menu.php';
// require_once __DIR__ . '/frontend/shortcode/price-shortcode.php';
// require_once __DIR__ . '/frontend/enqueue/frontend.php';

use GeoIp2\Database\Reader;

final class ADSW_Currency_Switcher {
    private static $instance = null;
    public $version = '1.0';

    private function __construct(){
        $this->define_constants();
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        
    }

    public static function get_instance() {
        if( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init() {
        load_plugin_textdomain( 'ADSWCS', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        new \ADSWCS\Admin\Menu();
        $visitor = new \ADSWCS\Woocommerce\Visitor();
        $visitor->set_currency_based_on_country();

        new \ADSWCS\Woocommerce\Post_Meta();
        new \ADSWCS\Frontend\WC_Price_Shortcode();
        new \ADSWCS\Assets();
    }

    /**
     * Load localization files
     *
     * @return void
     */
    public function ADSWCS_plugin_init() {
        
    }

    public function define_constants(){
        define( 'ADSWCS_VERSION', $this->version );
        define( 'ADSWCS_PLUGIN', __FILE__ );
        define( 'ADSWCS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        define( 'ADSWCS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    }
}

if( ! function_exists( 'ADSW_Currency_Switcher' ) ) {
    function ADSW_Currency_Switcher(){
        return ADSW_Currency_Switcher::get_instance();
    }
}

if( function_exists( 'ADSW_Currency_Switcher' ) ) {
    // Kick Off the plugin 
    ADSW_Currency_Switcher();
}




// Check if WooCommerce is active
// if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

// }


// Checkout page 
add_action( 'wp_footer', function(){
    ?>
    <script>
        jQuery(document).ready(function($) {
            $('.form_section_single_step_0_elementor-hific .wfacp_section_title').text('Learner Information');
        });
		
		jQuery(document).ready(function($) {
    	// Remove the class 'wfacp_display_none' from the specified div on first load
		$('.wfacp_mb_mini_cart_sec_accordion_content').removeClass('wfacp_display_none');
	});

    </script>
    <?php
}, 90 );

