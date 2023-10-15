<?php 
/**
 * Plugin Name: WC currency switcher
 * Plugin URI:  https://almn.me/wc-currency-switcher
 * Description: This plugin is a helper for WC currency switcher.
 * Version:     1.0
 * Author:      Al Amin
 * Author URI:  https://almn.me
 * Text Domain: ads-wc-currency-switcher
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Requires at least: 4.9
 * Requires PHP: 5.2.4
 * Requires Plugins: Required plugins
 *
 * @package     ADSWoocommerce_Currency_Switcher
 * @author      Al Amin
 * @copyright   2023 awesomedigitalsolution
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 *
 * Prefix:      ADSWCS
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

$file = __DIR__ . '/vendor/autoload.php';
if( file_exists( $file ) ) {
    require_once $file;
}else{
    wp_die('Composer vendor does not exists');
}
/**
 * The main class for the ADSW Currency Switcher plugin.
 *
 * This class initializes the plugin, defines constants, and sets up essential functionality.
 * It ensures that the plugin is loaded only once and handles localization.
 *
 * @package ADSWoocommerce_Currency_Switcher
 * @since   1.0
 */
final class ADSW_Currency_Switcher {
    /**
     * The single instance of the plugin class.
     *
     * @var null|ADSW_Currency_Switcher
     */
    private static $instance = null;

    /**
     * The current version of the plugin.
     *
     * @var string
     */
    public $version = '1.0';

    /**
     * Private constructor to prevent direct instantiation of the class.
     *
     * Initializes the plugin by defining constants and adding the 'plugins_loaded' action hook.
     *
     * @access private
     */
    private function __construct(){
        $this->define_constants();
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        
        if ( ! $this->is_woocommerce_installed() ) {
            add_action( 'admin_notices', [ $this, 'woocommerce_missing_notice' ] );
        }
    }

    /**
     * Retrieves the single instance of the plugin class.
     *
     * @return ADSW_Currency_Switcher The plugin instance.
     */
    public static function get_instance() {
        if( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initializes the plugin by loading localization files and setting up essential components.
     *
     * @access public
     */
    public function init() {
        // Load localization files
        load_plugin_textdomain( 'ADSWCS', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        // Initialize plugin components
        new \ADSWCS\Admin\Menu();
        $visitor = new \ADSWCS\Woocommerce\Visitor();
        $visitor->set_currency_based_on_country();

        new \ADSWCS\Woocommerce\Post_Meta();
        new \ADSWCS\Frontend();
        new \ADSWCS\Assets();
    }

    /**
     * Defines plugin-specific constants for version, plugin file, URL, and path.
     *
     * @access public
     */
    public function define_constants(){
        define( 'ADSWCS_VERSION', $this->version );
        define( 'ADSWCS_PLUGIN', __FILE__ );
        define( 'ADSWCS_PLUGIN_URL', plugins_url( '',  ADSWCS_PLUGIN ) );
        define( 'ADSWCS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        define( 'ADSWCS_PLUGIN_ASSETS', ADSWCS_PLUGIN_URL . '/assets');
    }

    /**
     * Checks if WooCommerce is installed and activated.
     *
     * @return bool True if WooCommerce is installed and active, false otherwise.
     */
    private function is_woocommerce_installed() {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    /**
     * Displays a notice in the WordPress admin if WooCommerce is missing.
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e( 'ADSW Currency Switcher requires WooCommerce to be installed and activated. Please install and activate WooCommerce to use this plugin.', 'ADSWCS' ); ?></p>
        </div>
        <?php
    }
}

// Check if the function does not already exist
if( ! function_exists( 'ADSW_Currency_Switcher' ) ) {
    /**
     * Retrieves the single instance of the ADSW_Currency_Switcher class.
     *
     * @return ADSW_Currency_Switcher The plugin instance.
     */
    function ADSW_Currency_Switcher(){
        return ADSW_Currency_Switcher::get_instance();
    }
}

if( function_exists( 'ADSW_Currency_Switcher' ) ) {
    // Kick Off the plugin 
    ADSW_Currency_Switcher();
}
