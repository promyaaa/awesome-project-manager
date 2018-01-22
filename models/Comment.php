<?php 


class FusionPM_Comment {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_comments';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Comment();
        }

        return $instance;
    }

    public function get_formatted_date( $date ) {
        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );

        $datetime_format = $date_format.' '.$time_format;

        $given_date = date_create( $date );
        $formatted_date = date_format( $given_date, $datetime_format );

        return $formatted_date;
    }

    public function create( $data ) {
        global $wpdb;

        $insert = $wpdb->insert( $this->table_name, $data );

        if ( $insert ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function update( $data, $where ) {
        global $wpdb;

        $update = $wpdb->update( $this->table_name, $data, $where );

        if ( $update ) {
            return true;
        }

        return false;
    }

    public function delete( $id ) {
        global $wpdb;
        $result = $wpdb->delete( 
            $this->table_name,
            array( 'ID' => $id )
        );
        return $result;
    }

    public function get_comments_by_column_info( $id, $type, $limit = NULL ) {

        global $wpdb;

        $final_result = array();
        $files_array = array();
        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `commentable_id` = {$id} AND `commentable_type` = '{$type}'", ARRAY_A );


        
        foreach ($result as $commentObject) {
            $commentObject['avatar_url'] = get_avatar_url($commentObject['userID'], array('size'=>32));
            $files = $commentObject['file_ids'];
            $fileIDs = maybe_unserialize( $files );
            if ($fileIDs) {
                foreach ($fileIDs as $ID) {
                    $fileObject = new stdClass();
                    $fileObject->ID = $ID;
                    $fileObject->url = wp_get_attachment_url( $ID );
                    array_push( $files_array, $fileObject );
                }
                $commentObject['files'] = $files_array;
                $commentObject['attachmentIDs'] = $fileIDs;
            } else {
                $commentObject['files'] = [];
                $commentObject['attachmentIDs'] = [];
            }
            
            array_push($final_result, $commentObject);
        }
        return $final_result;
    }

    public function delete_comments( $id, $type ) {

        global $wpdb;

        $result = $wpdb->get_results( "DELETE FROM {$this->table_name} WHERE `commentable_id` = {$id} AND `commentable_type` = '{$type}'" );
        return $result;
    }

}