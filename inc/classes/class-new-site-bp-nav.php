<?php
namespace BP_Site_Customizer\inc\classes;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );

class New_Site_BP_Nav {


	public static function init() {
		// add_action( 'bp_setup_nav', array( __CLASS__, 'newsite_bp_nav_item1' ), 99 );

		// add_shortcode( 'new-site-bp-nav', array( __CLASS__, 'class_info' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'build_site_ajax_enqueue' ) );		// THE AJAX ADD ACTIONS
		// add_action( 'wp_ajax_the_ajax_hook',  array( __CLASS__, 'build_site_function' ) );
		// // need this to serve non logged in users
		// add_action( 'wp_ajax_nopriv_the_ajax_hook',  array( __CLASS__, 'build_site_function' ) );
		// add_action( 'the_content', array( __CLASS__, 'my_action_javascript' ) );
	}

	public static function class_info() {
		return '<h3>Class = ' . __CLASS__ . '<br>File = ' . basename( __FILE__ ) . ' &nbsp; Function = ' . __FUNCTION__ . '</h3><h3>' . __FILE__ . '</h3>';
	}
	/**
	 * adds the profile user nav link
	 */
	public static function newsite_bp_nav_item() {
		global $bp;

		$args = array(
			'name' => __( 'New Site', 'buddypress' ),
			'slug' => 'newsite',
			'default_subnav_slug' => 'newsite',
			'position' => 11,
			'screen_function' => array( __CLASS__, 'add_new_site_tab_screen' ),
			'item_css_id' => 'newsite',
		);

		bp_core_new_nav_item( $args );
	}
	/**
	 * the calback function from our nav item arguments
	 */
	public static function add_new_site_tab_screen() {
		add_action( 'bp_template_title', array( __CLASS__, 'new_site_tab_title' ) );
		add_action( 'bp_template_content', array( __CLASS__, 'hello_world_ajax_frontend' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', array( __CLASS__, 'members/single/plugins' ) ) );
	}

	public static function new_site_tab_title() {
		echo 'Build a New Site';
	}


	public static function hello_world_ajax_frontend() {
		$the_form = '
		<form id="theForm">
		<input id="name" name="name" placeholder="your name" type="text" />
		<input id="domain" name="domain" placeholder="domain" type="text" />
		<input id="title" name="title" placeholder="title" type="text" />
		<input name="action" type="hidden" value="the_ajax_hook" />&nbsp; <!-- this puts the action the_ajax_hook into the serialized form -->
		<input id="submit_button" value="Click This" type="button" onClick="submit_me();" />
		</form>
		<div id="response_area">
		This is where we\'ll get the response
		</div>';
		// return $the_form;
		echo $the_form;
	}


	public static function hello_world_ajax_enqueue() {
		// enqueue and localise scripts
		// wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-submit.js', array( 'jquery' ) );
		// wp_localize_script( 'my-ajax-handle', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		// Register the script
		wp_register_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-submit.js', array( 'jquery' ) );

		// Localize the script with new data
		$translation_array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		    'some_string' => __( 'Some string to translate', 'textdomain' ),
		    'a_value' => '10',
		);
		wp_localize_script( 'my-ajax-handle', 'the_ajax_script', $translation_array );

		// Enqueued script with localized data.
		wp_enqueue_script( 'my-ajax-handle' );
	}


	public static function the_action_function() {
		/* this area is very simple but being serverside it affords the possibility of retreiving data from the server and passing it back to the javascript function */
		$array = array();
		$array['name'] = $_POST['name'];
		$array['domain'] = $_POST['domain'];
		$array['path'] = '/';
		$array['title'] = $_POST['title'];
		$array['user_id'] = get_current_user_id();;
		$array['meta'] = 'Scarry';
		$array['site_id'] = 6;

		echo '<pre>';
		print_r( $array );
		echo '</pre>';

		echo 'New site for: ' . $array['name'];
		echo '<br>at ' . $array['domain'];
		echo '<br>entitled ' . $array['title'];

		die();

	}


	public static function new_site_tab_content3() {
		 // if ( bp_is_active( 'xprofile' ) ) :
		/**
		 * Fires before the display of member registration xprofile fields.
		 *
		 * @since 1.2.4

			<?php /***** Blog Creation Details ******/ ?>

			<div class="register-section" id="blog-details-section">
			<form class="password-check" method="post" id="theForm" action="console.log( '' );">

				<!--<h4><?php /* _e( 'Delete Checkbox', 'buddypress' ); ?></h4>

				<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'buddypress' ); */ ?></label></p>-->

				<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

					<label for="signup_blog_url"><?php _e( 'Blog URL', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
					<?php

					/**
					 * Fires and displays any member registration blog URL errors.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_signup_blog_url_errors' ); ?>

					<p class="input-ptb">
					<?php if ( is_subdomain_install() ) : ?>
						http:// <input type="text" placeholder="subdomain-name"name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_signup_subdomain_base(); ?>
					<?php else : ?>
						<?php echo home_url( '/' ); ?> <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
					<?php endif; ?></p>
					<p class="input-ptb">
					<label for="signup_blog_title"><?php _e( 'Site Title', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label></p>
					<?php

					/**
					 * Fires and displays any member registration blog title errors.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_signup_blog_title_errors' ); ?>
					<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

					<fieldset class="register-site">
						<legend class="label"><?php _e( 'Privacy: I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ); ?></legend>
						<?php

						/**
						 * Fires and displays any member registration blog privacy errors.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_signup_blog_privacy_errors' ); ?>

						<label for="signup_blog_privacy_public"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'buddypress' ); ?></label>
						<label for="signup_blog_privacy_private"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'buddypress' ); ?></label>
					</fieldset>

					<?php

					/**
					 * Fires and displays any extra member registration blog details fields.
					 *
					 * @since 1.9.0
					 */
					do_action( 'bp_blog_details_fields' ); ?>

				</div>

			</div><!-- #blog-details-section -->

			<?php

			/**
			 * Fires after the display of member registration blog details fields.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_after_blog_details_fields' );

		/**
		 * Fires before the display of the registration submit buttons.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_before_registration_submit_buttons' ); ?>

		<div class="submit">
			<input type="submit" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( 'Build a New Site', 'buddypress' ); ?>" onClick="submit_me();" />

				<input type="hidden" name="current_user" value="<?php echo $current_user->ID ?>">
				<input type="hidden" name="signup_blog_url" value="signup_blog_url">
				<div id="response_area">
		This is where we'll get the response
		</div>
		</div>
		</form>

		<?php

		/**
		 * Fires after the display of the registration submit buttons.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_after_registration_submit_buttons' ); ?>

		<?php wp_nonce_field( 'bp_new_signup' ); ?>

	<?php // endif; // request-details signup step ?>

	<?php /* if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

		<div id="template-notices" role="alert" aria-atomic="true">
			<?php

			/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php * /
			do_action( 'template_notices' ); ?>

		</div>
<?php */
	}


	/**
	 *  Get the AJAX url.
	 *
	 *  Fixes potential issue with Domain Mapping plugin.
	 */
	public static function admin_ajax_url() {
		$path   = 'admin-ajax.php';
		$scheme = ( is_ssl() || force_ssl_admin() ? 'https' : 'http' );

		if ( class_exists( 'domain_map' ) ) {
			global $dm_map;

			return $dm_map->domain_mapping_admin_url( admin_url( $path, $scheme ), '/', false );
		} else {
			return admin_url( $path, $scheme );
		}

	}


	public static function new_site_tab_content() {
		// Prep
		$output  = '';
		$user_id = bp_displayed_user_id();
		$user_name = bp_core_get_username( $user_id );
		// Check for exclusion
		// if ( $this->core->exclude_user( $user_id ) ) return;

		// // Check visibility settings
		// if ( ! $this->buddypress['visibility']['balance'] && ! bp_is_my_profile() && ! mycred_is_admin() ) return;

		// Loop though all post types
		$mycred_types = mycred_get_types();
		if ( ! empty( $mycred_types ) ) {

			// $template = $this->buddypress['balance_template'];
			foreach ( $mycred_types as $type => $label ) {

				// Load myCRED with this points type
				$mycred = mycred( $type );

				// Check if user is excluded from this type
				if ( $mycred->exclude_user( $user_id ) ) continue;

				// Get users balance
				$balance = $mycred->get_users_balance( $user_id, $type );

				// Output
				// $template = str_replace( '%label%', $label, $template );
				$output .= sprintf( '<div class="bp-widget mycred"><h4>%s</h4><table class="profile-fields"><tr class="field_1 field_current_balance_' . $type . '"><td class="label">%s</td><td class="data">%s</td><td class="user">%s</td></tr></table></div>', $mycred->plural(), __( 'Current balance', 'mycred' ), $mycred->format_creds( $balance ), $user_name );

			}
		}

		echo apply_filters( 'mycred_bp_profile_details', $output, $balance );

		echo do_shortcode( '[create-new-site]' );
	}

	/**
	 * adds the profile user nav link
	 */
	public static function newsite_bp_nav_item1() {
		global $bp;

		$args = array(
			'name' => __( 'Build Site', 'buddypress' ),
			'slug' => 'newsite',
			'default_subnav_slug' => 'newsite',
			'position' => 11,
			'screen_function' => array( __CLASS__, 'add_new_site_tab_screen1' ),
			'item_css_id' => 'newsite',
		);

		bp_core_new_nav_item( $args );
	}
	/**
	 * the calback function from our nav item arguments
	 */
	public static function add_new_site_tab_screen1() {
		add_action( 'bp_template_title', array( __CLASS__, 'build_site_tab_title' ) );
		add_action( 'bp_template_content', array( __CLASS__, 'build_site_ajax_frontend' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', array( __CLASS__, 'members/single/plugins' ) ) );
	}

	public static function build_site_tab_title() {
		echo 'Build New Site';
	}


	public static function build_site_ajax_frontend() {
		// $site_user = wp_get_current_user();
		$the_form = '
		<form id="build-site-form">
		<input id="name" name="name" placeholder="name" type="text" />
		<input id="domain" name="domain" placeholder="domain" type="text" />
		<input id="title" name="title" placeholder="title" type="text" />
		<input name="action" type="hidden" value="the_ajax_hook" />&nbsp; <!-- this puts the action the_ajax_hook into the serialized form -->
		<input id="submit_button" value="Click This" type="button" onClick="submit_me();" />
		</form>
		<div id="response_area">
		This is where we\'ll get the response
		</div>';
		// return $the_form;
		echo $the_form;

	}


	public static function build_site_ajax_enqueue() {
		// enqueue and localise scripts
		wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . '../js/ajax-submit.js', array( 'jquery' ) );
		wp_localize_script( 'my-ajax-handle', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}


	public static function build_site_function() {
		/* this area is very simple but being serverside it affords the possibility of retreiving data from the server and passing it back to the javascript function */
		$array = array();
		$array['name'] = $_POST['name'];
		$array['domain'] = $_POST['domain'];
		$array['path'] = '/';
		$array['title'] = $_POST['title'];
		$array['user_id'] = get_current_user_id();;
		$array['meta'] = 'Scarry';
		$clone_from_blog_id = 4;
		// self::bpdev_clone_blog( 4, 8 );
		//the table prefix for the blog we want to clone
		$old_table_prefix = $wpdb->get_blog_prefix( $clone_from_blog_id );
		// $old_table_prefix = wpdb::get_blog_prefix(4);
		// $array['site_id'] = $old_table_prefix;

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
		// echo '<br>at ' . $array['domain'];

		die();

	}

	public static function bpdev_clone_on_new_blog_registration( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

		$specified_blog_id = 2;//change it to the blog you want to clone
		bpdev_clone_blog( $specified_blog_id, $blog_id ); //clone the specified blog to the newly registered blog

	}

	/**
	*
	* @global type $wpdb
	* @param int $clone_from_blog_id the blog id which we are going to clone
	* @param int $clone_to_blog_id the blog id in which we are cloning
	*/
	public static function bpdev_clone_blog( $clone_from_blog_id, $clone_to_blog_id ) {

		global $wpdb;

		//the table prefix for the blog we want to clone
		$old_table_prefix = $wpdb->get_blog_prefix( $clone_from_blog_id );

		//the table prefix for the target blog in which we are cloning
		$new_table_prefix = $wpdb->get_blog_prefix( $clone_to_blog_id );

		//which tables we want to clone
		//add or remove your table here
		$tables = array( 'posts', 'comments', 'options', 'postmeta', 'terms', 'term_taxonomy', 'term_relationships', 'commentmeta' );

		//the options that we don't want to alter on the target blog
		//we will preserve the values for these in the options table of newly created blog
		$excluded_options = array(
			'siteurl',
			'blogname',
			'blogdescription',
			'home',
			'admin_email',
			'upload_path',
			'upload_url_path',
			$new_table_prefix.'user_roles', //preserve the roles
			//add your on keys to preserve here
		);

		//should we? I don't see any reason to do it, just to avoid any glitch
		$excluded_options = esc_sql( $excluded_options );

		//we are going to use II Clause to fetch everything in single query. For this to work, we will need to quote the string
		//
		//not the best way to do it, will improve in future
		//I could not find an elegant way to quote string using sql, so here it is
		$excluded_option_list = "('" . join( "','", $excluded_options ) . "')";

		//the options table name for the new blog in which we are going to clone in next few seconds
		$new_blog_options_table = $new_table_prefix.'options';

		$excluded_options_query = "SELECT option_name, option_value FROM {$new_blog_options_table} WHERE option_name IN {$excluded_option_list}";

		//let us fetch the data

		$excluded_options_data = $wpdb->get_results( $excluded_options_query );

		//we have got the data which we need to update again later

		//now for each table, let us clone
		foreach ( $tables as $table ) {
			//drop table
			//clone table
			$query_drop = "DROP TABLE {$new_table_prefix}{$table}";

			$query_copy = "CREATE TABLE {$new_table_prefix}{$table} AS (SELECT * FROM {$old_table_prefix}{$table})" ;
			//drop table
			$wpdb->query( $query_drop );
			//clone table
			$wpdb->query( $query_copy );
		}

		//update the preserved options to the options table of the clonned blog
		foreach ( (array) $excluded_options_data as $excluded_option ) {
			update_blog_option( $clone_to_blog_id, $excluded_option->option_name, $excluded_option->option_value );
		}
	}

}
