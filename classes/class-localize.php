<?php

/**
* Ajax handling class
*/
class FusionPM_Localize {

    /**
    * Automatically loaded when class initiate
    *
    * @since 1.0.0
    */
    public function __construct() {
        add_action( 'wp_ajax_fpm-get-local-data', array( $this, 'get_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-home-local-data', array( $this, 'home_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-myassignment-local-data', array( $this, 'myassignment_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-summary-local-data', array( $this, 'summary_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-todo-lists-local-data', array( $this, 'todolists_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-single-todo-local-data', array( $this, 'single_todo_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-messages-local-data', array( $this, 'messages_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-single-message-local-data', array( $this, 'single_messages_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-new-message-local-data', array( $this, 'new_messages_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-edit-message-local-data', array( $this, 'edit_messages_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-users-local-data', array( $this, 'get_users_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-project-edit-local-data', array( $this, 'get_project_edit_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-comments-local-data', array( $this, 'comments_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-single-file-local-data', array( $this, 'single_file_localize_data' ), 10 );
        add_action( 'wp_ajax_fpm-get-folder-local-data', array( $this, 'folder_localize_data' ), 10 );

    }

    /* class common methods */

    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new FusionPM_Localize();
        }

        return $instance;
    }

    /**
     * Get home localize data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function home_localize_data() {
        $localize_data = array(
            'my_assignments'                  => __( 'My Assignments', 'fusion-pm' ),
            'projects'                        => __( 'Projects', 'fusion-pm' ),
            'add_new_project'                 => __( 'Add New Project', 'fusion-pm' ),
            'no_prject_found_message'         => __( 'No Project added yet. Hit the +Add button to add one.', 'fusion-pm' ),
            'project_title_placeholder'       => __( 'Enter your project title ...', 'fusion-pm' ),
            'project_description_placeholder' => __( 'Write some details about your project ...', 'fusion-pm' ),
            'create_project_label'            => __( 'Create Project', 'fusion-pm' ),
            'cancel_project_label'            => __( 'Cancel', 'fusion-pm' ),
            'loading'                         => __( 'Loading ...', 'fusion-pm' ),
            'load_more'                         => __( 'Load More...', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get myassignment_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function myassignment_localize_data() {
        $localize_data = array(
            'my_assignments' => __( 'My Assignments', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get myassignment_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function summary_localize_data() {
        $localize_data = array(
            'edit_info'                => __( 'Edit info', 'fusion-pm' ),
            'activities'                => __( 'Activities', 'fusion-pm' ),
            'delete'                   => __( 'Delete', 'fusion-pm' ),
            'people'                   => __( 'People', 'fusion-pm' ),
            'add_remove_people'        => __( 'Add/Remove People...', 'fusion-pm' ),
            'todos'                    => __( 'To-dos', 'fusion-pm' ),
            'docs_and_files'           => __( 'Docs & Files', 'fusion-pm' ),
            'message_board'            => __( 'Discussion', 'fusion-pm' ),
            'project_activity'         => __( 'Project Activity', 'fusion-pm' ),
            'create_a_todo'            => __( 'created a Todo', 'fusion-pm' ),
            'updated_a_todo'           => __( 'updated a Todo', 'fusion-pm' ),
            'deleted_a_todo'           => __( 'deleted a Todo', 'fusion-pm' ),
            'deleted_a_todo'           => __( 'deleted a Todo', 'fusion-pm' ),
            'checked_off_todo'         => __( 'checked off a Todo', 'fusion-pm' ),
            'reopen_a_todo'            => __( 're-open a Todo', 'fusion-pm' ),
            'created_a_message_called' => __( 'created a Topic, called', 'fusion-pm' ),
            'udpated_a_message_called' => __( 'updated a Topic, called', 'fusion-pm' ),
            'deleted_a_message_called' => __( 'deleted a Topic, called', 'fusion-pm' ),
            'no_activity_yet'          => __( 'No activiy yet.', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get todolists_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function todolists_localize_data() {
        $localize_data = array(
            'make_list_btn'         => __( 'Make List', 'fusion-pm' ),
            'todos'                 => __( 'To-Dos', 'fusion-pm' ),
            'name_list_placeholder' => __( 'Enter the name of the list...', 'fusion-pm' ),
            'create_list'           => __( 'Create List', 'fusion-pm' ),
            'cancel'                => __( 'Cancel', 'fusion-pm' ),
            'loading'               => __( 'Loading ...', 'fusion-pm' ),
            'no_list_added_yet'     => __( 'No Lists Added Yet', 'fusion-pm' ),
            'load_more_btn'         => __( 'Load More...', 'fusion-pm' ),
            'edit'                  => __( 'Edit', 'fusion-pm' ),
            'delete'                => __( 'Delete', 'fusion-pm' ),
            'update'           => __( 'Update', 'fusion-pm' ),
            'add_new_todo'           => __( 'Add New Todo', 'fusion-pm' ),
            'add_todo'           => __( 'Add todo', 'fusion-pm' ),
            'add_todo_placeholder'           => __( 'Enter your todo...', 'fusion-pm' ),
            'select_user'           => __( '--select user--', 'fusion-pm' ),
            'add_files'           => __( 'Add files', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get single_todo_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function single_todo_localize_data() {
        $localize_data = array(
            'assign_to_label'      => __( 'Assigned To :', 'fusion-pm' ),
            'due_date_label'       => __( 'Due Date :', 'fusion-pm' ),
            'attachment_label'     => __( 'Attachments :', 'fusion-pm' ),
            'added_by'             => __( 'Added by', 'fusion-pm' ),
            'on'                   => __( 'on', 'fusion-pm' ),
            'todos'                => __( 'To-Dos', 'fusion-pm' ),
            'cancel'               => __( 'Cancel', 'fusion-pm' ),
            'loading'              => __( 'Loading ...', 'fusion-pm' ),
            'edit'                 => __( 'Edit', 'fusion-pm' ),
            'delete'               => __( 'Delete', 'fusion-pm' ),
            'update'               => __( 'Update', 'fusion-pm' ),
            'add_todo'             => __( 'Add todo', 'fusion-pm' ),
            'add_todo_placeholder' => __( 'Enter your todo...', 'fusion-pm' ),
            'select_user'          => __( '--select user--', 'fusion-pm' ),
            'add_files'            => __( 'Add files', 'fusion-pm' ),
            'comment_label'        => __( 'Comments', 'fusion-pm' ),
            'comment_by'           => __( 'commented by', 'fusion-pm' ),
            'add_comment'          => __( 'Add Comment', 'fusion-pm' ),
            'overdue'              => __( 'overdue', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get messages_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function messages_localize_data() {
        $localize_data = array(
            'add_new_msg_btn'      => __( 'Add New Topic', 'fusion-pm' ),
            'message_heading'      => __( 'Discussion Board', 'fusion-pm' ),
            'no_message_yet'      => __( 'No Topic Added Yet', 'fusion-pm' ),
            'posted_by'      => __( 'posted by', 'fusion-pm' ),
            'loading'              => __( 'Loading ...', 'fusion-pm' ),
            'load_more_btn'         => __( 'Load More...', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get single_messages_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function single_messages_localize_data() {
        $localize_data = array(
            'add_new_msg_btn'      => __( 'Add New Topic', 'fusion-pm' ),
            'message_heading'      => __( 'Discussion Board', 'fusion-pm' ),
            'no_message_yet'      => __( 'No Topic Added Yet', 'fusion-pm' ),
            'posted_by'      => __( 'posted by', 'fusion-pm' ),
            'loading'              => __( 'Loading ...', 'fusion-pm' ),
            'load_more_btn'         => __( 'Load More...', 'fusion-pm' ),
            'edit'                 => __( 'Edit', 'fusion-pm' ),
            'delete'               => __( 'Delete', 'fusion-pm' ),
            'cancel'                    => __( 'Cancel', 'fusion-pm' ),
            'update'               => __( 'Update', 'fusion-pm' ),
            'add_files'            => __( 'Add files', 'fusion-pm' ),
            'comment_label'            => __( 'Comments', 'fusion-pm' ),
            'comment_by'            => __( 'commented by', 'fusion-pm' ),
            'add_comment'            => __( 'Add Comment', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get new_messages_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function new_messages_localize_data() {
        $localize_data = array(
            'message_label'             => __( 'Discussions', 'fusion-pm' ),
            'message_title_placeholder' => __( 'Enter topic title...', 'fusion-pm' ),
            'post_new_msg_btn'          => __( 'Post', 'fusion-pm' ),
            'edit'                      => __( 'Edit', 'fusion-pm' ),
            'delete'                    => __( 'Delete', 'fusion-pm' ),
            'cancel'                    => __( 'Cancel', 'fusion-pm' ),
            'update'                    => __( 'Update', 'fusion-pm' ),
            'add_files'                 => __( 'Add files', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * Get edit_messages_localize_data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function edit_messages_localize_data() {
        $localize_data = array(
            'message_label'             => __( 'Discussions', 'fusion-pm' ),
            'message_title_placeholder' => __( 'Enter topic title...', 'fusion-pm' ),
            'post_new_msg_btn'          => __( 'Post', 'fusion-pm' ),
            'edit'                      => __( 'Edit', 'fusion-pm' ),
            'delete'                    => __( 'Delete', 'fusion-pm' ),
            'cancel'                    => __( 'Cancel', 'fusion-pm' ),
            'update'                    => __( 'Update', 'fusion-pm' ),
            'add_files'                 => __( 'Add files', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    /**
     * [get_localize_data description]
     *
     * @return html
     */
    public function get_localize_data() {
        wp_send_json_success(
            array(
                'actions' => array(
                    'save' => __( 'Save', 'fusion-pm' ),
                    'add_new' => __( 'Add New', 'fusion-pm' ),
                    'cancel' => __( 'Cancel', 'fusion-pm' )
                ),
                'text_messages' => array(
                    'test' => __('Test', 'fusion-pm')
                )
            )
        );
    }

    public function folder_localize_data() {
        $localize_data = array(
            'add_new_file'   => __( 'Add File', 'fusion-pm' ),
            'edit'   => __( 'Edit', 'fusion-pm' ),
            'update'   => __( 'Update', 'fusion-pm' ),
            'delete'   => __( 'Delete', 'fusion-pm' ),
            'cancel'         => __( 'Cancel', 'fusion-pm' ),
            'load_more_btn'  => __( 'Load More...', 'fusion-pm' ),
            'add_files'      => __( 'Add files', 'fusion-pm' ),
            'make_folder'    => __( 'Make Folder', 'fusion-pm' ),
            'docs_and_files' => __( 'Docs & Files', 'fusion-pm' ),
            'create' => __( 'Create', 'fusion-pm' ),
            'no_folder_message' => __( 'No folder added yet', 'fusion-pm' ),
            'created_by' => __( 'Created by', 'fusion-pm' ),
            'archived_folders' => __( 'Archived Folders', 'fusion-pm' ),
            'folder_title' => __( 'folder title', 'fusion-pm' ),
            'add_files_to_folder' => __( 'Add files to folder', 'fusion-pm' ),
            'this_folder_is' => __( 'This folder is', 'fusion-pm' ),
            'archived' => __( 'Archived', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    public function comments_localize_data() {
        $localize_data = array(
            'add_new_file'         => __( 'Add File', 'fusion-pm' ),
            'update'               => __( 'Update', 'fusion-pm' ),
            'cancel'               => __( 'Cancel', 'fusion-pm' ),
            'add_files'            => __( 'Add files', 'fusion-pm' ),
            'comment_label'        => __( 'Comments', 'fusion-pm' ),
            'comment_by'           => __( 'commented by', 'fusion-pm' ),
            'add_comment'          => __( 'Add Comment', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    public function get_users_localize_data() {
        $localize_data = array(
            'header_label'              => __( 'Set up who’s on ', 'fusion-pm' ),
            'header_note'               => __( 
                                                'Everyone below will be able to sign in to project manager and collaborate with you on this project (make to-dos, post discussion topics, upload files, etc.).', 'fusion-pm' 
                                                ),
            'decorated_heading'         => __( 'People already on the project', 'fusion-pm' ),
            'add_btn_text'              => __( 'Add more people to this project', 'fusion-pm' ),
            'add_new'                   => __( 'Add new', 'fusion-pm' ),
            'edit'                      => __( 'Edit', 'fusion-pm' ),
            'remove'                    => __( 'Remove', 'fusion-pm' ),
            'cancel'                    => __( 'Cancel', 'fusion-pm' ),
            'update'                    => __( 'Update', 'fusion-pm' ),
            'title_placeholder'                    => __( 'Title', 'fusion-pm' ),
            'email_placeholder'                    => __( 'Email', 'fusion-pm' ),
            'name_placeholder'                    => __( 'Name', 'fusion-pm' ),
            'loading'                    => __( 'Loading . . .', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

    public function get_project_edit_localize_data() {
        $localize_data = array(
            'edit'                      => __( 'Edit', 'fusion-pm' ),
            'cancel'                    => __( 'Cancel', 'fusion-pm' ),
            'update'                    => __( 'Update', 'fusion-pm' )
        );

        wp_send_json_success( $localize_data );
    }

    public function single_file_localize_data() {
        $localize_data = array(
            'posted_by'       => __( 'posted by', 'fusion-pm' ),
            'loading'         => __( 'Loading ...', 'fusion-pm' ),
            'edit'            => __( 'Edit', 'fusion-pm' ),
            'delete'          => __( 'Delete', 'fusion-pm' ),
            'cancel'          => __( 'Cancel', 'fusion-pm' ),
            'update'          => __( 'Update', 'fusion-pm' ),
            'docs_and_files'  => __( 'Docs & Files', 'fusion-pm' ),
            'download'        => __( 'Download', 'fusion-pm' ),
        );

        wp_send_json_success( $localize_data );
    }

}

