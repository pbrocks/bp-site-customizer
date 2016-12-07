<?php
namespace BP_Site_Customizer\inc\classes;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );

class Clone_Site {

	// class MultiSiteCloner {
	// https://wordpress.org/plugins/multisite-cloner/

	/*
	This page suggests that cloning should be easy
	https://buddydev.com/wordpress-multisite/cloning-blogs-on-wordpress-multisite-programmatically
	*/
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'print_site_ajax_enqueue' ) );		// THE AJAX ADD ACTIONS
		add_shortcode( 'print-ajax-frontend', array( __CLASS__, 'print_ajax_frontend' ) );
		add_shortcode( 'frontend-clone-site', array( __CLASS__, 'brint_ajax_frontend' ) );
		add_action( 'wp_ajax_brint_ajax_hook',  array( __CLASS__, 'set_new_blog_build' ) );
		add_action( 'wp_ajax_print_ajax_hook',  array( __CLASS__, 'print_ajax_function' ) );
		// add_action( 'admin_menu', array( __CLASS__, 'sample_menu' ) );
		add_shortcode( 'bp-template-content', array( __CLASS__, 'build_site_ajax_frontend' ) );
		add_shortcode( 'get-sites', array( __CLASS__, 'get_sites' ) );
		register_activation_hook( __FILE__, array( __CLASS__, 'install_multisite_cloner' ) );
		add_action( 'admin_init', array( __CLASS__, 'init_multisite_cloner' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'plugin_setup' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'build_site_ajax_enqueue' ) );		// THE AJAX ADD ACTIONS


	}


	public static function print_site_ajax_enqueue() {
		// enqueue and localise scripts
		wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-submit-form.js', array( 'jquery' ) );
		wp_localize_script( 'my-ajax-handle', 'print_ajax_hook', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-submit.js', array( 'jquery' ) );
		wp_localize_script( 'ajax-handle', 'brint_ajax_hook', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	public static function print_ajax_frontend() {
		// $site_user = wp_get_current_user();
		$the_form = '
		<form id="buildsite">
		<input id="domain" name="domain" placeholder="domain" type="text" />
		<input id="title" name="title" placeholder="title" type="text" />
		<input name="action" type="hidden" value="print_ajax_hook" />&nbsp; <!-- this puts the action print_ajax_hook into the serialized form -->
		<input id="submit_button" value="submit_me" type="button" onClick="submit_me();"  />
		</form>
		This the response from <h4>submit_me ' . __FUNCTION__ . '</h4>
		<div id="response_area">

		</div>';
		return $the_form . ' from <h4>' . __FUNCTION__ . '</h4>';
	}

	public static function brint_ajax_frontend() {
		// $site_user = wp_get_current_user();
		$the_form2 = '
		<form id="buildsite">
		<input id="domain" name="domain" placeholder="input a subdomain name (no spaces or special characters)" type="text" />
		<input id="title" name="title" placeholder="title" type="text" />
		<input name="action" type="hidden" value="brint_ajax_hook" />&nbsp; <!-- this puts the action print_ajax_hook into the serialized form -->
		<input id="submit_button" value="submit_me3" type="button" onClick="submit_me3();"  />
		</form>
		This the response from <h4>submit_me ' . __FUNCTION__ . '</h4>
		<div id="response_area">

		</div>';
		return $the_form2 . ' from <h4>' . __FUNCTION__ . '</h4>';
	}

	public static function brint_ajax_function() {
		$print = array();
		// $array['name'] = $_POST['name'];
		$print['domain'] = $_POST['domain'];
		$print['function'] = __FUNCTION__;

		// $print['path'] = '/';
		$print['title'] = $_POST['title'];
		$current_user = wp_get_current_user();
		$print['user_email'] = $current_user->user_email;
		$print['user_id'] = get_current_user_id();
		// $print['site_id'] = 6;

		echo '<pre>';
		print_r( $print );
		echo '</pre>';

		echo '<br>at ' . $print['domain'];
		echo '<br>entitled ' . $print['title'];
		echo '<br> ' . $print['user_email'];
		echo '<h4> ' . $print['function'] . '</h4> returns ';
		die();
	}

	public static function print_ajax_function() {
		$print = array();
		// $array['name'] = $_POST['name'];
		$print['domain'] = $_POST['domain'];
		// $print['path'] = '/';
		$print['title'] = $_POST['title'];
		$current_user = wp_get_current_user();
		$print['user_email'] = $current_user->user_email;
		$print['user_id'] = get_current_user_id();
		// $print['site_id'] = 6;

		echo '<pre>';
		print_r( $print );
		echo '</pre>';

		echo '<br>at ' . $print['domain'];
		echo '<br>entitled ' . $print['title'];
		echo '<br> ' . $print['user_email'];
		echo $current_user->user_email;
		die();
	}


	private static function get_main_blog_id() {
		if (function_exists( 'get_network' ) ) { // WP 4.6+
			return get_network()->site_id;
		} else {
			global $current_site;
			global $wpdb;
		$query = $wpdb->prepare ( "SELECT `blog_id` FROM `$wpdb->blogs` WHERE `domain` = '%s' AND `path` = '%s' ORDER BY `blog_id` ASC LIMIT 1", $current_site->domain, $current_site->path );
		return $wpdb->get_var ( $query );
		}
	}

	private static function get_first_blog_id() {
		global $wpdb;
		$query = $wpdb->prepare ( "SELECT `blog_id` FROM `$wpdb->blogs` WHERE `blog_id` != %d ORDER BY `blog_id` ASC LIMIT 0,1", self::get_main_blog_id() );
	    return $wpdb->get_var ( $query );
	}

	public static function get_instance() {
	    NULL === self::$instance and self::$instance = new self;
	    return self::$instance;
	}

	public static function install_multisite_cloner() {
		if ( ! is_multisite() )
			wp_die(
				'The whole point of this plugin is to clone blogs within a Multisite network. It can\'t be installed on a single site',
				'Error',
				array(
					'response' => 500,
					'back_link' => true,
				)
			);
	}

	public static function plugin_setup() {
		// Screenshot http://take.ms/geTIj.
	    add_action( 'network_admin_menu', array( __CLASS__, 'wp_mu_clone_page_link' ) );
	    add_action( 'wpmu_new_blog', array( __CLASS__, 'set_new_blog' ), 1, 1 );
	    add_action( 'admin_footer', array( __CLASS__, 'clone_input_admin' ) );
	    add_filter( 'manage_sites_action_links', array( __CLASS__, 'add_clone_link' ), null, 2 );
	    $plugin = plugin_basename( __FILE__ );
	    add_filter( "network_admin_plugin_action_links_$plugin", array( __CLASS__, 'cloner_settings_link' ), 4, 4 );
	}

	public static function init_multisite_cloner() {
	    add_option( 'wpmuclone_default_blog', self::get_first_blog_id() );
	    register_setting( 'default', 'wpmuclone_default_blog' );
	}

	public static function set_new_blog( $blog_id = false ) {
	    global $wpdb;
	    $main_blog_id = self::get_main_blog_id();

	    if ( isset( $_POST['wpmuclone_default_blog'] ) ) {
	        $id_default_blog = intval( $_POST['wpmuclone_default_blog'] );
	    } else {
	        $id_default_blog = get_option( 'wpmuclone_default_blog' );
	    }
	    $copy_users = get_option( 'wpmuclone_copy_users' );

	    if ( ! $id_default_blog) { return false; }

	    $old_url = get_site_url($id_default_blog);

	    switch_to_blog( $blog_id );

	    $new_url = get_site_url();
	    $new_name = get_bloginfo('title','raw');
	    $admin_email = get_bloginfo('admin_email','raw');

	    $prefix = $wpdb->base_prefix;
	    $prefix_escaped = str_replace('_','\_',$prefix);

	    // List all tables for the default blog,
	    $tables_q = $wpdb->get_results("SHOW TABLES LIKE '" . $prefix_escaped . $id_default_blog . "\_%'");

	    foreach($tables_q as $table){
	        $in_array = get_object_vars($table);
	        $old_table_name = current($in_array);
	        $tables[] = str_replace($prefix . $id_default_blog . '_', '', $old_table_name);
	        unset($in_array);
	    }

	    // Replace tables from the new blog with the ones from the default blog
	    foreach($tables as $table){
	        $new_table = $prefix . $blog_id . '_' . $table;
	        $old_table = $prefix . $id_default_blog . '_' . $table;

	        unset($queries);
	        $queries = array();

	        $queries[] = "DROP TABLE IF EXISTS " . $new_table ;
	        $queries[] = "CREATE TABLE " . $new_table . " LIKE " . $old_table;
	        $queries[] = "INSERT INTO " . $new_table . " SELECT * FROM " . $old_table;

	        foreach($queries as $query){
	            $wpdb->query($query);
	        }

	        $new_tables[] = $new_table;
	    }

	    $wp_uploads_dir = wp_upload_dir();
	    $base_dir = $wp_uploads_dir['basedir'];
	    $relative_base_dir = str_ireplace(get_home_path(), '', $base_dir);

	    // I need to get the previous folder before the id, just in case this is different to 'sites'
	    $dirs_relative_base_dirs = explode('/',$relative_base_dir);
	    $sites_dir = $dirs_relative_base_dirs[count($dirs_relative_base_dirs)-2];

	    $old_uploads = str_ireplace('/'.$sites_dir.'/'.$blog_id, '/'.$sites_dir.'/'.$id_default_blog, $relative_base_dir);
	    $new_uploads = $relative_base_dir;

	    // Replace URLs and paths in the DB

	    $old_url = str_ireplace(array('http://', 'https://'), '://', $old_url);
	    $new_url = str_ireplace(array('http://', 'https://'), '://', $new_url);

	    cloner_db_replacer( array($old_url,$old_uploads), array($new_url,$new_uploads), $new_tables);

	    // Update Title
	    update_option('blogname',$new_name);

	    // Update Email
	    update_option('admin_email',$admin_email);

	    // Copy Files
	    $old_uploads = str_ireplace('/'.$sites_dir.'/'.$blog_id, '/'.$sites_dir.'/'.$id_default_blog, $base_dir);
	    $new_uploads = $base_dir;;

	    cloner_recurse_copy($old_uploads, $new_uploads);

	    // User Roles
	    $user_roles_sql = "UPDATE $prefix" . $blog_id . "_options SET option_name = '$prefix" . $blog_id . "_user_roles' WHERE option_name = '$prefix" . $id_default_blog . "_user_roles';";
	    $wpdb->query($user_roles_sql);

	    // Copy users
	    if ( $copy_users ){
	        $users = get_users('blog_id='.$id_default_blog);

	        function user_array_map( $a ){ return $a[0]; }

	        foreach($users as $user){

	            $all_meta = array_map( 'user_array_map', get_user_meta( $user->ID ) );

	            foreach ($all_meta as $metakey => $metavalue) {
	                $prefix_len = strlen($prefix . $id_default_blog);

	                $metakey_prefix = substr($metakey, 0, $prefix_len);
	                if($metakey_prefix == $prefix . $id_default_blog) {
	                    $raw_meta_name = substr($metakey,$prefix_len);
	                    update_user_meta( $user->ID, $prefix . $blog_id . $raw_meta_name, maybe_unserialize($metavalue) );
	                }
	            }
	        }
		}

		// Restores main blog
		switch_to_blog( $main_blog_id );
	}

	public static function clone_input_admin() {
	    if ( 'site-new-network' != get_current_screen()->base ) {
	        return;
	    }

		$dropdown = '<input name="wpmuclone_default_blog" id="wpmuclone_default_blog" type="hidden" value="';

		if ( isset( $_GET['clone_from'] ) ) {
			$dropdown .= intval( $_GET['clone_from'] );
		} else {
			$dropdown .= get_option( 'wpmuclone_default_blog' ) ;
		}

		$dropdown .= '">';

echo <<<HTML
	<script type="text/javascript">
	jQuery(document).ready( function($) {
		$('$dropdown').appendTo('.form-table');
	});
	</script>
HTML;

	}

	public static function add_clone_link( $actions, $blog_id ) {
		$main_blog_id = self::get_main_blog_id();
		if ( $main_blog_id != $blog_id ) :
		    $actions['clone'] = '<a href="'. network_admin_url( 'site-new.php' ).'?clone_from=' . $blog_id . '">Clone</a>';
		endif;
		return $actions;
	}


	public static function build_site_ajax_frontend() {
		echo 'Main Blog = ' . self::name_ly_get_main_blog_id();
		// echo '<br>' . self::set_new_blog_build();
		echo self::print_ajax_frontend();
	}

	public static function name_ly_get_main_blog_id () {
		global $current_site;
		global $wpdb;

		$this_site = $wpdb->get_var ( $wpdb->prepare ( "SELECT `blog_id` FROM `$wpdb->blogs` WHERE `domain` = '%s' AND `path` = '%s' ORDER BY `blog_id` ASC LIMIT 1", $current_site->domain, $current_site->path ) );

		return $this_site;
	}

	public static function get_sites() {
		$subsites = get_sites();
		foreach ( $subsites as $subsite ) {
			if ( isset( $subsite ) ) {
				$subsite_id = get_object_vars( $subsite )['blog_id'];
				$subsite_name = get_blog_details( $subsite_id )->blogname;
				echo 'Site ID/Name: ' . esc_html( $subsite_id ) . ' / ' . esc_html( $subsite_name ) . '<br>';
			}
		}
	}

	public static function set_new_blog_build() {
	    global $wpdb;

		$print = array();
		// $array['name'] = $_POST['name'];
		$print['domain'] = $_POST['domain'];
		$print['function'] = __FUNCTION__ . ' should build us a new blog';

		// $print['path'] = '/';
		$print['title'] = $_POST['title'];
		$current_user = wp_get_current_user();
		$print['user_email'] = $current_user->user_email;
		$print['user_id'] = get_current_user_id();
		// $print['site_id'] = 6;

		echo '<pre>';
		print_r( $print );
		echo '</pre>';

		echo '<br>at ' . $print['domain'];
		echo '<br>entitled ' . $print['title'];
		echo '<br> ' . $print['user_email'];
		echo '<h4> ' . $print['function'] . '</h4>';
	

	    $main_blog_id = self::get_main_blog_id();
		// return '$main_blog_id = ' . $main_blog_id . ' from line ' . __LINE__;

		echo '<br><h4>Let\'s build a site from </h4>';

		if ( isset( $_POST['wpmuclone_default_blog'] ) ) {
		    $id_default_blog = intval( $_POST['wpmuclone_default_blog'] );
			echo '<br>Site ' . $id_default_blog;
		} else {
		    $id_default_blog = get_option( 'wpmuclone_default_blog' );
			echo '<br>Site ' . $id_default_blog;
		}

		$copy_users = get_option( 'wpmuclone_copy_users' );

		if ( ! $id_default_blog) { return false; }

		$old_url = get_site_url($id_default_blog);
		echo ', which is ' . $old_url;

	    switch_to_blog( $blog_id );

		$main_url = get_site_url(); // pbrocks $_POST['domain'];

		echo '<br>The Network URL = ' . $main_url; // = $cloned_url;

		$domain = $print['domain'];
		$network = get_blog_details(1);

		echo '<br>The name of your subdomain = http://' . $domain . '.' . $network->domain . '/';

	    $new_name = $print['title']; // pbrocks 
		echo '<br>The title of your site = ' . $new_name;

		$admin_user = wp_get_current_user();
		// echo '<br>$admin_user = ' . $admin_user . ' from line ' . __LINE__;
		$admin_email = $admin_user->user_email;
		echo '<br>$admin_email = ' . $admin_email;

		$prefix = $wpdb->base_prefix;
		// return '$prefix = ' . $prefix . ' from line ' . __LINE__;

	    $prefix_escaped = str_replace('_','\_',$prefix);

	    // List all tables for the default blog,
	    $tables_q = $wpdb->get_results("SHOW TABLES LIKE '" . $prefix_escaped . $id_default_blog . "\_%'");

		// echo '<pre>';
		// print_r( $tables_q );
		// echo '</pre>';

		die();
	    foreach($tables_q as $table){
	        $in_array = get_object_vars($table);
	        $old_table_name = current($in_array);
	        $tables[] = str_replace($prefix . $id_default_blog . '_', '', $old_table_name);
	        unset($in_array);
	    }

	    // Replace tables from the new blog with the ones from the default blog
	    foreach($tables as $table){
	        $new_table = $prefix . $blog_id . '_' . $table;
	        $old_table = $prefix . $id_default_blog . '_' . $table;

	        unset($queries);
	        $queries = array();

	        $queries[] = "DROP TABLE IF EXISTS " . $new_table ;
	        $queries[] = "CREATE TABLE " . $new_table . " LIKE " . $old_table;
	        $queries[] = "INSERT INTO " . $new_table . " SELECT * FROM " . $old_table;

	        foreach($queries as $query){
	            $wpdb->query($query);
	        }

	        $new_tables[] = $new_table;
	    }

	    $wp_uploads_dir = wp_upload_dir();
	    $base_dir = $wp_uploads_dir['basedir'];
	    $relative_base_dir = str_ireplace(get_home_path(), '', $base_dir);

	    // I need to get the previous folder before the id, just in case this is different to 'sites'
	    $dirs_relative_base_dirs = explode('/',$relative_base_dir);
	    $sites_dir = $dirs_relative_base_dirs[count($dirs_relative_base_dirs)-2];

	    $old_uploads = str_ireplace('/'.$sites_dir.'/'.$blog_id, '/'.$sites_dir.'/'.$id_default_blog, $relative_base_dir);
	    $new_uploads = $relative_base_dir;

	    // Replace URLs and paths in the DB

	    $old_url = str_ireplace(array('http://', 'https://'), '://', $old_url);
	    $new_url = str_ireplace(array('http://', 'https://'), '://', $new_url);

	    cloner_db_replacer( array($old_url,$old_uploads), array($new_url,$new_uploads), $new_tables);

	    // Update Title
	    update_option('blogname',$new_name);

	    // Update Email
	    update_option('admin_email',$admin_email);

	    // Copy Files
	    $old_uploads = str_ireplace('/'.$sites_dir.'/'.$blog_id, '/'.$sites_dir.'/'.$id_default_blog, $base_dir);
	    $new_uploads = $base_dir;;

	    cloner_recurse_copy($old_uploads, $new_uploads);

	    // User Roles
	    $user_roles_sql = "UPDATE $prefix" . $blog_id . "_options SET option_name = '$prefix" . $blog_id . "_user_roles' WHERE option_name = '$prefix" . $id_default_blog . "_user_roles';";
	    $wpdb->query($user_roles_sql);

	    // Copy users
	    if ( $copy_users ){
	        $users = get_users('blog_id='.$id_default_blog);

	        function user_array_map( $a ){ return $a[0]; }

	        foreach($users as $user){

	            $all_meta = array_map( 'user_array_map', get_user_meta( $user->ID ) );

	            foreach ($all_meta as $metakey => $metavalue) {
	                $prefix_len = strlen($prefix . $id_default_blog);

	                $metakey_prefix = substr($metakey, 0, $prefix_len);
	                if($metakey_prefix == $prefix . $id_default_blog) {
	                    $raw_meta_name = substr($metakey,$prefix_len);
	                    update_user_meta( $user->ID, $prefix . $blog_id . $raw_meta_name, maybe_unserialize($metavalue) );
	                }
	            }
	        }
		}

		// Restores main blog
		switch_to_blog( $main_blog_id );
	}

	public static function cloner_settings_link( $links, $plugin_file, $plugin_data, $context ) {
	  $settings_link = sprintf( '<a href="settings.php?page=wp_mu_clone_settings">%s</a>', __( 'Settings' ) );
	  array_unshift( $links, $settings_link );
	  return $links;
	}

	public static function wp_mu_clone_settings() {
	    if ( ! empty( $_POST[ 'action' ] ) ) {
	        update_option( 'wpmuclone_default_blog', $_POST['wpmuclone_default_blog'] );
	        update_option( 'wpmuclone_copy_users', isset( $_POST['wpmuclone_copy_users'] ) );
	    }

	    $main_blog_id = self::get_main_blog_id();

	    ?>
	    <div class="wrap">
	        <style>
	            .settings_page_wp_mu_clone_settings .wrap {
	                max-width: 600px;
	            }
	            label[for=wpmuclone_default_blog]{
	                white-space: nowrap;
	            }
	            #wpmuclone_default_blog {
	              max-width: 500px;
	            }
	            #wpbody {
	                background-size: 200px;
	            }
	            @media screen and (max-width: 600px) {
	                #wpbody {
	                    background-image: none;
	                }
	            }
	        </style>
	        <h1>Site to be Cloned Settings</h1>
	        <form method="post">
	            <?php settings_fields( 'default' ); ?>
	            <h2>Setting Agent Assets Site to be cloned</h2>
	            <p>All the data from this site will be copied into new sites.</p>
	            <p>This includes settings, posts and other content, theme options, and uploaded files.</p>
	            <p>Note: the main site in your network (id = <?php echo $main_blog_id ?>) can't be cloned, as it contains many DB tables, assets and sensitive information that shouldn't be replicated to other sites.</p>
	            <table class="form-table">
	                <tr valign="top">
	                    <th scope="row"><label for="wpmuclone_default_blog">Cloneable Site</label></th>
	                    <td>
	                    <select name="wpmuclone_default_blog" id="wpmuclone_default_blog">
	                        <option value="0">No default site</option>
	                    <?php
	                    $blog_list = get_sites(array('number' => null));
	                    $blog_list_counter = 0;
	                    foreach ( $blog_list as $blog ) {
	                        if( $blog->blog_id != $main_blog_id ){
	                            $blog_list_counter++;
	                            ?>
	                            <option value="<?php echo $blog->blog_id;?>" <?php if (get_option('wpmuclone_default_blog') == $blog->blog_id ){ ?> selected <?php } ?>><?php echo get_blog_details( $blog->blog_id, 'blogname' )->blogname ; ?> (<?php echo $blog->domain, $blog->path; ?>)</option>
	                            <?php
	                        }
	                    }
	                    ?>                   
	                    </select>
	                    <?php if ( ! $blog_list_counter ) { ?>
	                        <div class="error">
	                            <p>The plugin won&rsquo;t work until you have created a site in your network. (The main site should never be cloned.) </p>
	                        </div>
	                    <?php } ?>
	                    </td>
	                </tr>
	            </table>
	            <table class="form-table">
	                <h2>Global clone settings</h2>
	                <p>These settings apply to the default site selected above, and to sites copied using the &ldquo;Clone&rdquo; link on the &ldquo;All Sites&rdquo; network admin page.</p>
	                <tr valign="top">
	                    <th scope="row">Copy users</th>
	                    <td>
	                    <input type="checkbox" name="wpmuclone_copy_users" id="wpmuclone_copy_users" <?php if ( get_option( 'wpmuclone_copy_users' ) ) { ?> checked <?php } ?> >
	                    <label for="wpmuclone_copy_users">Copy all users too</label>
	                    <p class="description">Check this if you want to copy all users from the source site into the new cloned site.</p>
	                    </td>
	                </tr>
	            </table>
	            <?php submit_button(); ?>
	        </form>
	    </div>
	    <?php
	}

	public static function wp_mu_clone_page_link() {
	    add_submenu_page( 'myassets-options-page', 'AA Site Creation', 'AA Site Creation', 'manage_options', 'wp_mu_clone_settings', array( __CLASS__, 'wp_mu_clone_settings' ) );
	}

} // end class

