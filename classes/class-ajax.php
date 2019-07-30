<?php

/**
* Ajax handling class
*/
class FusionPM_Ajax {

    /**
    * Automatically loaded when class initiate
    *
    * @since 1.0.0
    */
   
    protected $userObject;


    public function __construct(  ) {
        // Common
        add_action( 'wp_ajax_fpm-get-user-avatar', array( $this, 'get_user_avatar' ), 10 );
        // PROJECT
        add_action( 'wp_ajax_fpm-get-projects', array( $this, 'fetch_projects' ), 10 );
        add_action( 'wp_ajax_fpm-get-project', array( $this, 'fetch_project' ), 10 );
        add_action( 'wp_ajax_fpm-insert-project', array( $this, 'insert_project' ), 10 );
        add_action( 'wp_ajax_fpm-delete-project', array( $this, 'delete_project' ), 10 );

        add_action( 'wp_ajax_fpm-get-project-count', array( $this, 'fetch_project_count' ), 10 );
        add_action( 'wp_ajax_fpm-load-more-projects', array( $this, 'load_more_projects' ), 10 );

        add_action( 'wp_ajax_fpm-load-more-messages', array( $this, 'load_more_messages' ), 10 );
        add_action( 'wp_ajax_fpm-load-more-lists', array( $this, 'load_more_lists' ), 10 );
        add_action( 'wp_ajax_fpm-load-more-users', array( $this, 'load_more_users' ), 10 );
        add_action( 'wp_ajax_fpm-load-more-activities', array( $this, 'load_more_activities' ), 10 );
        add_action( 'wp_ajax_fpm-get-activities', array( $this, 'fetch_activities' ), 10 );


        // LIST
        add_action( 'wp_ajax_fpm-get-lists', array( $this, 'fetch_lists' ), 10 );
        add_action( 'wp_ajax_fpm-create-list', array( $this, 'create_list' ), 10 );
        add_action( 'wp_ajax_fpm-delete-list', array( $this, 'delete_list' ), 10 );
        add_action( 'wp_ajax_fpm-get-list-details', array( $this, 'fetch_list_details' ), 10 );

        // TODO
        add_action( 'wp_ajax_fpm-complete-todo', array( $this, 'complete_todo' ), 10 );
        add_action( 'wp_ajax_fpm-insert-todo', array( $this, 'insert_todo' ), 10 );
        add_action( 'wp_ajax_fpm-delete-todo', array( $this, 'delete_todo' ), 10 );
        add_action( 'wp_ajax_fpm-get-todo-details', array( $this, 'fetch_todo_details' ), 10 );
        add_action( 'wp_ajax_fpm-get-todos', array( $this, 'fetch_todos' ), 10 );

        // COMMENT
        add_action( 'wp_ajax_fpm-insert-comment', array( $this, 'insert_comment' ), 10 );
        add_action( 'wp_ajax_fpm-delete-comment', array( $this, 'delete_comment' ), 10 );

        // MESSAGE
        add_action( 'wp_ajax_fpm-get-messages', array( $this, 'fetch_messages' ), 10 );
        add_action( 'wp_ajax_fpm-insert-message', array( $this, 'insert_message' ), 10 );
        add_action( 'wp_ajax_fpm-delete-message', array( $this, 'delete_message' ), 10 );
        add_action( 'wp_ajax_fpm-get-message-details', array( $this, 'fetch_message_details' ), 10 );

        // USERS
        add_action( 'wp_ajax_fpm-get-users', array( $this, 'fetch_users' ), 10 );
        add_action( 'wp_ajax_fpm-insert-user', array( $this, 'insert_user' ), 10 );
        add_action( 'wp_ajax_fpm-remove-user', array( $this, 'remove_user' ), 10 );
        add_action( 'wp_ajax_fpm-update-user', array( $this, 'update_user' ), 10 );

        // Folders
        add_action( 'wp_ajax_fpm-insert-folder', array( $this, 'insert_folder' ), 10 );
        add_action( 'wp_ajax_fpm-get-folders', array( $this, 'fetch_folders' ), 10 );
        add_action( 'wp_ajax_fpm-get-folder-details', array( $this, 'fetch_folder_details' ), 10 );
        add_action( 'wp_ajax_fpm-get-file-details', array( $this, 'fetch_file_details' ), 10 );
        add_action( 'wp_ajax_fpm-delete-folder', array( $this, 'delete_folder' ), 10 );
                
    }

