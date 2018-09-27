<?php
/**
 * Plugin Name: Administer
 * Plugin URI: http://restulestari.com
 * Description: This plugin is a plugin that listed action and hook to disable action and filters other than administrator 
 * Version: 1.0.0
 * Author: Ghazali Tajuddin
 * Author URI: http://www.ghazalitajuddin.com
 * License: GPL2
 * 
 * https://developer.wordpress.org/resource/dashicons/
 */

//define('ADMINISTER_PLUGIN_DIR',plugin_dir_path(_FILE_));
//require_once('include/administer');

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if class not exist
if ( ! class_exists( 'Administer' ) ) {

	class Administer {

	
		function __construct() {

			


			//Add Admin Menu
			add_action('admin_menu', array($this,'addAdministerMenu'));

			
			add_filter('show_admin_bar', array($this,'__return_false'),1000);
			//add_filter('show_admin_bar', array($this,'remove_admin_bar_front_end'),1000);
			//add_action( 'after_setup_theme', array($this,'remove_admin_bar_front_end'));

			//Remove Items In Admin Bar 
			//remove_action('admin_bar_menu', array($this,'remove_admin_menu_bar'),9999);
			add_action('wp_before_admin_bar_render', array($this,'remove_items_wp_admin_bar'), 1000);
			
			//admin footer version remove
			add_action( 'admin_menu', array($this,'dashboard_footer_remove'));
			
			//admin footer version update
			add_filter('update_footer', array($this,'dashboard_footer_update'),9999);
			//add_action( 'admin_menu', array($this,'adminFooterUpdate' ));
			
			add_filter('admin_footer_text', array($this,'dashboard_footer_text'));

			add_action( 'admin_menu', array($this,'remove_menus'),100);

			//Remove JetPack
			add_action( 'admin_init', array($this,'remove_jetpack'));

			

			//add_filter('show_admin_bar', array($this,'__return_false'), 999);

			add_action('wp_dashboard_setup', array($this,'disable_default_dashboard_widgets'));

			add_action('admin_head', array($this,'remove_help_tabs'));

			
			add_filter('pre_site_transient_update_core', array($this,'remove_core_updates')); //hide updates for WordPress itself
			add_filter('pre_site_transient_update_plugins', array($this,'remove_core_updates'),10,1); //hide updates for all plugins
			add_filter('pre_site_transient_update_themes', array($this,'remove_core_updates')); //hide updates for all themes

			

			//add_filter( 'site_transient_update_plugins', array($this,'remove_update_notifications'));

			add_action( 'admin_print_scripts', array($this,'disable_admin_notices' ));

			add_action('wp_dashboard_setup', array($this,'add_custom_dashboard_widget'));

			//add_action('wp_dashboard_setup', array($this,'another_custom_dashboard_widget'));

			//add_action('wp_dashboard_setup', array($this,'my_wp_dashboard_setup'));
		
			
		}

		////////////////////////////////////////////////////////////////
   		//Create Plugin Menu
	    ////////////////////////////////////////////////////////////////

		function addAdministerMenu(){
			
			//Add Menu Page
			//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
			add_menu_page("Wodpress Administer Plugin Page","Administer Plugin","manage_options","wordpress_administer_plugin_slug",array($this,"administerOptions"),"dashicons-admin-plugins");
	
		}

		

		////////////////////////////////////////////////////////////////
   		//Create Plugin Option
	    ////////////////////////////////////////////////////////////////
	    
	    function administerOptions() {
	    	//global $title;
   		 ?>
        <h2>MENU PAGE</h2>
        This is your option page. Do what you want here!
        <?php

	    }

	    ////////////////////////////////////////////////////////////////
   		//Change Footer Text
	    ////////////////////////////////////////////////////////////////

	    function dashboard_footer_text () {  

	    	echo "Administer &copy;";
  		
  			//echo 'Site developed by <a href="https://s12621.p20.sites.pressdns.com">Web Design Weekly</a>. Powered by <a href="http://www.wordpress.org">WordPress</a>';
		}

		////////////////////////////////////////////////////////////////
   		//Remove Footer
	    ////////////////////////////////////////////////////////////////

		function dashboard_footer_remove() {
		    if ( ! current_user_can('manage_options') ) { // 'update_core' may be more appropriate
		        remove_filter( 'update_footer', 'core_update_footer' ); 
		    }
		}
		
		////////////////////////////////////////////////////////////////
   		//Change version
	    ////////////////////////////////////////////////////////////////

		function dashboard_footer_update(){
			return 'Version 1.0.0';
			//echo "asd";
			//add_filter( 'update_footer', '__return_empty_string', 11 );
		}

		////////////////////////////////////////////////////////////////
   		//Remove admin bar front end
	    ////////////////////////////////////////////////////////////////

		function remove_admin_bar_front_end(){
			//return false;
			//show_admin_bar(false);			
			// if (is_blog_admin()) {
			//     return true;
			//  }
			//   return false;

			if( ! current_user_can('manage_options') )
				add_filter('show_admin_bar', array($this,'__return_false'));	


		}

		////////////////////////////////////////////////////////////////
   		//Remove side menu
	    ////////////////////////////////////////////////////////////////
	    
	    function remove_menus(){
			// get current login user's role
			$roles = wp_get_current_user()->roles;
			 
			// test role
			if( !in_array('contributor',$roles)){
			return;
			}
			 
			//remove menu from site backend.
			//remove_menu_page( 'index.php' ); //Dashboard
			//remove_menu_page( 'edit.php' ); //Posts
			remove_menu_page( 'upload.php' ); //Media
			remove_menu_page( 'edit-comments.php' ); //Comments
			remove_menu_page( 'themes.php' ); //Appearance
			remove_menu_page( 'plugins.php' ); //Plugins
			remove_menu_page( 'users.php' ); //Users
			remove_menu_page( 'tools.php' ); //Tools
			remove_menu_page( 'options-general.php' ); //Settings
			remove_menu_page( 'edit.php?post_type=page' ); //Pages
			remove_menu_page('edit.php?post_type=testimonial'); // Custom post type 1			
			remove_menu_page('edit.php?post_type=dedo_download');
			remove_menu_page('edit.php?post_type=tribe_events');
			remove_menu_page('wpcf7'); // Custom post type 2
			//remove_menu_page('admin.php?page=jetpack'); // Custom post type 2
			
		}

		////////////////////////////////////////////////////////////////
   		//Remove Jetpack
	    ////////////////////////////////////////////////////////////////

		function remove_jetpack(){
			if ( ! current_user_can('manage_options') ){
				remove_menu_page('jetpack');
			}
		}


		////////////////////////////////////////////////////////////////
   		//Remove dashboard widget
	    ////////////////////////////////////////////////////////////////

		// disable default dashboard widgets
		function disable_default_dashboard_widgets() {

			if ( !current_user_can('manage_options') ) {
		      remove_meta_box( 'dashboard_quick_press','dashboard', 'side' );      //Quick Press widget
		      remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );      //Recent Drafts
		      remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );      //WordPress.com Blog
		      remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );      //Other WordPress News
		      remove_meta_box( 'dashboard_incoming_links','dashboard', 'normal' );    //Incoming Links
		      remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );    //Plugins
		      remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
		      remove_meta_box('dashboard_activity', 'dashboard', 'normal');
		      remove_meta_box('tribe_dashboard_widget', 'dashboard', 'normal');
		  }

		}


		////////////////////////////////////////////////////////////////
   		//Remove help tabs
	    ////////////////////////////////////////////////////////////////
	 

		function remove_help_tabs() {
		    $screen = get_current_screen();
		    $screen->remove_help_tabs();
		}

		////////////////////////////////////////////////////////////////
   		//Remove core updates notifications
	    ////////////////////////////////////////////////////////////////

		function remove_core_updates(){
			global $wp_version;
		     return(object) array(
		          'last_checked'=> time(),
		          'version_checked'=> $wp_version,
		          'updates' => array()
		     );
		}
		
		////////////////////////////////////////////////////////////////
   		//Remove update notifications
	    ////////////////////////////////////////////////////////////////
	   
		function remove_update_notifications($value) {

		    if ( isset( $value ) && is_object( $value ) ) {
		        unset( $value->response[ plugin_basename(__FILE__) ] );
		    }

		    return $value;
		}

		function disable_admin_notices() {
		global $wp_filter;
			if ( is_user_admin() ) {
				if ( isset( $wp_filter['user_admin_notices'] ) ) {
								unset( $wp_filter['user_admin_notices'] );
				}
			} elseif ( isset( $wp_filter['admin_notices'] ) ) {
						unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
						unset( $wp_filter['all_admin_notices'] );
			}
		}
		
		////////////////////////////////////////////////////////////////
   		//Add custom dashboard widgets
	    ////////////////////////////////////////////////////////////////


		function add_custom_dashboard_widget() {
			if ( !current_user_can('manage_options') ) {
				wp_add_dashboard_widget('test_custom_dashboard_widget', 'Custom Widget Dashboard Ku...',array($this,'WidgetFunction'));
			}
		}

		function WidgetFunction(){
			//global $wp_meta_boxes;

			echo "Hello World, Selamat Pembersihan Dashboard!";
			//print_r($wp_meta_boxes);

		}
		

		////////////////////////////////////////////////////////////////
   		//Remove items admin bar
	    ////////////////////////////////////////////////////////////////
		
		function remove_items_wp_admin_bar(){
			global $wp_admin_bar;
			// replace 'updraft_admin_node' with your node id
			//$wp_admin_bar->remove_menu('tribe-events');
			
			//if ( !current_user_can('manage_options') ) {
			$wp_admin_bar->remove_node('tribe-events');
			$wp_admin_bar->remove_node('comments');
			$wp_admin_bar->remove_node('new-content');
			//$wp_admin_bar->remove_node('site-name');
			$wp_admin_bar->remove_node('wp-logo');
			//}
			
		}


	}//close class




}// close if class exist


if( is_admin() )
  $administerPluginPage = new Administer();


?>