<?php
/*
Plugin Name: StartUp CPT Charts
Description: Le plugin pour activer le Custom Post Charts
Author: Yann Caplain
Version: 1.2.0
Text Domain: startup-cpt-charts
*/

//GitHub Plugin Updater
function startup_reloaded_charts_updater() {
	include_once 'lib/updater.php';
	//define( 'WP_GITHUB_FORCE_UPDATE', true );
	if ( is_admin() ) {
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'startup-cpt-charts',
			'api_url' => 'https://api.github.com/repos/yozzi/startup-cpt-charts',
			'raw_url' => 'https://raw.github.com/yozzi/startup-cpt-charts/master',
			'github_url' => 'https://github.com/yozzi/startup-cpt-charts',
			'zip_url' => 'https://github.com/yozzi/startup-cpt-charts/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'access_token' => '',
		);
		new WP_GitHub_Updater( $config );
	}
}

add_action( 'init', 'startup_reloaded_charts_updater' );

//CPT
function startup_reloaded_charts() {
	$labels = array(
		'name'                => _x( 'Charts', 'Post Type General Name', 'startup-cpt-charts' ),
		'singular_name'       => _x( 'Chart', 'Post Type Singular Name', 'startup-cpt-charts' ),
		'menu_name'           => __( 'Charts', 'startup-cpt-charts' ),
		'name_admin_bar'      => __( 'Charts', 'startup-cpt-charts' ),
		'parent_item_colon'   => __( 'Parent Item:', 'startup-cpt-charts' ),
		'all_items'           => __( 'All Items', 'startup-cpt-charts' ),
		'add_new_item'        => __( 'Add New Item', 'startup-cpt-charts' ),
		'add_new'             => __( 'Add New', 'startup-cpt-charts' ),
		'new_item'            => __( 'New Item', 'startup-cpt-charts' ),
		'edit_item'           => __( 'Edit Item', 'startup-cpt-charts' ),
		'update_item'         => __( 'Update Item', 'startup-cpt-charts' ),
		'view_item'           => __( 'View Item', 'startup-cpt-charts' ),
		'search_items'        => __( 'Search Item', 'startup-cpt-charts' ),
		'not_found'           => __( 'Not found', 'startup-cpt-charts' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'startup-cpt-charts' )
	);
	$args = array(
		'label'               => __( 'charts', 'startup-cpt-charts' ),
		'description'         => __( '', 'startup-cpt-charts' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'revisions' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-chart-pie',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
        'capability_type'     => array('chart','charts'),
        'map_meta_cap'        => true
	);
	register_post_type( 'charts', $args );

}

add_action( 'init', 'startup_reloaded_charts', 0 );

//Flusher les permalink à l'activation du plugin pour qu'ils fonctionnent sans mise à jour manuelle
function startup_reloaded_charts_rewrite_flush() {
    startup_reloaded_charts();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'startup_reloaded_charts_rewrite_flush' );

// Capabilities
function startup_reloaded_charts_caps() {
	$role_admin = get_role( 'administrator' );
	$role_admin->add_cap( 'edit_chart' );
	$role_admin->add_cap( 'read_chart' );
	$role_admin->add_cap( 'delete_chart' );
	$role_admin->add_cap( 'edit_others_charts' );
	$role_admin->add_cap( 'publish_charts' );
	$role_admin->add_cap( 'edit_charts' );
	$role_admin->add_cap( 'read_private_charts' );
	$role_admin->add_cap( 'delete_charts' );
	$role_admin->add_cap( 'delete_private_charts' );
	$role_admin->add_cap( 'delete_published_charts' );
	$role_admin->add_cap( 'delete_others_charts' );
	$role_admin->add_cap( 'edit_private_charts' );
	$role_admin->add_cap( 'edit_published_charts' );
}

register_activation_hook( __FILE__, 'startup_reloaded_charts_caps' );

// Metaboxes
function startup_reloaded_charts_meta() {
    require get_template_directory() . '/inc/font-awesome.php';
    
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_startup_reloaded_charts_';

	$cmb_box = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Service details', 'startup-cpt-charts' ),
		'object_types'  => array( 'charts' )
	) );
    
    $cmb_box->add_field( array(
            'name'             => __( 'Icon', 'startup-cpt-charts' ),
            'desc'             => __( 'The service icon', 'startup-cpt-charts' ),
            'id'               => $prefix . 'icon',
            'type'             => 'select',
            'show_option_none' => true,
            'options'          => $font_awesome
    ) );
}

add_action( 'cmb2_admin_init', 'startup_reloaded_charts_meta' );

// Shortcode
function startup_reloaded_charts_shortcode( $atts ) {

	// Attributes
    $atts = shortcode_atts(array(
            'bg' => '#f0f0f0'
        ), $atts);
    
	// Code
        ob_start();
        require get_template_directory() . '/template-parts/content-charts.php';
        return ob_get_clean();    
}
add_shortcode( 'charts', 'startup_reloaded_charts_shortcode' );
?>