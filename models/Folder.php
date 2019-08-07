<?php 


class FusionPM_Folder {

    protected $table_name;
    
    function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fpm_folders';
    }

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Folder();
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
        // var_dump($data);die();
        $update = $wpdb->update( $this->table_name, $data, $where );

        if ( $update ) {
            return true;
        }

        return false;
    }

    public function delete( $folder_id ) {
        global $wpdb;

        $commentModel = FusionPM_Comment::init();

        $commentModel->delete_comments( $folder_id, 'folder' );

        $result = $wpdb->delete( 
            $this->table_name,
            array( 'ID' => $folder_id )
        );
        return $result;
    }

    public function delete_file_comments( $file_id ) {
        global $wpdb;

        $commentModel = FusionPM_Comment::init();

        $result = $commentModel->delete_comments( $file_id, 'file' );

        // $result = $wpdb->delete( 
        //     $this->table_name,
        //     array( 'ID' => $folder_id )
        // );
        return $result;
    }

    public function delete_folders_by_column( $value, $column ) {

        global $wpdb;

        $result = $wpdb->get_results( "SELECT `ID` FROM {$this->table_name} WHERE {$column} = {$value}" );

        foreach ($result as $folder) {
            $isSuccess = $this->delete($folder->ID);
        }

        return $isSuccess;
    }

    public function get_folder_count( $project_id, $isArchive ) {
        global $wpdb;

        $folder_count = $wpdb->get_var( 
            "SELECT COUNT(*) FROM {$this->table_name} 
            WHERE `projectID` = {$project_id}
            AND `is_archive`= {$isArchive} " );

        return $folder_count;
    }

    public function get_folder_details( $projectid, $folderid ) {
        global $wpdb;

        $files_array = array();
        $result = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} WHERE `ID` = {$folderid} AND `projectID` = {$projectid}"
        );
        
        if ( !$result ) {
            return $result;
        }
        $result[0]->formatted_created = $this->get_formatted_date( $result[0]->created );

        $files = $result[0]->file_ids;
        $fileIDs = maybe_unserialize( $files );

        if ( $fileIDs ) {
            foreach ( $fileIDs as $ID ) {
                $fileObject = new stdClass();
                $fileObject->ID = $ID;
                $fileObject->url = wp_get_attachment_url( $ID );

                $filetype = wp_check_filetype( $fileObject->url );

                $fileObject->mime = $filetype['type'];
                $fileObject->extension = $filetype['ext'];
                $fileObject->icon = wp_mime_type_icon( $filetype['type'] );
                $fileObject->title = get_the_title($ID);
                // $fileObject->attachment_meta = wp_get_attachment_metadata( $ID );
                array_push( $files_array, $fileObject );
            }
            $result[0]->files = $files_array;
            $result[0]->attachmentIDs = $fileIDs;
        } else {
            $result[0]->files = [];
            $result[0]->attachmentIDs = [];
        }
        // $bookmarkModel = FusionPM_Bookmark::init();
        $commentModel = FusionPM_Comment::init();
        $result[0]->comments = $commentModel->get_comments_by_column_info( $folderid, 'folder' );

        // $bookmark = $bookmarkModel->get_bookmark( $projectid, $folderid, 'folder' );

        // if ( $bookmark->ID ) {
        //     $result[0]->bookmark_id = $bookmark->ID;
        //     $result[0]->is_bookmarked = true;
        // } else {
        //     $result[0]->is_bookmarked = false;
        // }

        return $result;
    }

    public function get_file_details( $projectid, $folderid, $fileid ) {
        global $wpdb;
        $result = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} WHERE `ID` = {$folderid} AND `projectID` = {$projectid}"
        );
        
        if ( $result ) {
            $fileObject = new stdClass();
            $ID = $fileid;
            $fileObject->ID = $ID;
            $fileObject->url = wp_get_attachment_url( $ID );

            $filetype = wp_check_filetype( $fileObject->url );

            $fileObject->mime = $filetype['type'];
            $fileObject->extension = $filetype['ext'];
            $fileObject->icon = wp_mime_type_icon( $filetype['type'] );
            $fileObject->title = get_the_title($ID);
            $authorID = get_post_field( 'post_author', $ID );
            $authorObj = get_user_by( 'ID', $authorID );
            $fileObject->author_name = $authorObj->display_name;
            $fileObject->userID = $authorID;
            // $fileObject->attachment_user = new WP_User( $authorID );
            $commentModel = FusionPM_Comment::init();
            $fileObject->comments = $commentModel->get_comments_by_column_info( $fileid, 'file' );

            // $bookmarkModel = FusionPM_Bookmark::init();

            // $bookmark = $bookmarkModel->get_bookmark( $projectid, $fileid, 'file' );

            // if ( $bookmark->ID ) {
            //     $fileObject->bookmark_id = $bookmark->ID;
            //     $fileObject->is_bookmarked = true;
            // } else {
            //     $fileObject->is_bookmarked = false;
            // }
            
            $result[0]->attachment = $fileObject;
            return $result;
        }
        return false;
    }

    public function get_folders_by_column_info( $column, $param, $isArchive, $limit = NULL, $offset = NULL ) {

        global $wpdb;

        if ( !$limit ) {
            $limit = 15;
        }

        if ( !$offset ) {
            $offset = 0;
        }

        $result = $wpdb->get_results( 
            "SELECT * FROM {$this->table_name} 
            WHERE {$column} = {$param}
            AND `is_archive`= {$isArchive} 
            ORDER BY `ID` DESC 
            LIMIT {$limit} OFFSET {$offset}"
        );

        foreach ( $result as $folderObject ) {
            $folderObject->avatar_url = get_avatar_url($folderObject->userID, array('size'=>70));
            $folderObject->formatted_created = $this->get_formatted_date( $folderObject->created );
        }

        return $result;
    }

}