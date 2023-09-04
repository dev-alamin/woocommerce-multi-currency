<?php
namespace ADSWCS\Admin;

class Menu{
    public function __construct() {
        add_action('admin_menu', [ $this, 'top_level_menu' ] );
        // add_action('admin_init', [ $this, 'register_country_option_settings' ] );
    }

    
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
    
    public function adswcs_callback(){
        $file = ADSWCS_PLUGIN_PATH . 'templates/admin-page.php';

        if( file_exists( $file ) ) {
            $country_class = new \ADSWCS\Admin\Country();
            $all_countries = $country_class->get_countries(); // $all_count var used on template

            if (isset($_POST['submit'])) {
                    // Save selected countries when nonce is valid
                    $this->save_selected_countries();
            }

            require_once $file;
        }
    }

    public function save_selected_countries() {
        // Check if the form is submitted and the nonce is valid
        if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['_wpnonce'])) {
            // Verify the nonce for security
            if (wp_verify_nonce($_POST['_wpnonce'], 'adswcs_country_nonce')) {
                // Retrieve the selected countries from the form data
                $selected_countries = isset($_POST['selected_countries_option']) ? $_POST['selected_countries_option'] : array();

                // Save the selected countries to a WordPress option
                update_option('selected_countries_option', $selected_countries);

                // Redirect back to the settings page or perform any other actions as needed
                wp_redirect(admin_url('admin.php?page=adsw_currency_switcher&success=true'));
                exit;
            } else {
                // Nonce verification failed, handle the error
                echo 'Nonce verification failed. Please try again.';
            }
        }

    }
    
    
}

