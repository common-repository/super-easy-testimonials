<?php
error_reporting('E_ALL');
/*
  Plugin Name: Super Easy Testimonials
  Plugin URI:
  Description:This plugin allows you add a custom testimonial section in the admin end for the user.
  Author: Debakant Mohanty
  Author URI: http://sharenconnect.com
  Version: 1.1.1
  License: GPL2 or later
 */
/*  Copyright 2014  Debakant Mohanty  (email : babuna.mohanty7@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
?>
<?php
$globalimagepath = wp_upload_dir();
global $globalimagepath;

//Create a new directory on activation
function set_create_folder() {
    $upload_dir = wp_upload_dir();
    $upload_loc = $upload_dir['basedir'] . "/authorimage";
    if (!is_dir($upload_loc)) {
        wp_mkdir_p($upload_loc);
    }
}
register_activation_hook(__FILE__, 'set_create_folder');
//Directory creation ends here
//Drop Table on plugin delete
function set_deleteTable()
{
	global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}super_easy_testimonials");
}
register_uninstall_hook(__FILE__, 'set_deleteTable');
//Drop Table on plugin delete
/* * ************************** Create a new admin menu ******************************** */
add_action('admin_menu', 'set_register_my_custom_menu_page');
function set_register_my_custom_menu_page() {
    add_menu_page('Super Easy Testimonials', 'Super Easy Testimonials', 'manage_options', 'super-easy-testimonials/testimonial.php', '', plugins_url('super-easy-testimonials/images/testimonials.png'), 6);
}

/* * ************************** Create a new admin menu ******************************** */
/* Adding Custom CSS */

function set_add_css() {

    wp_enqueue_style('set-admin-style', plugins_url('/css/set-admin-style.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_style('screen', plugins_url('/css/screen.css', __FILE__), false, '1.0.0', 'all');
}
//add_action('admin_enqueue_scripts', "set_add_css");

add_action('wp_enqueue_scripts', "set_add_css"); 

/* Creating the testimonial table */

function set_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'super_easy_testimonials';
    //table is not created. you may create the table here.
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`testimonial_title` VARCHAR( 500 ) NOT NULL ,
`testimonial_description` TEXT NOT NULL ,
`author_image` VARCHAR( 200 ) NOT NULL ,
`author_name` VARCHAR( 200 ) NOT NULL ,
`author_address` TEXT NOT NULL ,
`created` DATE NOT NULL ,
`is_active` TINYINT( 4 ) NOT NULL COMMENT '0=>inactive, 1=>active'
) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'set_create_table');
/* Table Creation Ends Here */

add_shortcode("list-testimonials", "set_displaytest_handler");

function set_displaytest_handler($limit = NULL) {
    $atts = shortcode_atts($limit, NULL);
    $limit = $atts['limit'];
    //run function that actually does the work of the plugin
    ob_start();
    $demolph_output = set_displayTestimonials($limit);
$demolph_output = ob_get_clean();
    //send back text to replace shortcode in post
    return $demolph_output;
ob_end_clean();
}

add_action('admin_print_scripts', 'add_editor' );
add_action('admin_print_styles', 'editor_css' );

function editor_css()
{
    wp_enqueue_style('thickbox');
}

function add_editor()
{
    wp_enqueue_script('editor');
    wp_enqueue_script('thickbox');
    add_action( 'admin_head', 'wp_tiny_mce' );
}

// Display the testimonials in the front end
function set_displayTestimonials($limit = NULL) {
    $globalimagepath = wp_upload_dir();
    global $wpdb;
    $table_name = $wpdb->prefix . 'super_easy_testimonials';
    $displayFrontEnd = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC LIMIT %d", $limit));
    foreach ($displayFrontEnd as $frontdetails) {
$content = stripslashes($frontdetails->testimonial_description);
if(!($frontdetails->author_image))
{
          echo $demolp_output = "
<div id='block'>
    <h3>$frontdetails->testimonial_title</h3>
    <div class='photo'>
      <img src='" . plugins_url() . '/super-easy-testimonials/images/' . 'photo-bg.png' . "' alt='' class='photo-bg'/>
      <img src='" . plugins_url() . '/super-easy-testimonials/images/' . 'photo.jpg' . "'>
    </div>
    <p class='content'><span class='laquo'>&nbsp;</span>$content<span class='raquo'>&nbsp;</span></p>
    <div class='sign'>
      <a href='#'>$frontdetails->author_name</a>
    </div>
  </div>
<div style='clear:both;'></div>
";
}
else
{
        echo $demolp_output = "
<div id='block'>
    <h3>$frontdetails->testimonial_title</h3>
    <div class='photo'>
      <img src='" . plugins_url() . '/super-easy-testimonials/images/' . 'photo-bg.png' . "' alt='' class='photo-bg'/>
      <img src='" . $globalimagepath['baseurl'] . '/authorimage/' . $frontdetails->author_image . "'>
    </div>
    <p class='content'><span class='laquo'>&nbsp;</span>$content<span class='raquo'>&nbsp;</span></p>
    <div class='sign'>
      <a href='#'>$frontdetails->author_name</a>
    </div>
  </div>
<div style='clear:both'></div>
";
}
    }
}
?>
