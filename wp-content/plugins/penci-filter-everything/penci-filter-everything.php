<?php
/*
Plugin Name: Penci Filter Everything
Plugin URI: https://pencidesign.net/
Description: Filters everything in WordPress & WooCommerce: Products, any Post types, by Any Criteria. Compatible with WPML, ACF and others popular. Supports AJAX.
Version: 1.1
Author: PenciDesign
Author URI: https://pencidesign.net/
License: GPLv2 or later
Text Domain: penci-filter-everything
*/
define( 'PENCI_FTE_VERSION', '1.1' );
define( 'PENCI_FTE_URL', plugin_dir_url( __FILE__ ) );
define( 'PENCI_FTE_DIR', plugin_dir_path( __FILE__ ) );

require_once 'inc/helper.php';
require_once 'inc/front.php';
require_once 'inc/admin.php';
require_once 'inc/post_filters.php';
require_once 'widgets/filter-everything.php';

add_action( 'plugin_loaded', function () {
	Penci_FTE_Admin::getInstance();
	Penci_FTE_Front::getInstance();
} );