/*
SEARCH AND REPLACE for WP DBs, taking into account serialized arrays commonly used by plugin options.
Adapted from the excellent "Search Replace DB" tool by Robert O'Rourke and David Coveney.
https://github.com/interconnectit/Search-Replace-DB/
*/
function cloner_recursive_unserialize_replace( $from = '', $to = '', $data = '', $serialised = false ) {

	// some unseriliased data cannot be re-serialised eg. SimpleXMLElements
	try {

	    if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {
	        $data = cloner_recursive_unserialize_replace( $from, $to, $unserialized, true );
	    }

	    elseif ( is_array( $data ) ) {
	        $_tmp = array( );
	        foreach ( $data as $key => $value ) {
	            $_tmp[ $key ] = cloner_recursive_unserialize_replace( $from, $to, $value, false );
	        }

	        $data = $_tmp;
	        unset( $_tmp );
	    } elseif ( is_object( $data ) ) {

			// Submitted by Tina Matter
	        $_tmp = $data;
	        $props = get_object_vars( $data );
	        foreach ( $props as $key => $value ) {
	            $_tmp->$key = cloner_recursive_unserialize_replace( $from, $to, $value, false );
	        }

	        $data = $_tmp;
	        unset( $_tmp );
	    } else {
	        if ( is_string( $data ) ) {
	            $data = str_replace( $from, $to, $data );
	        }
	    }

	    if ( $serialised ) {
	        return serialize( $data );
	    }

	} catch( Exception $error ) {

	}

	return $data;
	}


