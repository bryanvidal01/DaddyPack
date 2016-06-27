<?php

if(get_option('clrz_core_installed') != 1){
	if($wpdb->get_var("SHOW TABLES LIKE 'clrz_core'") != 'clrz_core') {
        $wpdb->query(
        	"
        	CREATE TABLE `clrz_core` (
        	  `meta_id` bigint(20) NOT NULL auto_increment,
        	  `meta_key` varchar(255) default NULL,
        	  `meta_value` longtext,
        	  PRIMARY KEY  (`meta_id`),
        	  KEY `meta_key` (`meta_key`)
        	);
        	"
        );
	}
	if($wpdb->get_var("SHOW TABLES LIKE 'clrz_friends'") != 'clrz_friends') {
	    $wpdb->query(
	    	"
			CREATE TABLE `clrz_friends` (
			  `id` int(11) NOT NULL auto_increment,
			  `initiator_user_id` int(11) NOT NULL,
			  `friend_user_id` int(11) NOT NULL,
			  `is_confirmed` int(11) NOT NULL,
			  `is_limited` int(11) NOT NULL,
			  `date_created` datetime NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `initiator_user_id` (`initiator_user_id`),
			  KEY `friend_user_id` (`friend_user_id`)
			);
	    	"
	    );
	}
	if($wpdb->get_var("SHOW TABLES LIKE 'clrz_messages'") != 'clrz_messages') {
		$wpdb->query(
			"
			CREATE TABLE `clrz_messages` (
			  `id` int(11) NOT NULL auto_increment,
			  `submit_id` int(11) NOT NULL,
			  `inbox_id` int(11) NOT NULL,
			  `date` datetime NOT NULL,
			  `title` varchar(500) NOT NULL,
			  `message` text NOT NULL,
			  `state` int(11) NOT NULL,
			  `id_parent` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ;
			"
		);
	}
	add_option('clrz_core_installed',1);
}

