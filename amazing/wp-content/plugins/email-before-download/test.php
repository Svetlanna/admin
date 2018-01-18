<?php
// Force a short-init since we just need core WP, not the entire framework stack
define( 'SHORTINIT', true );

// Require the wp-load.php file (which loads wp-config.php and bootstraps WordPress)
require( '/var/www/u0253017/data/www/amazinghiring.ru/blog/wp-load.php' );

global $wpdb;
$table_item = "wp_ebd_item";
global $wpdb;
$download_id = 123123123123;
    $ebd_item = $wpdb->get_row( "SELECT * FROM $table_item  WHERE download_id = '99999';" );
//echo $ebd_item;
echo "PROPUSK";
echo "<br>";
     $ebd_item3 = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta WHERE  meta_key = 'wb_file_id' AND  meta_value = '123123123123';" );

$mylink = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta WHERE meta_value = 123123123123" );
echo $mylink->meta_id; // prints "10"
echo "<br>";
echo $ebd_item3->post_id;
echo "STOP";
$fff = $ebd_item3->post_id;
echo "<br>";
	  $ebd_item5 = $wpdb->get_row( "SELECT * FROM $wpdb->postmeta  WHERE post_id = $fff AND meta_key='youtube_link';" );
echo $ebd_item5->meta_value;
$youtube1 = $ebd_item5->meta_value;
echo "<br>";
	  $wpdb->update( $table_item, array("youtube_link"=>$youtube1), array( "download_id" => $download_id ));

?>