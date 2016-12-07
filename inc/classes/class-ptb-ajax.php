<?php
namespace BP_Site_Customizer\inc\classes;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );

class PTB_AJAX {


	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'buildsite_ajax_enqueue' ) );		// THE AJAX ADD ACTIONS
		add_shortcode( 'print-ajax-frontend', array( __CLASS__, 'print_ajax_frontend' ) );
		add_action( 'wp_ajax_the_ajax_hook',  array( __CLASS__, 'build_site_function' ) );
		// need this to serve non logged in users
		add_action( 'wp_ajax_nopriv_the_ajax_hook',  array( __CLASS__, 'build_site_function' ) );

		/* =========================================================== */
		add_shortcode( 'build-ajax-frontend', array( __CLASS__, 'build_site_ajax_frontend' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'build_site_ajax_enqueue' ) );		// THE AJAX ADD ACTIONS
		add_action( 'wp_ajax_the_ajax_hook',  array( __CLASS__, 'the_action_function' ) );
		// need this to serve non logged in users
		add_action( 'wp_ajax_nopriv_the_ajax_hook',  array( __CLASS__, 'the_action_function' ) );
	}


	public static function buildsite_ajax_enqueue() {
		// enqueue and localise scripts
		wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-submit.js', array( 'jquery' ) );
		wp_localize_script( 'my-ajax-handle', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	public static function print_ajax_frontend() {
		// $site_user = wp_get_current_user();
		$the_form = '
		<form id="build-site-form">
		<input id="domain" name="domain" placeholder="domain" type="text" />
		<input id="title" name="title" placeholder="title" type="text" />
		<input name="action" type="hidden" value="the_ajax_hook" />&nbsp; <!-- this puts the action the_ajax_hook into the serialized form -->
		<input id="submit_button" value="Click This" type="button" onClick="submit_me();" />
		</form>
		<div id="response_area">
		This the response 
		</div>';
		return $the_form . ' from <h4>' . __FUNCTION__ . '</h4>';
	}

	public static function build_site_function() {
		/* this area is very simple but being serverside it affords the possibility of retreiving data from the server and passing it back to the javascript function */
		$array = array();
		$array['domain'] = $_POST['domain'];
		$array['path'] = '/';
		$array['title'] = $_POST['title'];
		$array['user_id'] = get_current_user_id();
		$main = get_site_url();
		$cloned_url = $array['domain'] . '.' . $main;

		// self::set_new_blog_build( $cloned_url );
		
		echo '<pre>';
		print_r( $array );
		echo '</pre>';

		echo 'New site for: ';
		echo '<br>at ' . $array['domain'];
		echo '<br>entitled ' . $array['title'];

		die();
	}

	public static function build_site_ajax_enqueue() {
		// enqueue and localise scripts
		wp_register_script( 'ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-handle.js', array( 'jquery' ) );
		wp_localize_script( 'ajax-handle', 'the_ajax_hook', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
		wp_enqueue_script( 'ajax-handle' );
	}


	public static function the_action_function() {
		/* this area is very simple but being serverside it affords the possibility of retreiving data from the server and passing it back to the javascript function */
		$array = array();
		$array['name'] = $_POST['name'];
		$array['domain'] = $_POST['domain'];
		// $array['path'] = '/';
		$array['title'] = $_POST['title'];
		// $array['user_id'] = get_current_user_id();
		// $array['user'] = wp_get_current_user();
		// $array['site_id'] = 6;

		echo '<pre>';
		print_r( $array );
		echo '</pre>';
		// wpmu_create_blog( $domain, $path, $title, $user_id, $meta, $site_id );
		// wpmu_create_blog( $array['domain'], $array['path'], $array['title'], $array['user_id'] );
		//do_action( 'wpmu_new_blog', $blog_id, $user_id, $domain, $path );
		// do_action( 'wpmu_new_blog', $array['site_id'], $array['user_id'], $array['domain'], $array['path'] );

		echo 'New site for: ' . $array['name'];
		echo '<br>at ' . $array['domain'];
		echo '<br>entitled ' . $array['title'];
		// echo '<br>at ' . $array['user']->user_email;

		die();

	}

		// ADD EG A FORM TO THE PAGE
	public static function build_site_ajax_frontend() {
		$the_form = '
		<form id="build">
		<input id="name" name="name" placeholder="your name" type="text" />
		<input id="domain" name="domain" placeholder="domain" type="text" />
		<input id="title" name="title" placeholder="title" type="text" />
		<input name="action" type="hidden" value="the_ajax_hook" />&nbsp; <!-- this puts the action the_ajax_hook into the serialized form -->
		<input id="submit_button" value="Click This" type="button" />
		</form>
		<div id="response-area">
		This the response from <h4>' . __FUNCTION__ . '</h4>
		</div>';
		return $the_form;
	}
}
