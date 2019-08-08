<?php
/*
Plugin Name: Fusion project manager
Plugin URI: http://example.com/
Description: A single page project manager plugin for WordPress.
Version: 1.0.5
Author: M Abdur Rokib Promy
Author URI: https://github.com/promyaaa
License: GPL2
*/

/**
 * Copyright (c) 2018 M Abdur Rokib Promy (email: promy89@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Fusion_PM class
 *
 * @class Fusion_PM The class that holds the entire Fusion_PM plugin
 */
class Fusion_PM {

     /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.5';

    /**
     * Constructor for the Fusion_PM class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {

        // Define all constant
        $this->define_constant();

        //includes file
        $this->includes();

        // init actions and filter
        $this->init_filters();
        $this->init_actions();

        // initialize classes
        $this->init_classes();

        do_action( 'fusion_pm_loaded', $this );
    }

    /**
     * Initializes the Fusion_PM() class
     *
     * Checks for an existing Fusion_PM() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Fusion_PM();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public static function activate() {
        $installer = new FusionPM_Install();
        $installer->do_install();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public static function deactivate() {

    }

    /**
    * Defined constant
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function define_constant() {
        define( 'FUSION_PM_VERSION', $this->version );
        define( 'FUSION_PM_FILE', __FILE__ );
        define( 'FUSION_PM_PATH', dirname( FUSION_PM_FILE ) );
        define( 'FUSION_PM_ASSETS', plugins_url( '/assets', FUSION_PM_FILE ) );
    }

    /**
    * Includes all files
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function includes() {
        
        if ( is_admin() ) {
            require_once FUSION_PM_PATH . '/classes/class-install.php';

            require_once FUSION_PM_PATH . '/models/Project.php';
            require_once FUSION_PM_PATH . '/models/List.php';
            require_once FUSION_PM_PATH . '/models/Todo.php';
            require_once FUSION_PM_PATH . '/models/Comment.php';
            require_once FUSION_PM_PATH . '/models/Message.php';
            require_once FUSION_PM_PATH . '/models/User.php';
            require_once FUSION_PM_PATH . '/models/Activity.php';
            require_once FUSION_PM_PATH . '/models/Folder.php';


            require_once FUSION_PM_PATH . '/classes/class-ajax.php';
            require_once FUSION_PM_PATH . '/classes/class-admin.php';
            require_once FUSION_PM_PATH . '/classes/class-localize.php';
        }
        
    }

    /**
    * Init all filters
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function init_filters() {
        // Load all filters
        add_filter( 'posts_where', array( $this, 'hide_others_uploads' ) );
        add_filter( 'upload_mimes', array( $this, 'restrict_mime' ) );
    }

    public function hide_others_uploads( $where ) {
        global $pagenow, $wpdb;

        if ( ( $pagenow == 'upload.php' || $pagenow == 'media-upload.php' || $pagenow == 'admin-ajax.php' ) ) {
            $user_id = get_current_user_id();
            $where .= " AND $wpdb->posts.post_author = $user_id";
        }

        return $where;
    }

    public function restrict_mime($mimes) { 
        $mimes = array( 
                    'jpg|jpeg|jpe'   => 'image/jpeg', 
                    'gif'            => 'image/gif', 
                    'png'            => 'image/png',
                    
                    'txt|asc|c|cc|h' => 'text/plain',
                    'csv'            => 'text/csv',
                    'css'            => 'text/css',
                    'htm|html'       => 'text/html',
                    
                    'js'             => 'application/javascript',
                    'pdf'            => 'application/pdf',
                    // 'swf'            => 'application/x-shockwave-flash',
                    'tar'            => 'application/x-tar',
                    'zip'            => 'application/zip',
                    'gz|gzip'        => 'application/x-gzip',
                    'rar'            => 'application/rar',
                    '7z'             => 'application/x-7z-compressed',
                    
                    'doc'            => 'application/msword',
                    'pot|pps|ppt'    => 'application/vnd.ms-powerpoint',
                    'docx'           => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'odt'            => 'application/vnd.oasis.opendocument.text',
                    'odp'            => 'application/vnd.oasis.opendocument.presentation',
        ); 
        return $mimes;
    }

    /**
    * Init all actions
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function init_actions() {
        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    /**
    * Inistantiate all classes
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function init_classes() {
        if ( is_admin() ) {
            FusionPM_Admin::init();
            FusionPM_Ajax::init();
            FusionPM_Localize::init();
        }
    }

    /**
     * Initialize plugin for localization
     *
     * @since 1.0.0
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'fusion-pm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Enqueue admin scripts
     *
     * @since 1.0.0
     *
     * Allows plugin assets to be loaded.
     *
     * @return void
     */
    public function enqueue_scripts() {

        wp_enqueue_style( 'fusion-pm-styles', FUSION_PM_ASSETS . '/css/style.css', false, date( 'Ymd' ) );
        wp_enqueue_script( 'fusion-pm-scripts', FUSION_PM_ASSETS . '/js/script.js', array( 'jquery' ), false, true );
    }

    /**
    * Load admin scripts
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function admin_enqueue_scripts( $hooks ) {
        wp_enqueue_media();
        
        wp_register_style( 'style_css', FUSION_PM_ASSETS . '/css/style.css', false, time() );
        wp_register_style( 'jquery-ui-style', FUSION_PM_ASSETS . '/css/jquery-ui.css', false, time() );
        wp_register_style( 'fusion-fontawesome', FUSION_PM_ASSETS . '/css/font-awesome.min.css', false, time() );
        wp_register_script( 'fusion-timepicker', FUSION_PM_ASSETS . '/vendor/jquery-ui-timepicker.js', array( 'jquery' ), false, true );
        wp_register_script( 'fusion-dropdown', FUSION_PM_ASSETS . '/vendor/dropdown.js', array( 'jquery' ), time(), true ); // array( 'jquery', 'fusion-vue' )
        wp_register_script( 'fusion-admin', FUSION_PM_ASSETS . '/dist/build.js', array( 'jquery', 'fusion-dropdown' ), time(), true ); 

        if ( 'toplevel_page_fusion-pm' == $hooks ) {
            wp_enqueue_style( 'style_css' );
            wp_enqueue_style( 'jquery-ui-style' );
            wp_enqueue_style( 'fusion-fontawesome' );
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'fusion-timepicker' );
            wp_enqueue_script( 'fusion-dropdown' );
            wp_enqueue_script( 'fusion-admin' );

            $currentuser = wp_get_current_user();
            $currentuser->avatar_url = get_avatar_url( $currentuser->ID );


            $localvar = array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'currentUserInfo' => $currentuser,
                'topLevelPage' => $hooks,
                'nonce' => wp_create_nonce( 'fusion-pm-nonce' )
            );

            wp_localize_script( 'fusion-admin', 'fpm', $localvar );
        }
    }

}

$fusion_pm = Fusion_PM::init();

register_activation_hook( __FILE__, array( 'Fusion_PM', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Fusion_PM', 'deactivate' ) );