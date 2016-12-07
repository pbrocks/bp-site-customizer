<?php
/*
* Plugin Name: BuddyPress Site Customizer
* Description: Creating new sites thru 
* Version: 0.8.1
* Author: PTB
*/

namespace BP_Site_Customizer;

defined( 'ABSPATH' ) or die( 'File cannot be accessed directly' );


// Autoloader will let us call classes directly rather than requiring the files first
require_once( 'autoload.php' );


// inc\classes\New_Site_BP_Nav::init();
inc\classes\Clone_Site::init();
inc\classes\PTB_AJAX::init();
