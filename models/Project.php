<?php 


/**
* 
*/
class FusionPM_Project {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_projects';
        $this->relation_table_name = $wpdb->prefix . 'fpm_project_user';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Project();
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

    public function updateRelation( $projectID, $userID = NULL ) {
        global $wpdb;

        if ( !$userID ) {
            $userID = get_current_user_id();
        }
        
        $insert = $wpdb->insert(
            $this->relation_table_name,
            array(
                'userID' => $userID,
                'projectID' => $projectID
            )
        );

        if ( $insert ) {
            return true;
        }

        return false;
    }

    public function get_project_user_count( $project_id ) {
        global $wpdb;
        $user_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->relation_table_name} WHERE `projectID` = {$project_id}" );
        return $user_count;
    }

    public function get_project_message_count( $project_id ) {
        
        $messageModel = FusionPM_Message::init();
        $message_count = $messageModel->get_message_count( $project_id );

        return $message_count;
    }

    public function get_project_list_count( $project_id ) {
        
        $listModel = FusionPM_List::init();
        $list_count = $listModel->get_list_count( $project_id );

        return $list_count;
    }

    public function get_project_count( $user_id ) {
        global $wpdb;
        if (current_user_can('manage_options')) {
            $project_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name}" );
        } else {
            $project_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->relation_table_name} WHERE `userID` = {$user_id}" );
        }
        return $project_count;
    }

    public function create( $data ) {
        global $wpdb;

        $insert = $wpdb->insert( $this->table_name, $data );
        $projectID = $wpdb->insert_id;

        if ( $insert ) {
            $update = $this->updateRelation( $projectID );

            if ( $update ) {
                return $projectID;
            }
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

    public function get_project( $project_id ) {
        global $wpdb;
        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `ID` = {$project_id}" );
        $result[0]->user_count = $this->get_project_user_count( $project_id );
        $result[0]->message_count = $this->get_project_message_count( $project_id );
        $result[0]->list_count = $this->get_project_list_count( $project_id );
        return $result;
    }

    public function get_projects( $limit = NULL, $offset = NULL ) {

        global $wpdb;

        if ( !$limit ) {
            $limit = 15;
        }

        if ( !$offset ) {
            $offset = 0;
        }

        $projects = array();
        $userModel = FusionPM_User::init();
        $current_user_id = get_current_user_id();
        if (current_user_can('manage_options')) {
            $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} LIMIT {$limit} OFFSET {$offset}" );

            foreach ($result as $project) {
                $project->user_count = $this->get_project_user_count( $project->ID );
                $project->users = $userModel->get_project_users( $project->ID, 32, 5 );
                array_push($projects, $project);
            }
        } else {
            // $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `userID` = $current_user_id" );
            $result = $wpdb->get_results(
                "SELECT {$this->table_name}.ID, 
                        {$this->table_name}.project_title, 
                        {$this->table_name}.project_desc,
                        {$this->table_name}.created
                 FROM  {$this->table_name} 
                 LEFT JOIN  {$this->relation_table_name} 
                 ON {$this->table_name}.ID = {$this->relation_table_name}.projectID 
                 WHERE {$this->relation_table_name}.userID = {$current_user_id}
                 LIMIT {$limit}
                 OFFSET {$offset}"
            );

            foreach ($result as $project) {
                $project->users = $userModel->get_project_users( $project->ID, 32, 5 );
                $project->user_count = $this->get_project_user_count( $project->ID );
                array_push($projects, $project);
            }
        }

        return $projects;
    }

    public function checkRelation( $projectID, $userID ) {
        global $wpdb;
        
        $result = $wpdb->get_results( "SELECT * FROM {$this->relation_table_name} WHERE `userID` = {$userID} AND `projectID` = {$projectID}" );
        
        return $result;
    }

    // Check user role method
    // function isSiteAdmin(){
    //     return in_array('administrator',  wp_get_current_user()->roles);
    // }

}