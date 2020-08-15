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
    function register_menu() {
        global $submenu;

        $capability = apply_filters( 'fusion_pm_menu_cap', 'manage_project' );
        add_menu_page( 'Fusion PM', 'Project Manager', $capability, 'fusion-pm', array( $this, 'pm_dashboard_cb' ), 'dashicons-editor-table', 15 );

        $submenu['fusion-pm'][] = array( __( 'Projects', 'text-domain' ), $capability, 'admin.php?page=fusion-pm#/' );
        // $submenu['fusion-pm'][] = array( __( 'Completed Projects', 'text-domain' ), $capability, 'admin.php?page=fusion-pm#/completed-projects' );
        $submenu['fusion-pm'][] = array( __( 'Assignments', 'text-domain' ), $capability, 'admin.php?page=fusion-pm#/my/assignments' );
        $submenu['fusion-pm'][] = array( __( 'My Activity', 'text-domain' ), $capability, 'admin.php?page=fusion-pm#/my/activity' );
        $submenu['fusion-pm'][] = array( __( 'My Bookmarks', 'text-domain' ), $capability, 'admin.php?page=fusion-pm#/my/bookmarks' );

        do_action( 'fusion_pm_menu', $capability );
    }
    // For line number 8 callback function. where main single page div loaded
    function pm_dashboard_cb() {
      include_once FUSION_PM_PATH . '/views/admin.php';
    }

}