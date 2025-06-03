<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the commission admin area.
 *
 */
function slicewp_include_files_admin_commission() {

	// Get commission admin dir path.
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page.
	if ( file_exists( $dir_path . 'class-submenu-page-commissions.php' ) ) {
		include $dir_path . 'class-submenu-page-commissions.php';
	}

	// Include actions.
	if ( file_exists( $dir_path . 'functions-actions-commissions.php' ) ) {
		include $dir_path . 'functions-actions-commissions.php';
	}

	// Include commissions list table.
	if ( file_exists( $dir_path . 'class-list-table-commissions.php' ) ) {
		include $dir_path . 'class-list-table-commissions.php';
	}

}
add_action( 'slicewp_include_files', 'slicewp_include_files_admin_commission' );


/**
 * Register the Commission admin submenu page.
 *
 */
function slicewp_register_submenu_page_commissions( $submenu_pages ) {

	if ( ! is_array( $submenu_pages ) ) {
		return $submenu_pages;
	}

	$submenu_pages['commissions'] = array(
		'class_name' => 'SliceWP_Submenu_Page_Commissions',
		'data' 		 => array(
			'page_title' => __( 'Commissions', 'slicewp' ),
			'menu_title' => __( 'Commissions', 'slicewp' ),
			'capability' => apply_filters( 'slicewp_submenu_page_capability_commissions', 'manage_options' ),
			'menu_slug'  => 'slicewp-commissions'
		)
	);

	return $submenu_pages;

}
add_filter( 'slicewp_register_submenu_page', 'slicewp_register_submenu_page_commissions', 25 );


/**
 * Capture the commission status in a global before it gets updated. This global value is used mainly by email notifications.
 *
 * Ideally, we should have a better method of detecting an object's status change, but for alternative will work.
 *
 * @param int $commission_id
 *
 */
function slicewp_set_pre_update_previous_commission_status_global( $commission_id = 0 ) {

	// Verify received arguments not to be empty.
	if ( empty( $commission_id ) ) {
		return;
	}

	// Get the commission that will be updated.
	$commission = slicewp_get_commission( $commission_id );

	if ( empty( $commission ) ) {
		return;
	}

	// Save the previous commission status.
	slicewp()->globals()->set( 'pre_update_commission_status_' . $commission_id, $commission->get( 'status' ) );

}
add_action( 'slicewp_pre_update_commission', 'slicewp_set_pre_update_previous_commission_status_global' );