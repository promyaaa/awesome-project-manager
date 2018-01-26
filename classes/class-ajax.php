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

        // add_action( 'wp_ajax_fpm-get-message-count', array( $this, 'fetch_message_count' ), 10 );
        add_action( 'wp_ajax_fpm-load-more-messages', array( $this, 'load_more_messages' ), 10 );
        add_action( 'wp_ajax_fpm-load-more-lists', array( $this, 'load_more_lists' ), 10 );
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
        // TODO: implement this method to all other functions where nonce check is required
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

    // updated
    public function create_activity( $data ) {
        $activityModel = FusionPM_Activity::init();
        $activityModel->create( $data );
    }

    /* Ajax Callbacks */
    public function update_user() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $user_name = $this->get_validated_input('user_name');

        $email_address = $this->get_validated_input('email');

        $projectID = $this->get_validated_input('project_id');

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

        $user_id = username_exists( $user_name );

        if( !$user_id and email_exists($email_address) == false ) {

            // Generate the password and create the user
            $password = wp_generate_password( 6, false );
            $user_id = wp_create_user( $user_name, $password, $email_address );

            add_user_meta( $user_id, 'fpm_user_title', $title );
            // Set the nickname
            // wp_update_user(
            //     array(
            //       'ID'          =>    $user_id,
            //       'nickname'    =>    $email_address
            //     )
            // );

            // Set the role
            $user = new WP_User( $user_id );
            $user->set_role( 'member' );

            // $projectObject = FusionPM_Project::init();
            $related = $projectObject->updateRelation($projectID, $user_id);

            // Email the user
            wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );

            $resp = array(
                'message' => __( 'Successfully added to this project', 'fusion-pm' ),
                'user' => array(
                    'ID' => $user_id,
                    'avatar_url' => get_avatar_url($user_id, array('size'=>50))
                )
            );

            if ( $user_id ) {
                wp_send_json_success( $resp );
            }

        } 
        if( $user_id ) {
            $user = new WP_User( $user_id );

            update_user_meta( $user_id, 'fpm_user_title', $title );

            $isRelated = $projectObject->checkRelation($projectID, $user_id);

            if ($isRelated) {
                wp_send_json_success(
                    array(
                        'message' => __( 'Already added to this project', 'fusion-pm' )
                    )
                );
            }

            $makeRelation = $projectObject->updateRelation($projectID, $user_id);

            // Email the user
            // wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );

            $resp = array(
                'message' => __( 'Successfully added to this project', 'fusion-pm' ),
                'user' => array(
                    'ID' => $user_id,
                    'avatar_url' => get_avatar_url($user_id, array('size'=>50))
                )
            );

            wp_send_json_success( $resp );
        }

    }

    public function fetch_users() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        if( !$projectID ) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $userModel = FusionPM_User::init();

        $users = $userModel->get_project_users( $projectID );

        // var_dump(get_role('member'));
        // die();

        if ( $users ) {
            wp_send_json_success( $users );
        }
        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function fetch_message_details() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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

        // var_dump(get_role('member'));
        // die();

        if ( $message ) {
            wp_send_json_success( $message );
        }
        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function insert_message() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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

        // $projectClass = FusionPM_Project::init();

        $userObject = get_user_by( 'ID', get_current_user_id() );

        // $projectObj = $projectClass->get_project( $projectID );

        $date = date("Y-m-d H:i:s");
        $data = array(
            'message' => wp_kses_post( $message ),
            'message_title' => $messageTitle,
            'userID' => $userObject->ID,
            'projectID' => $projectID,
            'user_name' => $userObject->display_name,
            // 'project_title' => $projectObj[0]->project_title,
            'project_title' => $projectTitle,
            'file_ids' => maybe_serialize( $fileIDs ),
            'created' => $date
        );

        if ( $messageID ) {
            $where = array( 'ID' => intval($messageID) );
            $successResponse = $messageModel->update( $data, $where );
        } else {
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

        if ( $insertID || $successResponse ) {
            $activityID = $insertID ? $insertID : $messageID;
            $activities = array(
                'userID' => $userObject->ID,
                'user_name' => $userObject->display_name,
                'projectID' => $projectID,
                'activity_id' => $activityID,
                'activity_type' => $insertID ? 'create_message' : 'update_message',
                'activity' => $messageTitle,
                'created' => $date
            );

            $this->create_activity($activities);

            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function delete_message() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $messageID = $this->get_validated_input('message_id'); 
        $userID = $this->get_validated_input('user_id'); 
        $userName = $this->get_validated_input('user_name'); 
        $projectID = $this->get_validated_input('project_id'); 
        $messageTitle = $this->get_validated_input('message_title'); 

        if( ! $messageID ) {
            wp_send_json_error( __( 'messageid not provided', 'fusion-pm' ) );
        }

        $messageModel = FusionPM_Message::init();
        $delete = $messageModel->delete( $messageID );

        if( $delete ) {
            $activities = array(
                'userID' => $userID,
                'user_name' => $userName,
                'projectID' => $projectID,
                'activity_id' => $messageID,
                'activity_type' => 'delete_message',
                'activity' => $messageTitle,
                'created' => date("Y-m-d H:i:s")
            );

            $this->create_activity($activities);
            wp_send_json_success( array('message' => __( 'message successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function fetch_messages() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $commentID = $this->get_validated_input('comment_id'); // !empty( $_POST['todo_id'] ) ? $_POST['todo_id'] : '';

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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');        
        $commentableID = $this->get_validated_input('commentable_id');        
        $commentableType = $this->get_validated_input('commentable_type');        
        $comment = $this->get_validated_input('comment');
        $commentID = $this->get_validated_input('comment_id');
        // $userName = $this->get_validated_input('user_name');

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
        
        $date = date("Y-m-d H:i:s"); 
        $current_user = wp_get_current_user(); // TODO: Discuss with kukur

        $data = array(
            'comment' => wp_kses_post($comment),
            'commentable_id' => $commentableID,
            'commentable_type' => $commentableType,
            'projectID' => $projectID,
            'userID' =>  $current_user->ID,
            'user_name' =>  $current_user->display_name,
            'created' => $date
        );

        if ( $commentID ) {
            $where = array( 'ID' => intval($commentID) );
            $successResponse = $commentModel->update( $data, $where );
        } else {
            $insertID = $commentModel->create( $data );
        }

        $resp = array(
            'message' => __( 'Successfully inserted', 'fusion-pm' ),
            'comment' => array(
                'ID' => $insertID ? insertID : $commentID,
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $assigneeID = get_current_user_id();

        $todoModel = FusionPM_Todo::init();

        $todos = $todoModel->get_todos_by_column_info( 'assigneeID', $assigneeID );

        if ( $todos ) {
            wp_send_json_success( $todos );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );

    }

    public function fetch_todo_details() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            // $listObject = $listModel->get_lists_by_colum_info( 'ID', $listID );
            $listObject = $listModel->get_list_details( $listID );
            
            wp_send_json_success( $listObject );    
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    } 

    // updated
    public function insert_todo() {

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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

        // if ( ! $todoID ) {
        //     wp_send_json_error( __( 'todoID is required', 'fusion-pm' ) );
        // }

        if ( ! $listID ) {
            wp_send_json_error( __( 'listID is required', 'fusion-pm' ) );
        }

        if ( ! $projectID ) {
            wp_send_json_error( __( 'projectID is required', 'fusion-pm' ) );
        }

        // if ( ! $userID ) {
        //     wp_send_json_error( __( 'userID is required', 'fusion-pm' ) );
        // }

        if ( ! $userName ) {
            wp_send_json_error( __( 'user name is required', 'fusion-pm' ) );
        }

        $todosModel = FusionPM_Todo::init();
        
        $date = date("Y-m-d H:i:s");

        $data = array(
            'todo' => $todo,
            'listID' => $listID,
            'userID' => get_current_user_id(),
            'projectID' => $projectID,
            'userID' => $userID,
            'user_name' => $userName,
            'assignee_name' => $assigneeName,
            'assigneeID' => $assigneeID,
            'file_ids' => maybe_serialize( $fileIDs ),
            'due_date' => $dueDate,
            'created' => $date
        );

        if ( $todoID ) {
            $where = array( 'ID' => intval($todoID) );
            $successResponse = $todosModel->update( $data, $where );
        } else {
            $insertID = $todosModel->create( $data );
        }

        $resp = array(
            'message' => __( 'Successfully inserted', 'fusion-pm' ),
            'todo' => array(
                'ID' => $insertID ? $insertID : $todoID,
                'created' => $date
            )
        );

        if ( $insertID || $successResponse ) {
            $activityID = $insertID ? $insertID : $todoID;
            $activities = array(
                'userID' => $userID ? $userID : get_current_user_id(),
                'user_name' => $userName,
                'projectID' => $projectID,
                'listID' => $listID,
                'activity_id' => $activityID,
                'activity_type' => $insertID ? 'create_todo' : 'update_todo',
                'activity' => $todo,
                'created' => $date
            );

            $this->create_activity($activities);

            wp_send_json_success( $resp );
        }

        wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
    }

    public function delete_todo() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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

        if( $delete ) {
            $activities = array(
                'userID' => $userObject->ID,
                'user_name' => $userObject->display_name,
                'projectID' => $projectID,
                // 'listID' => $listID,
                'activity_id' => $todoID,
                'activity_type' => 'delete_todo',
                'activity' => $todo,
                'created' => date("Y-m-d H:i:s")
            );

            $this->create_activity($activities);

            wp_send_json_success( array('message' => __( 'Successfully deleted', 'fusion-pm' )) );
        } else {
            wp_send_json_error( __( 'Something wrong, try again', 'fusion-pm' ) );
        }
    }

    public function complete_todo() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }
        
        $todoID = !empty( $_POST['todo_id'] ) ? $_POST['todo_id'] : '';
        $listID = !empty( $_POST['list_id'] ) ? $_POST['list_id'] : '';
        $projectID = !empty( $_POST['project_id'] ) ? $_POST['project_id'] : '';
        $userID = !empty( $_POST['user_id'] ) ? $_POST['user_id'] : '';
        $userName = !empty( $_POST['user_name'] ) ? $_POST['user_name'] : '';
        $todo = !empty( $_POST['todo'] ) ? $_POST['todo'] : '';

        if( ! $todoID ) {
            wp_send_json_error( __( 'todoid not provided', 'fusion-pm' ) );
        }

        $is_complete = (int)$_POST['is_complete'];

        $todoObject = FusionPM_Todo::init();
        $update = $todoObject->complete_todo( $todoID, $is_complete );

        $activities = array(
            'userID' => $userID,
            'user_name' => $userName,
            'projectID' => $projectID,
            'listID' => $listID,
            'activity_id' => $todoID,
            'activity' => $todo,
            'created' => date("Y-m-d H:i:s")
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            // $userObject = get_user_by( 'ID', get_current_user_id() );
            // $userName = $userObject->display_name;
        }

        // $table = $wpdb->prefix . 'fpm_lists';
        $date = date("Y-m-d H:i:s");
        $data = array(
            'list_title' => $list_title,
            'userID' => get_current_user_id(),
            'user_name' => $userName,
            'projectID' => $projectID,
            'created' => $date
        );

        $listModel = FusionPM_List::init();

        if ( $listID ) {
            $where = array( 'ID' => intval($listID) );
            $successResponse = $listModel->update( $data, $where );
        } else {
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
        // global $wpdb;
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = !empty( $_POST['project_id'] ) ? $_POST['project_id'] : '';

        if(!$projectID) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $limit = !empty( $_POST['limit'] ) ? $_POST['limit'] : '';
        $listModel = FusionPM_List::init();
        // $lists = $listModel->get_lists();
        $lists = $listModel->get_lists_by_column_info('projectID', $projectID, $limit);

        wp_send_json_success( $lists );
    }

    public function insert_project() {
        global $wpdb;

        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
        
        // $projectObject = FusionPM_Project::init();

        // $table = $wpdb->prefix . 'fpm_projects';
        
        $date = date("Y-m-d H:i:s");

        $data = array(
            'project_title' => $project_title,
            'project_desc' => $project_desc,
            'userID' => get_current_user_id(),
            'created' => $date
        );

        $projectModel = FusionPM_Project::init();
        
        if ( $projectID ) {
            $where = array( 'ID' => intval($projectID) );
            $successResponse = $projectModel->update( $data, $where );
        } else {
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $offset = $this->get_validated_input('offset');
        $limit = 15;

        $projectModel = FusionPM_Project::init();
        $projects = $projectModel->get_projects( $limit, $offset );

        wp_send_json_success( $projects );
    }

    public function load_more_messages() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
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
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $offset = $this->get_validated_input('offset');
        $limit = 10;

        $listModel = FusionPM_List::init();
        $lists = $listModel->get_lists_by_column_info( 'projectID', $projectID, $limit, $offset );

        wp_send_json_success( $lists );
    }

    public function load_more_activities() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');
        $offset = $this->get_validated_input('offset');
        $limit = 10;

        $activityModel = FusionPM_Activity::init();
        $activites = $activityModel->get_project_activities( $projectID, $limit, $offset );

        wp_send_json_success( $activites );
    }

    public function fetch_project_count() {
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectModel = FusionPM_Project::init();
        $projectCount = $projectModel->get_project_count( get_current_user_id() );

        wp_send_json_success( $projectCount );
    }

    // public function fetch_message_count() {
    //     if ( $this->is_nonce_verified() ) {
    //         wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
    //     }

    //     $projectID = $this->get_validated_input('project_id');

    //     $messageModel = FusionPM_Message::init();
    //     $messageCount = $messageModel->get_message_count( $projectID );

    //     wp_send_json_success( $messageCount );
    // }

    public function fetch_projects() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectModel = FusionPM_Project::init();
        $projects = $projectModel->get_projects();

        wp_send_json_success( $projects );
    }

    public function fetch_project() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        if(!$projectID) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $projectModel = FusionPM_Project::init();
        $project = $projectModel->get_project( $projectID );

        if ($project) {
            wp_send_json_success( $project );
        }
        wp_send_json_error( __( 'something went wrong', 'fusion-pm' ) );
    }
    

    public function fetch_activities() {
        
        if ( $this->is_nonce_verified() ) {
            wp_send_json_error( __( 'Nonce Verified failed.. Cheating uhhh?', 'fusion-pm' ) );
        }

        $projectID = $this->get_validated_input('project_id');

        if(!$projectID) {
            wp_send_json_error( __( 'projectid not provided', 'fusion-pm' ) );
        }

        $activityModel = FusionPM_Activity::init();
        $activites = $activityModel->get_project_activities( $projectID );

        if ($activites) {
            wp_send_json_success( $activites );
        }
        wp_send_json_error( __( 'something went wrong', 'fusion-pm' ) );
    }
}

