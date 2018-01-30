<?php 


/**
* 
*/
class FusionPM_User {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'users';
        $this->relation_table_name = $wpdb->prefix . 'fpm_project_user';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_User();
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

    public function get_project_users( $projectID, $avatar_size = NULL, $limit = NULL ) {

        global $wpdb;

        if (!$limit) {
            $limit = 10;
        }
        if(!$avatar_size) {
            $avatar_size = 50;
        }
        
        $result = $wpdb->get_results(
            "SELECT {$this->table_name}.ID, {$this->table_name}.display_name, {$this->table_name}.user_email
             FROM  {$this->table_name} 
             INNER JOIN  {$this->relation_table_name} 
             ON {$this->table_name}.ID = {$this->relation_table_name}.userID 
             WHERE {$this->relation_table_name}.projectID = {$projectID}
             LIMIT {$limit}"
        );
        
        foreach ($result as $userObject) {
            $userObject->avatar_url = get_avatar_url($userObject->ID, array('size'=>$avatar_size));
            $userObject->title = get_user_meta($userObject->ID, 'fpm_user_title', true);
        }

        return $result;
    }

    public function remove_user( $user_id, $project_id ) {
        // global $wpdb;

        // $result = $wpdb->get_results( "DELETE FROM {$this->relation_table_name} WHERE `userID` = {$user_id} AND `projectID` = {$project_id}" );

        // return $result;
        global $wpdb;

        $result = $wpdb->delete(
            $this->relation_table_name,
            array( 
                'userID' => $user_id, 
                'projectID' => $project_id, 
            )
        );
        return $result;
    }

}