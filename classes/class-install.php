<?php

/**
* Install classes
*/
class FusionPM_Install {

    function do_install() {
        add_role( 'fpm_member', 'Project Member', array( 'read' => true ) );
        $administrator     = get_role('administrator');
        $administrator->add_cap( 'manage_project' );
        $member     = get_role('fpm_member');
        $member->add_cap( 'manage_project' );
        $member->add_cap( 'upload_files' );
        $this->create_table();
    }

    /**
    * Create table
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function create_table() {
        global $wpdb;

        $schema = [
        
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_projects` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `project_title` varchar(300),
                `project_desc` text,
                `userID` int(11) unsigned,
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_lists` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `list_title` varchar(300),
                `userID` int(11) unsigned,
                `user_name` varchar(100),
                `projectID` int(11) unsigned,
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_todos` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `todo` text,
                `is_complete` tinyint(1) NOT NULL DEFAULT '0',
                `listID` int(11) unsigned,
                `userID` int(11) unsigned,
                `user_name` varchar(100),
                `assignee_name` varchar(100),
                `assigneeID` int(11) unsigned,
                `projectID` int(11) unsigned,
                `file_ids` longtext,
                `due_date` datetime,
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_messages` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `message_title` varchar(300),
                `message` text,
                `userID` int(11) unsigned,
                `projectID` int(11) unsigned,
                `user_name` varchar(100),
                `project_title` varchar(300),
                `file_ids` longtext,
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_folders` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `folder_title` varchar(250),
                `file_ids` longtext,
                `projectID` int(11) unsigned,
                `project_title` varchar(255),
                `userID` int(11) unsigned,
                `user_name` varchar(100),
                `is_archive` tinyint(1) NOT NULL DEFAULT '0',
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_comments` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `userID` int(11) unsigned,
                `user_name` varchar(100),
                `projectID` int(11) unsigned,
                `commentable_id` int(11) unsigned,
                `commentable_type` varchar(50),
                `comment` text,
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_activities` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `userID` int(11) unsigned,
                `user_name` varchar(100),
                `projectID` int(11) unsigned,
                `parentID` int(11) unsigned,
                `activity_id` int(11) unsigned,
                `activity_type` varchar(50),
                `activity` text,
                `created` datetime,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}fpm_project_user` (
                `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `userID` int(11) unsigned,
                `projectID` int(11) unsigned,
                PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

        ];

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        foreach ( $schema as $table ) {
            dbDelta( $table );
        }
    }
}