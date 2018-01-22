<?php

/**
* Admin class
*/
class FusionPM_Admin {

    /**
    * Autometically loaded when class initiate
    *
    * @since 1.0.0
    */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_menu' ), 10 );
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Admin();
        }

        return $instance;
    }

    /**
    * Regsiter menu
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function register_menu() {
        $capability = apply_filters( 'fusion_pm_menu_cap', 'manage_project' );

        add_menu_page( 'Fusion PM', 'FProject Manager', $capability, 'fusion-pm', array( $this, 'fusion_pm_page' ), 'dashicons-editor-table', 15 );

        do_action( 'fusion_pm_menu', $capability );
    }


    /**
    * Show menu content
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function fusion_pm_page() {
        include_once FUSION_PM_PATH . '/views/admin.php';
    }
}
