function cloner_db_replacer( $search = '', $replace = '', $tables = array( ) ) {

	global $wpdb;

	$guid = 1;
	$exclude_cols = array();

	if ( is_array( $tables ) && ! empty( $tables ) ) {
	    foreach( $tables as $table ) {

	        $columns = array( );

	        // Get a list of columns in this table
	        $fields = $wpdb->query( 'DESCRIBE ' . $table );
	        if ( ! $fields ) {
	            continue;
	        }

	        $columns_gr = $wpdb->get_results( 'DESCRIBE ' . $table );

	        foreach($columns_gr as $column){
	            $columns[ $column->Field ] = $column->Key == 'PRI' ? true : false;
	        }

	        // Count the number of rows we have in the table if large we'll split into blocks, This is a mod from Simon Wheatley
	        $row_count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $table );          
	        if ( $row_count == 0 )
	            continue;

	        $page_size = 50000;
	        $pages = ceil( $row_count / $page_size );

	        for( $page = 0; $page < $pages; $page++ ) {

	            $current_row = 0;
	            $start = $page * $page_size;
	            $end = $start + $page_size;
	            // Grab the content of the table
	            $data = $wpdb->query( sprintf( 'SELECT * FROM %s LIMIT %d, %d', $table, $start, $end ) );

	            $rows_gr = $wpdb->get_results( sprintf( 'SELECT * FROM %s LIMIT %d, %d', $table, $start, $end ) );

	            foreach($rows_gr as $row) {

	                $current_row++;

	                $update_sql = array( );
	                $where_sql = array( );
	                $upd = false;

	                foreach( $columns as $column => $primary_key ) {
	                    if ( $guid == 1 && in_array( $column, $exclude_cols ) )
	                        continue;

	                    $edited_data = $data_to_fix = $row->$column;

	                    // Run a search replace on the data that'll respect the serialisation.
	                    $edited_data = cloner_recursive_unserialize_replace( $search, $replace, $data_to_fix );

	                    // Something was changed
	                    if ( $edited_data != $data_to_fix) {
	                        $update_sql[] = $column . ' = "' . esc_sql( $edited_data ) . '"';
	                        $upd = true;
	                    }
	                    if ( $primary_key )
	                        $where_sql[] = $column . ' = "' . esc_sql( $data_to_fix ) . '"';
	                }

	                if ( $upd && ! empty( $where_sql )) {
	                    $sql = 'UPDATE ' . $table . ' SET ' . implode( ', ', $update_sql ) . ' WHERE ' . implode( ' AND ', array_filter( $where_sql ) );
	                    $result = $wpdb->query( $sql );   
	                }
	            }

	        }

	    }

	}

}

