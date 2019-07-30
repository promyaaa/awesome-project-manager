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
    // public function register_menu() {
    //     $capability = apply_filters( 'fusion_pm_menu_cap', 'manage_project' );

    //     add_menu_page( 'Fusion PM', 'Project Manager', $capability, 'fusion-pm', array( $this, 'fusion_pm_page' ), 'dashicons-editor-table', 15 );

        // do_action( 'fusion_pm_menu', $capability );
    // }

    
    function register_menu() {
        global $submenu;

      // add_menu_page( __( 'Project Manager', 'text-domain' ), __( 'Project Manager', 'text-domain' ), 'manage_options', 'awesome-pm', 'pm_dashboard_cb' );

        $capability = apply_filters( 'fusion_pm_menu_cap', 'manage_project' );
        add_menu_page( 'Fusion PM', 'Project Manager', $capability, 'fusion-pm', array( $this, 'pm_dashboard_cb' ), 'dashicons-editor-table', 15 );

        $submenu['fusion-pm'][] = array( __( 'Projects', 'text-domain' ), 'manage_project', 'admin.php?page=fusion-pm#/' );
        $submenu['fusion-pm'][] = array( __( 'Assignments', 'text-domain' ), 'manage_project', 'admin.php?page=fusion-pm#/my/assignments' );
        $submenu['fusion-pm'][] = array( __( 'My Activity', 'text-domain' ), 'manage_project', 'admin.php?page=fusion-pm#/my/activity' );
        do_action( 'fusion_pm_menu', $capability );
    }
    // For line number 8 callback function. where main single page div loaded
    function pm_dashboard_cb() {
      include_once FUSION_PM_PATH . '/views/admin.php';
    }

    /**
    * Show menu content
    *
    * @since 1.0.0
    *
    * @return void
    **/
    // public function fusion_pm_page() {
    //     include_once FUSION_PM_PATH . '/views/admin.php';
    // }
}