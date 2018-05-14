<?php 


/**
* 
*/
class FusionPM_List {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_lists';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_List();
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

    public function delete( $list_id ) {
        global $wpdb;

        $commentModel = FusionPM_Comment::init();
        $todoModel = FusionPM_Todo::init();

        $commentModel->delete_comments( $list_id, 'list' );

        $todoModel->delete_todos_by_column( $list_id, 'listID' );
        
        $result = $wpdb->delete(
            $this->table_name,
            array( 'ID' => $list_id )
        );
        return $result;
        
    }

    public function delete_lists_by_column( $value, $column ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT `ID` FROM {$this->table_name} WHERE {$column} = {$value}" );

        foreach ($result as $list) {
            $isSuccess = $this->delete($list->ID);
        }

        return $isSuccess;
    }

    public function get_list_count( $project_id ) {
        global $wpdb;

        $list_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table_name} WHERE `projectID` = {$project_id}" );
        
        return $list_count;
    }

    public function get_list_details( $list_id, $summary = NULL ) {

        global $wpdb;

        if ( $summary ) {
            $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `ID` = {$list_id}" );
            return $result[0];
        }

        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `ID` = {$list_id}" );

        if ( $result ) {
            $todoModel = FusionPM_Todo::init();
            $projectModel = FusionPM_Project::init();

            $result[0]->formatted_created = $this->get_formatted_date( $result[0]->created );
            $result[0]->todos = $todoModel->get_todos_by_column_info( 'listID', $list_id );
            $result[0]->project_info = $projectModel->get_project( $result[0]->projectID, true );
        }

        return $result;
    }

    public function get_lists_by_column_info( $column, $param, $limit = NULL, $offset = NULL ) {
        global $wpdb;

        if (!$limit) {
            $limit = 10;
        }
        if (!$offset) {
            $offset = 0;
        }

        if( !$column ) {
            return [];
        }

        if(!$param) {
            return [];
        }
        
        $result = $wpdb->get_results( 
            "SELECT * FROM {$this->table_name} WHERE {$column} = {$param} ORDER BY `ID` DESC LIMIT {$limit} OFFSET {$offset}"
        );

        $todoModel = FusionPM_Todo::init();

        foreach ($result as $listObject) {
            
            $listObject->todos = $todoModel->get_todos_by_column_info( 'listID', $listObject->ID );
        }

        return $result;
    }

    public function checkRelation( $projectID, $listID ) {
        global $wpdb;
        
        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `ID` = {$listID} AND `projectID` = {$projectID}" );
        
        return $result;
    }

}