<?php 

class FusionPM_Bookmark {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_bookmarks';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Bookmark();
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

    // public function get_formatted_date( $date ) {
    //     $date_format = get_option( 'date_format' );
    //     $given_date = date_create( $date );
    //     $formatted_date = date_format( $given_date, $date_format );

    //     return $formatted_date;
    // }

    // public function get_formatted_time( $date ) {
    //     $time_format = get_option( 'time_format' );
    //     $given_date = date_create( $date );
    //     $formatted_time = date_format( $given_date, $time_format );

    //     return $formatted_time;
    // }

    public function create( $data ) {
        global $wpdb;

        $insert = $wpdb->insert( $this->table_name, $data );

        if ( $insert ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function delete( $bookmark_id ) {
        global $wpdb;

        $result = $wpdb->delete( 
            $this->table_name,
            array( 'ID' => $bookmark_id )
        );
        return $result;
    }

    public function delete_bookmark_by( $bookmark_id, $bookmark_type, $project_id ) {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            array( 
                'bookmark_id'   => $bookmark_id,
                'bookmark_type' => $bookmark_type,
                'projectID'     => $project_id
            )
        );
        return $result;
    }

    public function delete_bookmarks_by_column( $value, $column ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT `ID` FROM {$this->table_name} WHERE {$column} = {$value}" );

        foreach ($result as $activity) {
            $isSuccess = $this->delete($activity->ID);
        }

        return $isSuccess;
    }

    public function delete_bookmarks_by_columns( $value1, $column1, $value2, $column2 ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT `ID` FROM {$this->table_name} WHERE {$column1} = {$value1} AND {$column2} = '{$value2}'" );

        foreach ($result as $activity) {
            $isSuccess = $this->delete($activity->ID);
        }

        return $isSuccess;
    }

    public function get_bookmark_count( ) {
        global $wpdb;

        $currentUserID = get_current_user_id();
        
        $bookmark_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} 
                                                WHERE `userID` = {$currentUserID}");
        
        return $bookmark_count;
    }

    public function get_bookmark( $project_id, $bookmark_id, $bookmark_type ) {
        global $wpdb;

        $currentUserID = get_current_user_id();
        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} 
                                        WHERE `projectID` = {$project_id}
                                        AND `userID` = {$currentUserID}
                                        AND `bookmark_id` = {$bookmark_id}
                                        AND `bookmark_type` = '{$bookmark_type}'" );
        return $result[0];
    }

    public function get_bookmarks( $limit = NULL, $offset = NULL, $project_id = NULL ) {

        global $wpdb;

        if ( !$limit ) {
            $limit = 20;
        }

        if ( !$offset ) {
            $offset = 0;
        }

        $currentUserID = get_current_user_id();
        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} 
                                            WHERE `userID` = {$currentUserID} 
                                            ORDER BY `ID` DESC LIMIT {$limit} 
                                            OFFSET {$offset}" );
        
        
        if ( $result ) {
            foreach ($result as $bookmark) {
                // $bookmark->avatar_url = get_avatar_url($bookmark->userID, array('size'=>32));
                $bookmark->formatted_date = $this->get_formatted_date( $bookmark->created );
                // $bookmark->formatted_time = $this->get_formatted_time( $bookmark->created );
            }
            $result[0]->total_bookmark = $this->get_bookmark_count();
        }

        return $result;
    }

}