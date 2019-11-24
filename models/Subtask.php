<?php 

class FusionPM_Subtask {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_subtasks';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Subtask();
        }

        return $instance;
    }

    public function get_formatted_date( $date ) {
        $date_format = get_option( 'date_format' );
        $given_date = date_create( $date );
        $formatted_date = date_format( $given_date, $date_format );

        return $formatted_date;
    }

    public function get_formatted_time( $date ) {
        $time_format = get_option( 'time_format' );
        $given_date = date_create( $date );
        $formatted_time = date_format( $given_date, $time_format );

        return $formatted_time;
    }

    public function create( $data ) {
        global $wpdb;

        $insert = $wpdb->insert( $this->table_name, $data );

        if ( $insert ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function delete( $subtask_id ) {
        global $wpdb;

        $result = $wpdb->delete( 
            $this->table_name,
            array( 'ID' => $subtask_id )
        );
        return $result;
    }

    public function complete_subtask( $task_id, $is_complete ) {
        global $wpdb;
        $result = $wpdb->update( 
            $this->table_name, 
            array( 
                'is_complete' => $is_complete
            ), 
            array( 'ID' => $task_id )
        );
        return $result;
    }


    public function get_subtasks_by( $todo_id ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `todoID` = {$todo_id}" );

        return $result;
    }

}