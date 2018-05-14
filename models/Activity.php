<?php 

class FusionPM_Activity {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_activities';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Activity();
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

    public function delete( $activity_id ) {
        global $wpdb;

        $result = $wpdb->delete( 
            $this->table_name,
            array( 'ID' => $activity_id )
        );
        return $result;
    }

    public function delete_activities_by_column( $activity_id, $activity_type, $project_id ) {

        global $wpdb;

        $result = $wpdb->delete( 
            $this->table_name,
            array( 
                'activity_id'   => $activity_id,
                'activity_type' => $activity_type,
                'projectID'     => $project_id
            )
        );

        return $result;
    }

    public function delete_activities_by_columns( $value1, $column1, $value2, $column2 ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT `ID` FROM {$this->table_name} WHERE {$column1} = {$value1} AND {$column2} = '{$value2}'" );

        foreach ($result as $activity) {
            $isSuccess = $this->delete($activity->ID);
        }

        return $isSuccess;
    }

    public function get_activity_count( $project_id ) {
        global $wpdb;

        if ( $project_id ) {
            $activity_count = $wpdb->get_var( 
                "SELECT COUNT(*) FROM {$this->table_name} 
                WHERE `projectID` = {$project_id}" 
            );
        } else {
            $userid = get_current_user_id();
            $activity_count = $wpdb->get_var( 
                "SELECT COUNT(*) FROM {$this->table_name} 
                WHERE `userID` = {$userid}" 
            );
        }

        return $activity_count;
    }

    public function get_project_activities( $project_id, $limit = NULL, $offset = NULL ) {

        global $wpdb;

        if ( !$limit ) {
            $limit = 20;
        }

        if ( !$offset ) {
            $offset = 0;
        }

        if ( $project_id ) {
            $result = $wpdb->get_results( 
                "SELECT * FROM {$this->table_name} 
                WHERE `projectID` = {$project_id} 
                ORDER BY `ID` DESC LIMIT {$limit} 
                OFFSET {$offset}" 
            );
        } else {
            $userid = get_current_user_id();
            $result = $wpdb->get_results( 
                "SELECT * FROM {$this->table_name} 
                WHERE `userID` = {$userid} 
                ORDER BY `ID` DESC LIMIT {$limit} 
                OFFSET {$offset}" 
            );
        }

        if ( $result ) {
            foreach ($result as $activity) {
                $activity->avatar_url = get_avatar_url($activity->userID, array('size'=>32));
                $activity->formatted_date = $this->get_formatted_date( $activity->created );
                $activity->formatted_time = $this->get_formatted_time( $activity->created );
            }
            $result[0]->total_activity = $this->get_activity_count( $project_id );
        }

        return $result;
    }

}