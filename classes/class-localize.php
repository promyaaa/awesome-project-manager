<?php

/**
* Ajax handling class
*/
class FusionPM_Localize {

    /**
    * Automatically loaded when class initiate
    *
    * @since 1.0.0
    */
    public function __construct() {
        add_action( 'wp_ajax_fpm-get-local-data', array( $this, 'get_localize_data' ), 10 );
    }

    /* class common methods */

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Localize();
        }

        return $instance;
    }

    /* Ajax Callbacks */

    public function get_localize_data() {
        // if ( $this->is_nonce_verified() ) {
        //     wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        // }
        wp_send_json_success(
            array(
                'actions' => array(
                    'save' => __( 'Save', 'fusion-pm' ),
                    'add_new' => __( 'Add New', 'fusion-pm' ),
                    'cancel' => __( 'Cancel', 'fusion-pm' )
                ),
                'text_messages' => array(
                    'test' => __('Test', 'fusion-pm')
                )
            )
        );
    }
    
}