/* RECURSIVELY COPY a directory.
   By gimmicklessgpt at http://php.net/manual/en/function.copy.php#91010 
   Edited to work with empty dirs: https://wordpress.org/support/topic/pull-request-error-while-copying-a-dir-while-cloning
*/
function cloner_recurse_copy( $src, $dst ) {
	$dir = opendir($src); 

	// maybe I am not a dir after all.
	if(!$dir || !is_dir($src)) {
	    mkdir($src);
	}

	if ( ! file_exists( $dst ) ) {
	    mkdir( $dst );
	}

	while( false !== ( $file = readdir( $dir ) ) ) {
		if ( ( $file != '.' ) && ( $file != '..' ) ) {
		    if ( is_dir( $src . '/' . $file ) ) {
		        cloner_recurse_copy( $src . '/' . $file,$dst . '/' . $file );
		    }
		    else {
		        copy( $src . '/' . $file, $dst . '/' . $file );
		    }
		}
	} 

	closedir( $dir );
}

if ( ! function_exists( 'get_sites' ) ) {
	function get_sites( $opts = array() ) {
	    $opts['limit'] = $opts['number'] == null ? 0 : $opts['number'];
	    $sites = wp_get_sites( $opts );
	    return array_map( function( $x ) { return (object) $x; }, $sites );
	}
}

