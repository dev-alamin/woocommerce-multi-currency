<?php
namespace ADSWCS\Admin;
use ADSWCS\Traits\Trait_Country;
/**
 * Class Menu
 * Handles the admin menu and callback functions for the Currency Switcher plugin.
 */
class Menu{
    use Trait_Country;

    /**
     * Constructor.
     * Adds an action to create the top-level admin menu.
     */
    public function __construct() {
        add_action('admin_menu', [ $this, 'top_level_menu' ] );
    }

    /**
     * Create the top-level admin menu and a submenu page.
     */
    public function top_level_menu(){
        add_menu_page( 
            __( 'Currency switcher', 'ads-currency-switcher' ), 
            __( 'Currency switcher', 'ads-currency-switcher' ),
            'manage_options', 
            'adsw_currency_switcher',
            [ $this, 'adswcs_callback' ],
            'dashicons-money-alt' 
        );

        add_submenu_page( 
            'adsw_currency_switcher',
            __( '2 Course Deal', 'ads-currency-switcher' ),
            __( '2 Course Deal', 'ads-currency-switcher' ),
            'manage_options', 
            'adsw_currency_switcher',
            [ $this, 'adswcs_callback' ] 
        );
    }

    /**
     * Callback function for the admin page.
     */
    public function adswcs_callback(){
        $file = ADSWCS_PLUGIN_PATH . 'templates/admin-page.php';

        if( file_exists( $file ) ) {
            $all_countries = $this->get_countries();
            if (isset($_POST['submit'])) {
                // Replace 'adswcs_country_nonce' with your actual nonce name
                if (isset($_POST['adswcs_country_nonce']) && check_admin_referer('adswcs_country_nonce')) {
                    $this->save_selected_countries();
                } else {
                    echo esc_html__( 'Sorry, action is not allowed', 'ads-currency-switcher' );
                }
            }            

            require_once $file;
        }
    }

    /**
     * Save selected countries from the admin settings page.
     */
    public function save_selected_countries() {
        if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['_wpnonce'])) {
            if (wp_verify_nonce($_POST['_wpnonce'], 'adswcs_country_nonce')) {
                $selected_countries = isset($_POST['adswcs_selected_countries_option']) ? $_POST['adswcs_selected_countries_option'] : array();
                $selected_default_country = isset($_POST['user_default_country']) ? $_POST['user_default_country'] : array();

                update_option('adswcs_selected_countries_option', $selected_countries);
                update_option('adswcs_default_country', $selected_default_country);                
                wp_redirect(admin_url('admin.php?page=adsw_currency_switcher&success=true'));
                exit;
            } else {
                echo esc_html__( 'Nonce verification failed. Please try again.', 'ads-currency-switcher' );
            }
        }
    }
}

