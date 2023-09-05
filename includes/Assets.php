<?php 
namespace ADSWCS;

class Assets{

    /**
     * Class constructor
     */
    function __construct() {
        // add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /**
     * All available scripts
     *
     * @return array
     */
    private function get_scripts() {
        return [
            // 'academy-script' => [
            //     'src'     => ADSWCS_PLUGIN_PATH . '/js/frontend.js',
            //     'version' => filemtime( ADSWCS_PLUGIN_PATH . '/assets/js/frontend.js' ),
            //     'deps'    => [ 'jquery' ]
            // ],
            // 'academy-admin-script' => [
            //     'src'     => ADSWCS_PLUGIN_PATH . '/js/admin.js',
            //     'version' => filemtime( ADSWCS_PLUGIN_PATH . '/assets/js/admin.js' ),
            //     'deps'    => [ 'jquery', 'wp-util' ]
            // ],
        ];
    }

    /**
     * All available styles
     *
     * @return array
     */
    private function get_styles() {
        return [
            'adswcs-style' => [
                'src'     => ADSWCS_PLUGIN_URL . 'assets/css/frontend.css',
                'version' => filemtime( ADSWCS_PLUGIN_PATH . '/assets/css/frontend.css' )
            ],
            'adswcs-admin-style' => [
                'src'     => ADSWCS_PLUGIN_URL . 'assets/css/admin.css',
                'version' => filemtime( ADSWCS_PLUGIN_PATH . '/assets/css/admin.css' )
            ],
        ];
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets() {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        // foreach ( $scripts as $handle => $script ) {
        //     $deps = isset( $script['deps'] ) ? $script['deps'] : false;

        //     wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
        // }

        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_enqueue_style( $handle, $style['src'], $deps, $style['version'] );
        }
    }
}