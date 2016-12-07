<?php
namespace BP_Site_Customizer\inc\classes;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );

class Dev_Dashboard {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'dev_menu' ) );
	}

	public static function dev_menu() {
		add_dashboard_page( 'Dev Dashboard', 'Dev Dashboard', 'manage_options', 'dev-dashboard.php',  array( __CLASS__, 'dev_menu_page' ) );
		// add_dashboard_page( 'myCred Dashboard', 'myCred Dashboard', 'manage_options', 'dev-dashboard1.php',  array( __CLASS__, 'dev_menu_page1' ) );
	}

	public static function dev_menu_page() {
		global $wpdb, $post, $menu, $submenu;

		$table_name = $wpdb->base_prefix . 'order_map';
		$package_settings = get_option( 'mism_package_settings' );
		$current_site = get_current_blog_id();
		$sites = get_sites();


		print '<pre>' . count( $sites ) . " sites\n</pre>";
		$network = get_blog_details(1);

		echo ' <pre>' . $network->domain . ' ';
		print_r( $network );
		echo '</pre>';

		echo '<h3>You are viewing this menu from a ';
		echo Setup_Functions::detect_mobile_device();
		echo ' device</h3>';

		echo '<pre>';
		echo 'You can find this file in  <br>';
		echo plugins_url( '/', __FILE__ );
		echo '<br><br>Table name is ' . $table_name . '<br><br>';

		foreach( $sites as $site ) {
			$site_id = get_object_vars( $site )['blog_id'];
			$site_name = get_blog_details( $site_id )->blogname;
			echo 'Site #' . $site_id . ' is named ' . $site_name . "\n";
		}

		$site_vars = get_object_vars( $sites[0] );
		$site_details = get_blog_details( get_current_blog_id() );

		echo '<h3>$site_details</h3>';
		print_r( $site_details );
		echo '<br>';
		echo '<h3>$site_vars</h3>';
		print_r( $site_vars );
		// print_r( $args );
		echo '</pre>';

		// echo '<pre><h2>This is the Menu</h2>';https://paulund.co.uk/build-responsive-tester-page
		// print_r( $menu );
		// echo '</pre>';

		echo '<pre><h2>This is the Submenu</h2>';
		print_r( $submenu );
		echo '</pre>';
	}

	public static function my_cred_menu() {
		$user_id = get_current_user();
		$balance = mycred_get_users_cred( $user_id );
		return $balance;
	}

	public static function my_cred_menu1() {
		$object = new myCRED_Account();
		$object->mycred();
		return $object;
	}

	public static function dev_menu_page1() {
		echo '<pre>';
		print_r( self::my_cred_menu1() );
		echo '</pre>';
	}
}
