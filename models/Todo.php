<?php 


/**
* 
*/
class FusionPM_Todo {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_todos';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Todo();
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

        $commentModel = FusionPM_Comment::init();

        $commentModel->delete_comments( $id, 'todo' );

        $result = $wpdb->delete(
            $this->table_name,
            array( 'ID' => $id )
        );
        return $result;
    
    }

    public function get_todos() {
        global $wpdb;

        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name}" );
        return $result;
    }

    public function get_todo_details( $todo_id ) {

        global $wpdb;

        $files_array = array();

        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `ID` = {$todo_id}" );

        $files = $result[0]->file_ids;
        $fileIDs = maybe_unserialize( $files );

        if( $fileIDs ) {
            foreach ($fileIDs as $ID) {
                $fileObject = new stdClass();
                $fileObject->ID = $ID;
                $fileObject->url = wp_get_attachment_url( $ID );
                // $fileObject->attachmentMeta = wp_get_attachment_metadata( $ID ); // have to talk to kukur
                array_push( $files_array, $fileObject );
            }
            $result[0]->files = $files_array;
            $result[0]->attachmentIDs = $fileIDs;
        } else {
            $result[0]->files = [];
            $result[0]->attachmentIDs = [];
        }

        if ( $result[0]->assigneeID ) {
            $result[0]->avatar_url = get_avatar_url($result[0]->assigneeID, array('size'=>15));
        } else {
            $result[0]->avatar_url = '';
        }
        $result[0]->formatted_created = $this->get_formatted_date( $result[0]->created );
        
        $commentModel = FusionPM_Comment::init();
        $listModel = FusionPM_List::init();

        $todoComments = $commentModel->get_comments_by_column_info( $todo_id, 'todo' );
        $result[0]->comments = $todoComments;
        $result[0]->list_info = $listModel->get_list_details( $result[0]->listID, true );
        if ( $result[0]->due_date ) {
            $result[0]->formatted_due_date = $this->get_formatted_date( $result[0]->due_date );
        } else {
            $result[0]->formatted_due_date = NULL;
        }

        return $result;
    }

    public function get_todos_by_column_info( $column , $param ) {
        global $wpdb;

        if( !$column ) {
            return [];
        }

        if(!$param) {
            return [];
        }

        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE {$column} = {$param}" );
        
        return $result;
    }

    public function complete_todo( $todo_id, $is_complete ) {
        global $wpdb;
        $result = $wpdb->update( 
            $this->table_name, 
            array( 
                'is_complete' => $is_complete
            ), 
            array( 'ID' => $todo_id )
        );
        return $result;
    }

    public function checkRelation( $projectID, $listID, $todoID ) {
        global $wpdb;
        
        $result = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `ID` = {$todoID} AND `projectID` = {$projectID} AND `listID` = {$listID}" );
        
        return $result;
    }

    public function delete_todos_by_column( $value, $column ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT `ID` FROM {$this->table_name} WHERE {$column} = {$value}" );

        foreach ($result as $todo) {
            $isSuccess = $this->delete($todo->ID);
        }

        return $isSuccess;
    }

}