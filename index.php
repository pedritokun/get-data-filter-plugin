<?php
 /*
      Plugin Name: Get Data Filter Plugin
      Plugin URI: Plugin's url.
      Description:  This is a plugin that help you to get last posts information
      Version: 1.0.
      Author: Pedro Portocarrero
      Author URI: www.pedritokun.com
   */

global $db_version;
$db_version = '1.0';

function plugin_install(){
    wp_schedule_event(time(),'every_ten_minutes','minute_cron');
}

register_activation_hook( __FILE__,'plugin_install' );

function cron_every_ten_minutes( $schedules ) {

    $schedules['every_ten_minutes'] = array(
            'interval' => 10*60,
            'display' => __('Every 10 Minutes','textdomain')
        );
    return $schedules;
}

add_filter('cron_schedules','cron_every_ten_minutes');

function cron_update_data(){

    global $wpdb;
    global $wp_query; 
    global $db_version;
    include 'connection.php';

    $table_name = "visiona";
    $charset_collate = $wpdb->get_charset_collate();
    $sql        = "TRUNCATE TABLE $table_name";

    $dbh->query( $sql );

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            postid int(11) NOT NULL DEFAULT '0',
            title varchar(255) NOT NULL DEFAULT '',
            sector varchar(255) NOT NULL DEFAULT '',
            nicedate varchar(255) DEFAULT NULL,
            date datetime DEFAULT '0000-00-00 00:00:00',
            content longtext NOT NULL,
            image varchar(255) NOT NULL DEFAULT 'http://semanaeconomica.com/wp-content/uploads/2016/02/C%C3%A9sar-Acu%C3%B1a-Elecciones-2016-9.jpg',
            PRIMARY KEY  (id),
            KEY title (title)
        ) $charset_collate;";

    $dbh->query( $sql );

    include 'get_data.php';

    add_option( 'db_version', $db_version );
}

add_action('minute_cron','cron_update_data');

function plugin_uninstall(){

    $option_name = 'plugin_option_name';

    delete_option( $option_name );
    delete_site_option( $option_name );

    global $wpdb;
    include 'connection.php';

    $table_name = "visiona";
    $sql        = "DROP TABLE IF EXISTS $table_name";

    $dbh->query( $sql );

    wp_clear_scheduled_hook('minute_cron');
}

register_deactivation_hook( __FILE__,'plugin_uninstall' );

?>