    /* class common methods */

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Ajax();
        }

        return $instance;
    }

    public function is_nonce_verified() {
        // returns false if nonce field is ok
        return isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'fusion-pm-nonce' );
    }

    public function get_validated_input( $field_name ) {
        return !empty( $_POST[$field_name] ) ? $_POST[$field_name] : '';
    }

    public function get_comments( $id, $type ) {
        $commentModel = FusionPM_Comment::init();
        $result = $commentModel->get_comments_by_column_info( $id, $type );
        return $result;
    }

    
    // public function create_activity( $data ) {
    //     $activityModel = FusionPM_Activity::init();
        
    //     if ( $data['activity_type'] === 'delete_todo' ) {
    //         $activityModel->delete_activities_by_column( $data['activity_id'], 'create_todo', $data['projectID'] );
    //         $activityModel->delete_activities_by_column( $data['activity_id'], 'check_todo', $data['projectID'] );
    //         $activityModel->delete_activities_by_column( $data['activity_id'], 'uncheck_todo', $data['projectID'] );
    //     }
    //     if ( $data['activity_type'] === 'delete_message' ) {
    //         $activityModel->delete_activities_by_column( $data['activity_id'], 'create_message', $data['projectID'] );
    //     }
    //     $activityModel->create( $data );
    // }

    // updated
    public function create_activity( $data ) {
        $activityModel = FusionPM_Activity::init();
        $activityModel->create( $data );
    }

    public function delete_activity( $activity_id, $activity_type, $project_id, $activity_meta=NULL ) {
        $activityModel = FusionPM_Activity::init();
        $activityModel->delete( $activity_id, $activity_type, $project_id, $activity_meta );
    }

    public function fetch_folders() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Nonce Verification failed', 'fusion-pm' )
                )
            );
        }

        $projectID = $this->get_validated_input('project_id');
        $isArchive = !empty( $_POST['is_archive'] ) ? (int)$_POST['is_archive'] : 0;

        if ( ! $projectID ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Project ID not found', 'fusion-pm' )
                )
            );
        }

        $offset      = $this->get_validated_input('offset');
        $limit       = $this->get_validated_input('limit');
        $folderModel = FusionPM_Folder::init();
        $folders     = $folderModel->get_folders_by_column_info('projectID', $projectID, $isArchive, $limit, $offset);
        
        if ( ! $offset && $folders ) {
            $folders[0]->folder_count = $folderModel->get_folder_count( $projectID, $isArchive );
            $folders[0]->a_folder_count = $folderModel->get_folder_count( $projectID, 1 );
        }
        if ( $folders ) {
            wp_send_json_success( $folders );
        }
        wp_send_json_error( 
            array(
                'message' => __( 'something went wrong', 'fusion-pm' )
            )
        );
    }

    public function fetch_file_details() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Nonce Verification failed', 'fusion-pm' )
                )
            );
        }

        $projectID = $this->get_validated_input('project_id');
        $folderID = $this->get_validated_input('folder_id');
        $fileID = $this->get_validated_input('file_id');

        if ( ! $projectID ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Project ID not found', 'fusion-pm' )
                )
            );
        }
        if ( ! $folderID ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Folder ID not found', 'fusion-pm' )
                )
            );
        }
        if ( ! $fileID ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'File ID not found', 'fusion-pm' )
                )
            );
        }

        $folderModel = FusionPM_Folder::init();

        $file = $folderModel->get_file_details( $projectID, $folderID, $fileID );

        if ( $file ) {
            wp_send_json_success( $file );
        }
        wp_send_json_error( 
            array(
                'message' => __( 'something went wrong', 'fusion-pm' )
            )
        );
    }

    public function fetch_folder_details() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Nonce Verification failed', 'fusion-pm' )
                )
            );
        }

        $projectID = $this->get_validated_input('project_id');

        $folderID = $this->get_validated_input('folder_id');

        if ( ! $projectID ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Project ID not found', 'fusion-pm' )
                )
            );
        }

        if ( ! $folderID ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Folder ID not found', 'fusion-pm' )
                )
            );
        }

        $folderModel = FusionPM_Folder::init();

        $folder = $folderModel->get_folder_details( $projectID, $folderID );

        if ( $folder ) {
            wp_send_json_success( $folder );
        }
        wp_send_json_error( 
            array(
                'message' => __( 'something went wrong', 'fusion-pm' )
            )
        );
    }

    public function delete_folder() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Nonce Verification failed', 'fusion-pm' )
                )
            );
        }

        $folderID = $this->get_validated_input('folder_id'); 
        $projectID = $this->get_validated_input('project_id'); 
        $folderTitle = $this->get_validated_input('folder_title'); 

        if ( ! $folderID ) {
            wp_send_json_error( __( 'folder ID not provided', 'fusion-pm' ) );
        }
        $currentUser = wp_get_current_user();

        $folderModel = FusionPM_Folder::init();
        $delete = $folderModel->delete( $folderID );
        $date = current_time( 'mysql' );
        if ( $delete ) {
            $activities = array(
                'userID'        => $currentUser->ID,
                'user_name'     => $currentUser->display_name,
                'projectID'     => $projectID,
                'activity_id'   => $folderID,
                'activity_type' => 'delete_folder',
                'activity'      => $folderTitle,
                'created'       => $date
            );

            $this->create_activity( $activities );
            $this->delete_activity( 
                $activities['activity_id'], 
                'create_folder', 
                $activities['projectID'] 
            );
            $this->delete_activity( 
                $activities['activity_id'], 
                'update_folder',
                $activities['projectID'] 
            );
            // $this->delete_bookmark( $folderID, 'folder', $projectID );
            wp_send_json_success( array('message' => __( 'Folder successfully deleted', 'fusion-pm' )) );
        }

        wp_send_json_error( 
            array(
                'message' => __( 'something went wrong', 'fusion-pm' )
            )
        );
    }

    public function insert_folder() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( 
                array(
                    'message' => __( 'Nonce Verification failed', 'fusion-pm' )
                )
            );
        }

        $folderTitle  = $this->get_validated_input('folder_title');

        $fileTitle    = $this->get_validated_input('file_title');
        
        $projectID    = $this->get_validated_input('project_id');
        
        $projectTitle = $this->get_validated_input('project_title');
        
        $folderID     = $this->get_validated_input('folder_id');
        
        $activityType = $this->get_validated_input('ac_type');
        
        $fileID       = $this->get_validated_input('file_id');

        if ( ! $folderID ) {
            if ( ! $projectID ) {
                wp_send_json_error( 
                    array(
                        'message' => __( 'Project ID not found', 'fusion-pm' )
                    )
                );
            }

            if ( ! $folderTitle ) {
                wp_send_json_error( 
                    array(
                        'message' => __( 'Folder title not found', 'fusion-pm' )
                    )
                );
            }
        }
        
        $folderModel = FusionPM_Folder::init();

        $currentUser = wp_get_current_user();

        $date = current_time( 'mysql' );

        if ( $folderID ) {
            $data = array();
            $fileIDs = !empty( $_POST['attachments'] ) ? $_POST['attachments'] : [];
            
            if ( $fileID ) {
                $deleteComments = $folderModel->delete_file_comments( $fileID );
            }
            // if ( $fileIDs ) {
            $data['file_ids'] = maybe_serialize( $fileIDs );
            // }
            // $data = array(
            //     'file_ids' => maybe_serialize( $fileIDs )
            // );
            if ( $folderTitle ) {
                $data['folder_title'] = $folderTitle;
            }
            // var_dump($data);die();
            $where = array( 'ID' => intval( $folderID ) );
            $successResponse = $folderModel->update( $data, $where );
        } else {
            $data = array(
                'folder_title'  => $folderTitle,
                'userID'        => $currentUser->ID,
                'user_name'     => $currentUser->display_name,
                'projectID'     => $projectID,
                'project_title' => $projectTitle,
                'created'       => $date
            );
            $insertID = $folderModel->create( $data );
        }

        $resp = array(
            'folder' => array(
                'ID' => $insertID ? $insertID : $folderID,
                'created' => $date
            )
        );

        if ( $insertID ) {
            $resp['message'] = __( 'Folder created successfully', 'fusion-pm' );
        } else {
            $resp['message'] = __( 'Folder updated successfully', 'fusion-pm' );
        } 

        if ( $insertID || $successResponse ) {
            $activityID = $insertID ? $insertID : $folderID;
            $activities = array(
                'userID'        => $currentUser->ID,
                'user_name'     => $currentUser->display_name,
                'projectID'     => $projectID,
                'activity_id'   => $activityID,
                'activity_type' => $activityType,
                'activity'      => $folderTitle,
                'created'       => $date
            );

            if ( $activityType === 'add_file' ) {
                $activities['parentID'] = $folderID;
                $activities['activity_id'] = $fileID;
                $resp['message'] = __( 'File added to this folder successfully', 'fusion-pm' );
            }

            if ( $activityType === 'delete_file' ) {
                $activities['parentID'] = $folderID;
                $activities['activity_id'] = $fileID;
                $activities['activity'] = $fileTitle .' from '.$folderTitle;

                // $this->delete_bookmark( $fileID, 'file', $projectID );

                $resp['message'] = __( 'File deleted from this folder successfully', 'fusion-pm' );
                
                $this->delete_activity( 
                    $activities['activity_id'], 
                    'add_file', 
                    $activities['projectID'] 
                );
            }

            $this->create_activity( $activities );
            wp_send_json_success( $resp );
        }

        wp_send_json_error( 
            array(
                'message' => __( 'something went wrong', 'fusion-pm' )
            )
        );
    }

    /* Ajax Callbacks */
    public function update_user() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $userID = $this->get_validated_input('user_id');

        $userEmail = $this->get_validated_input('email');

        $userTitle = $this->get_validated_input('title');

        if( ! $userEmail ) {
            wp_send_json_error( __( 'email is required', 'fusion-pm' ) );
        }
        if ( $userEmail ) {
            $updateID = wp_update_user( array( 'ID' => $userID, 'user_email' => $userEmail ) );
        }
        if ( $userTitle ) {
            $updateMeta = update_user_meta( $userID, 'fpm_user_title', $userTitle );
        }

        if ( $updateID || $updateMeta ) {
            $resp = array(
                'message' => __( 'Successfully updated', 'fusion-pm' ),
                'user' => array(
                    'ID' => $userID
                )
            );

            wp_send_json_success( $resp );
        } else {
            wp_send_json_error( __( 'Something went wrong!!', 'fusion-pm' ) );
        }
    }

    public function remove_user() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $userID = $this->get_validated_input('user_id');

        $projectID = $this->get_validated_input('project_id');

        if( ! $projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        if( ! $userID ) {
            wp_send_json_error( __( 'userid not provided', 'fusion-pm' ) );
        }

        $userModel = FusionPM_User::init();

        $result = $userModel->remove_user( $userID, $projectID );

        if ( $result ) {
            wp_send_json_success( __( 'Successfully removed from project', 'fusion-pm' ));
        } else {
            wp_send_json_error( __( 'Something went wrong!!', 'fusion-pm' ) );
        }
    }

    public function insert_user () {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $user_name = $this->get_validated_input('user_name');

        $email_address = $this->get_validated_input('email');

        $projectID = $this->get_validated_input('project_id');

        $projectTitle = $this->get_validated_input('project_title');

        $title = $this->get_validated_input('title');

        if( ! $projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        if( ! $email_address ) {
            wp_send_json_error( __( 'email is required', 'fusion-pm' ) );
        }

        if( ! $user_name ) {
            wp_send_json_error( __( 'username is required', 'fusion-pm' ) );
        }

        $projectObject = FusionPM_Project::init();

        $user_id = email_exists( $email_address );

        $project_link = add_query_arg( array( 'page'=>'fusion-pm'), admin_url( 'admin.php' ) ) . '#/projects/' . $projectID;

        if( !$user_id ) {

            // Generate the password and create the user
            $password = wp_generate_password( 6, false );
            $user_id = wp_create_user( $user_name, $password, $email_address );

            add_user_meta( $user_id, 'fpm_user_title', $title );

            wp_send_new_user_notifications( $user_id, 'user' );

            // Set the role
            $user = new WP_User( $user_id );
            $user->set_role( 'fpm_member' );

            $related = $projectObject->updateRelation( $projectID, $user_id );

            $project_link = add_query_arg( array( 'page'=>'fusion-pm'), admin_url( 'admin.php' ) ) . '#/projects/' . $projectID;

            // Email the user
            wp_mail( 
                $email_address, 
                'Invitation!', 
                'You have been invited to project - "'. $projectTitle .'". You can access the project via following link - ' . $project_link 
            );

            $resp = array(
                'message' => __( 'Successfully added to this project', 'fusion-pm' ),
                'user' => array(
                    'ID' => $user_id,
                    'user_name' => $user->user_login,
                    'avatar_url' => get_avatar_url($user_id, array('size'=>50))
                )
            );

            if ( $user_id ) {
                wp_send_json_success( $resp );
            }

        } 
        if( $user_id ) {
            $user = new WP_User( $user_id );
            
            if ( $title ) {
                update_user_meta( $user_id, 'fpm_user_title', $title );
            }

            $isRelated = $projectObject->checkRelation($projectID, $user_id);
            
            // Email the user
            wp_mail( 
                $email_address, 
                'Invitation!', 
                'You have been invited to project - "'. $projectTitle .'". You can access the project via following link - ' . $project_link 
            );

            if ($isRelated) {
                wp_send_json_success(
                    array(
                        'message' => __( 'Already added to this project', 'fusion-pm' )
                    )
                );
            }

            $makeRelation = $projectObject->updateRelation( $projectID, $user_id );

            $resp = array(
                'message' => __( 'Successfully added to this project', 'fusion-pm' ),
                'user' => array(
                    'ID' => $user_id,
                    'user_name' => $user->user_login,
                    'avatar_url' => get_avatar_url($user_id, array('size'=>50))
                )
            );

            wp_send_json_success( $resp );
        }

    }

    public function fetch_users() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        if( !$projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $userModel = FusionPM_User::init();

        $users = $userModel->get_project_users( $projectID );

        if ( $users ) {
            wp_send_json_success( $users );
        }
        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function fetch_message_details() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        $messageID = $this->get_validated_input('message_id');

        if( !$projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        if( !$messageID ) {
            wp_send_json_error( __( 'message id not provided', 'fusion-pm' ) );
        }

        $messageModel = FusionPM_Message::init();

        $message = $messageModel->get_message_details( $projectID, $messageID );

        if ( $message ) {
            wp_send_json_success( $message );
        }
        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function insert_message() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $message = $this->get_validated_input('message');

        $messageTitle = $this->get_validated_input('message_title');

        $projectTitle = $this->get_validated_input('project_title');

        $projectID = $this->get_validated_input('project_id');

        $messageID = $this->get_validated_input('message_id');

        $fileIDs = !empty( $_POST['attachments'] ) ? $_POST['attachments'] : [];

        if( ! $projectID ) {
            wp_send_json_error( __( 'project id not provided', 'fusion-pm' ) );
        }

        if( ! $messageTitle ) {
            wp_send_json_error( __( 'message Title is required', 'fusion-pm' ) );
        }

        $messageModel = FusionPM_Message::init();

        $userObject = wp_get_current_user();

        $date = current_time( 'mysql' );

        $data = array(
            'message' => wp_kses_post( $message ),
            'message_title' => $messageTitle,
            'userID' => $userObject->ID,
            'projectID' => $projectID,
            'user_name' => $userObject->user_login,
            'project_title' => $projectTitle,
            'file_ids' => maybe_serialize( $fileIDs )
        );

        if ( $messageID ) {
            $where = array( 'ID' => intval($messageID) );
            $successResponse = $messageModel->update( $data, $where );
        } else {
            $data['created'] = $date;
            $insertID = $messageModel->create( $data );
        }

        $resp = array(
            'message' => __( 'Successfully inserted', 'fusion-pm' ),
            'messageInfo' => array(
                'ID' => $insertID ? $insertID : $messageID,
                'message' => $message,
                'message_title' => $messageTitle,
                'created' => $date
            )
        );

        if ( $insertID ) {
            $activities = array(
                'userID'        => $userObject->ID,
                'user_name'     => $userObject->user_login,
                'projectID'     => $projectID,
                'activity_id'   => $insertID,
                'activity_type' => 'create_message',
                'activity'      => $messageTitle,
                'created'       => $date
            );

            $this->create_activity($activities);
        }

        if ( $insertID || $successResponse ) {
            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function delete_message() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $messageID = $this->get_validated_input('message_id'); 
        $userID = $this->get_validated_input('user_id'); 
        $userName = $this->get_validated_input('user_name'); 
        $projectID = $this->get_validated_input('project_id'); 
        $messageTitle = $this->get_validated_input('message_title'); 

        if( ! $messageID ) {
            wp_send_json_error( __( 'messageid not provided', 'fusion-pm' ) );
        }
        $userObject = wp_get_current_user();
        $messageModel = FusionPM_Message::init();
        $delete = $messageModel->delete( $messageID );
        $date = current_time( 'mysql' );
        if( $delete ) {
            $activities = array(
                'userID' => $userObject->ID,
                'user_name' => $userObject->user_login,
                'projectID' => $projectID,
                'activity_id' => $messageID,
                'activity_type' => 'delete_message',
                'activity' => $messageTitle,
                'created' => $date
            );

            $this->create_activity($activities);
            wp_send_json_success( array('message' => __( 'message successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function fetch_messages() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        if(!$projectID) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }
        $limit = !empty( $_POST['limit'] ) ? $_POST['limit'] : 15;

        $messageModel = FusionPM_Message::init();
        // $lists = $listModel->get_lists();
        $messages = $messageModel->get_messages_by_column_info( 'projectID', $projectID, $limit );

        if ($messages) {
            wp_send_json_success( $messages );
        }
        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function delete_comment() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $commentID = $this->get_validated_input('comment_id');

        if( ! $commentID ) {
            wp_send_json_error( __( 'commentid not provided', 'fusion-pm' ) );
        }

        $commentObject = FusionPM_Comment::init();
        $delete = $commentObject->delete( $commentID );

        if( $delete ) {
            wp_send_json_success( array('message' => __( 'Successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function insert_comment() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $commentableID = $this->get_validated_input('commentable_id');
        $commentableType = $this->get_validated_input('commentable_type');
        $comment = $this->get_validated_input('comment');
        $commentID = $this->get_validated_input('comment_id');

        if( ! $projectID ) {
            wp_send_json_error( __( 'commentid not provided', 'fusion-pm' ) );
        }
        if( ! $commentableID ) {
            wp_send_json_error( __( 'commentable id not provided', 'fusion-pm' ) );
        }
        if( ! $commentableType ) {
            wp_send_json_error( __( 'comment type not provided', 'fusion-pm' ) );
        }
        if( ! $comment ) {
            wp_send_json_error( __( 'comment not provided', 'fusion-pm' ) );
        }
        
        $commentModel = FusionPM_Comment::init();
        
        // $date = date("Y-m-d H:i:s"); 

        $date = current_time( 'mysql' );

        $current_user = wp_get_current_user();

        $data = array(
            'comment' => wp_kses_post($comment),
            'commentable_id' => $commentableID,
            'commentable_type' => $commentableType,
            'projectID' => $projectID,
            'userID' =>  $current_user->ID,
            'user_name' =>  $current_user->user_login
        );

        if ( $commentID ) {
            $where = array( 'ID' => intval( $commentID ) );
            $successResponse = $commentModel->update( $data, $where );
        } else {
            $data['created'] = $date;
            $insertID = $commentModel->create( $data );
        }

        $resp = array(
            'message' => __( 'Successfully inserted', 'fusion-pm' ),
            'comment' => array(
                'ID' => $insertID ? $insertID : $commentID,
                'avatar_url' => get_avatar_url($current_user->ID, array('size'=>32)),
                'created' => $date
            )
        );
        if ( $insertID || $successResponse ) {
            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );     
    }

    public function fetch_todos() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $assigneeID = get_current_user_id();

        $orderBy = $this->get_validated_input('order_by');

        $todoModel = FusionPM_Todo::init();

        $todos = $todoModel->get_todos_by_column_info( 'assigneeID', $assigneeID );

        if ( $todos ) {
            wp_send_json_success( $todos );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );

    }

    public function fetch_todo_details() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }
        
        $listID = $this->get_validated_input('list_id');

        $projectID = $this->get_validated_input('project_id');

        $todoID = $this->get_validated_input('todo_id');

        if ( ! $listID ) {
            wp_send_json_error( __( 'listID is required', 'fusion-pm' ) );
        }

        if ( ! $projectID ) {
            wp_send_json_error( __( 'projectID is required', 'fusion-pm' ) );
        }

        if ( ! $todoID ) {
            wp_send_json_error( __( 'todoID is required', 'fusion-pm' ) );
        }

        $todoModel = FusionPM_Todo::init();

        $isRelated = $todoModel->checkRelation( $projectID, $listID, $todoID );
        
        if ( $isRelated ) {
            $todo = $todoModel->get_todo_details( $todoID );
            
            wp_send_json_success( $todo );    
        }

        wp_send_json_error(
            array(
                'message' => __( 'Not related models, what are you upto??', 'fusion-pm' )
            )
        );
    }

    public function fetch_list_details() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $listID = $this->get_validated_input('list_id');

        $projectID = $this->get_validated_input('project_id');

        if ( ! $listID ) {
            wp_send_json_error( __( 'listID is required', 'fusion-pm' ) );
        }

        if ( ! $projectID ) {
            wp_send_json_error( __( 'projectID is required', 'fusion-pm' ) );
        }

        $listModel = FusionPM_List::init();

        $isRelated = $listModel->checkRelation( $projectID, $listID );
        
        if ( $isRelated ) {
            $listObject = $listModel->get_list_details( $listID );
            
            wp_send_json_success( $listObject ); 
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    } 

    // updated
    public function insert_todo() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $todo = !empty( $_POST['todo'] ) ? $_POST['todo'] : '';

        $listID = !empty( $_POST['list_id'] ) ? $_POST['list_id'] : '';

        $projectID = !empty( $_POST['project_id'] ) ? $_POST['project_id'] : '';

        $todoID = !empty( $_POST['todo_id'] ) ? $_POST['todo_id'] : '';

        $userID = !empty( $_POST['user_id'] ) ? $_POST['user_id'] : '';

        $userName = !empty( $_POST['user_name'] ) ? $_POST['user_name'] : '';

        $dueDate = !empty( $_POST['due_date'] ) ? $_POST['due_date'] : NULL;

        $fileIDs = !empty( $_POST['attachments'] ) ? $_POST['attachments'] : [];

        $assigneeID = !empty( $_POST['assignee_id'] ) ? $_POST['assignee_id'] : NULL;

        $assigneeName = !empty( $_POST['assignee_name'] ) ? $_POST['assignee_name'] : NULL;

        if ( ! $todo ) {
            wp_send_json_error( __( 'Todo is required', 'fusion-pm' ) );
        }

        if ( ! $listID ) {
            wp_send_json_error( __( 'listID is required', 'fusion-pm' ) );
        }

        if ( ! $projectID ) {
            wp_send_json_error( __( 'projectID is required', 'fusion-pm' ) );
        }

        if ( ! $userName ) {
            wp_send_json_error( __( 'user name is required', 'fusion-pm' ) );
        }

        $todosModel = FusionPM_Todo::init();
        
        $date = current_time( 'mysql' );
        $userObject = wp_get_current_user();

        $data = array(
            'todo' => $todo,
            'listID' => $listID,
            'projectID' => $projectID,
            'userID' => $userObject->ID,
            'user_name' => $userObject->user_login,
            'assignee_name' => $assigneeName,
            'assigneeID' => $assigneeID,
            'file_ids' => maybe_serialize( $fileIDs ),
            'due_date' => $dueDate
        );

        if ( $todoID ) {
            $where = array( 'ID' => intval( $todoID ) );
            $successResponse = $todosModel->update( $data, $where );
        } else {
            $data['created'] = $date;
            $insertID = $todosModel->create( $data );
        }

        if ( $dueDate ) {
            $overdue = false;
            if ( current_time( 'mysql' ) > $dueDate ) {
                $overdue = true;
            }
        }

        $resp = array(
            'message' => __( 'operation successful', 'fusion-pm' ),
            'todo' => array(
                'ID' => $insertID ? $insertID : $todoID,
                'due_date' => $dueDate ? $dueDate : NULL,
                'formatted_due_date' => $dueDate ? $todosModel->get_formatted_date( $dueDate ) : '', 
                'formatted_created' => $todosModel->get_formatted_date( $date ),
                'is_overdue' => $overdue,
                'created' => $date
            )
        );

        if ( $insertID ) {
            $activities = array(
                'userID'        => $userObject->ID,
                'user_name'     => $userObject->user_login,
                'projectID'     => $projectID,
                'parentID'      => $listID,
                'activity_id'   => $insertID,
                'activity_type' => 'create_todo',
                'activity'      => $todo,
                'created'       => $date
            );

            $this->create_activity($activities);
        }

        if ( $insertID || $successResponse ) {
            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function delete_todo() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $todoID = $this->get_validated_input('todo_id');
        $projectID = $this->get_validated_input('project_id');
        $todo = $this->get_validated_input('todo');

        if( ! $todoID ) {
            wp_send_json_error( __( 'todoid not provided', 'fusion-pm' ) );
        }

        $todoObject = FusionPM_Todo::init();
        $delete = $todoObject->delete( $todoID );

        $userObject = get_user_by( 'ID', get_current_user_id() );
        $date = current_time( 'mysql' );
        if( $delete ) {
            $activities = array(
                'userID' => $userObject->ID,
                'user_name' => $userObject->user_login,
                'projectID' => $projectID,
                'activity_id' => $todoID,
                'activity_type' => 'delete_todo',
                'activity' => $todo,
                'created' => $date
            );

            $this->create_activity($activities);

            wp_send_json_success( array('message' => __( 'Successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function complete_todo() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }
        
        $todoID = !empty( $_POST['todo_id'] ) ? $_POST['todo_id'] : '';
        $listID = !empty( $_POST['list_id'] ) ? $_POST['list_id'] : '';
        $projectID = !empty( $_POST['project_id'] ) ? $_POST['project_id'] : '';
        // $userID = !empty( $_POST['user_id'] ) ? $_POST['user_id'] : '';
        // $userName = !empty( $_POST['user_name'] ) ? $_POST['user_name'] : '';
        $todo = !empty( $_POST['todo'] ) ? $_POST['todo'] : '';
        $currentUser = wp_get_current_user();
        if( ! $todoID ) {
            wp_send_json_error( __( 'todoid not provided', 'fusion-pm' ) );
        }

        $is_complete = (int)$_POST['is_complete'];

        $todoObject = FusionPM_Todo::init();
        $update = $todoObject->complete_todo( $todoID, $is_complete );

        $date = current_time( 'mysql' );

        $activities = array(
            'userID'      => $currentUser->ID,
            'user_name'   => $currentUser->user_login,
            'projectID'   => $projectID,
            'parentID'    => $listID,
            'activity_id' => $todoID,
            'activity'    => $todo,
            'created'     => $date
        );

        if ( $is_complete ) {
            $activities['activity_type'] = 'check_todo';
            $this->create_activity($activities);
        } else {
            $activities['activity_type'] = 'uncheck_todo';
            $this->create_activity($activities);
        }
        
        if( $update ) {
            $resp = array(
                'message' => __( 'Successfully Updated', 'fusion-pm' )
            );
            wp_send_json_success( $resp );
        }

    }

    public function create_list() {

        global $wpdb;

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $list_title = $this->get_validated_input('title');

        $projectID = $this->get_validated_input('project_id');

        $listID = $this->get_validated_input('list_id');

        $userName = $this->get_validated_input('user_name');

        if( ! $projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        if( ! $list_title ) {
            wp_send_json_error( __( 'Title is required', 'fusion-pm' ) );
        }

        if ( ! $userName ) {
            wp_send_json_error( __( 'User Name is required', 'fusion-pm' ) );
        }

        $date = current_time( 'mysql' );
        $userObject = wp_get_current_user();

        $data = array(
            'list_title' => $list_title,
            'userID' => $userObject->ID,
            'user_name' => $userObject->user_login,
            'projectID' => $projectID
        );

        $listModel = FusionPM_List::init();

        if ( $listID ) {
            $where = array( 'ID' => intval($listID) );
            $successResponse = $listModel->update( $data, $where );
        } else {
            $data['created'] = $date;
            $insertID = $listModel->create( $data );
        }

        $resp = array(
            'message' => __( 'Successfully inserted', 'fusion-pm' ),
            'listInfo' => array(
                'ID' => $insertID ? $insertID : $listID,
                'created' => $date
            )
        );

        if ( $insertID || $successResponse ) {
            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function delete_list() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $listID = $this->get_validated_input('list_id');

        if( ! $listID ) {
            wp_send_json_error( __( 'listid not provided', 'fusion-pm' ) );
        }

        $listModel = FusionPM_List::init();
        $delete = $listModel->delete( $listID );

        if( $delete ) {
            wp_send_json_success( array('message' => __( 'Successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function fetch_lists() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = !empty( $_POST['project_id'] ) ? $_POST['project_id'] : '';

        if(!$projectID) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $limit = !empty( $_POST['limit'] ) ? $_POST['limit'] : '';
        $listModel = FusionPM_List::init();
        $lists = $listModel->get_lists_by_column_info('projectID', $projectID, $limit);

        wp_send_json_success( $lists );
    }

    public function insert_project() {
        global $wpdb;

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $project_title = !empty( $_POST['title'] ) ? $_POST['title'] : '';
        $project_desc = !empty( $_POST['description'] ) ? $_POST['description'] : '';
        $projectID = !empty( $_POST['project_id'] ) ? $_POST['project_id'] : '';

        if ( ! $project_title ) {
            wp_send_json_error( __( 'Title is required', 'fusion-pm' ) );
        }

        if ( ! $project_desc ) {
            wp_send_json_error( __( 'Description is required', 'fusion-pm' ) );
        }
        
        $date = current_time( 'mysql' );

        $data = array(
            'project_title' => $project_title,
            'project_desc' => $project_desc,
            'userID' => get_current_user_id()
        );

        $projectModel = FusionPM_Project::init();
        
        if ( $projectID ) {
            $where = array( 'ID' => intval($projectID) );
            $successResponse = $projectModel->update( $data, $where );
        } else {
            $data['created'] = $date;
            $insertID = $projectModel->create( $data );
        }

        $resp = array(
            'message' => __( 'Successfully inserted', 'fusion-pm' ),
            'project' => array(
                'ID' => $insertID ? $insertID : $projectID,
                'created' => $date
            )
        );

        if ( $insertID || $successResponse ) {
            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );

    }

    public function delete_project() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id'); 

        if( ! $projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }
        
        $projectModel = FusionPM_Project::init();
        $delete = $projectModel->delete( $projectID );

        if( $delete ) {
            wp_send_json_success( array('message' => __( 'message successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function load_more_projects() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $offset = $this->get_validated_input('offset');
        $limit = 15;

        $projectModel = FusionPM_Project::init();
        $projects = $projectModel->get_projects( $limit, $offset );

        wp_send_json_success( $projects );
    }

    public function load_more_messages() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $offset = $this->get_validated_input('offset');
        $limit = 15;

        $messageModel = FusionPM_Message::init();
        $messages = $messageModel->get_messages_by_column_info( 'projectID', $projectID, $limit, $offset );

        wp_send_json_success( $messages );
    }

    public function load_more_lists() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $offset = $this->get_validated_input('offset');
        $limit = 10;

        $listModel = FusionPM_List::init();
        $lists = $listModel->get_lists_by_column_info( 'projectID', $projectID, $limit, $offset );

        wp_send_json_success( $lists );
    }

    public function load_more_users() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $offset = $this->get_validated_input('offset');
        $limit = 15;
        $avatar_size = 50;

        $userModel = FusionPM_User::init();
        $users = $userModel->get_project_users( $projectID, $avatar_size, $limit, $offset );

        wp_send_json_success( $users );
    }

    public function load_more_activities() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $offset = $this->get_validated_input('offset');
        $limit = 15;

        $activityModel = FusionPM_Activity::init();
        $activites = $activityModel->get_project_activities( $projectID, $limit, $offset );

        wp_send_json_success( $activites );
    }

    public function fetch_project_count() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectModel = FusionPM_Project::init();
        $projectCount = $projectModel->get_project_count( get_current_user_id() );

        wp_send_json_success( $projectCount );
    }

    public function fetch_projects() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectModel = FusionPM_Project::init();
        $projects = $projectModel->get_projects();

        wp_send_json_success( $projects );
    }

    public function fetch_project() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        $isSummary = !empty( $_POST['is_summary'] ) ? $_POST['is_summary'] : NULL;

        if( !$projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $projectModel = FusionPM_Project::init();
        $project = $projectModel->get_project( $projectID, $isSummary );

        if ( $project ) {
            wp_send_json_success( $project );
        }
        wp_send_json_error( __( 'something went wrong', 'fusion-pm' ) );
    }
    

    public function fetch_activities() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verification failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        $activityModel = FusionPM_Activity::init();
        $activites = $activityModel->get_project_activities( $projectID );

        if ($activites) {
            wp_send_json_success( $activites );
        }
        wp_send_json_error( __( 'something went wrong', 'fusion-pm' ) );
    }
}

