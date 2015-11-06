<?php
/*
Plugin Name: StartUp CPT Charts
Description: Le plugin pour activer le Custom Post Charts
Author: Yann Caplain
Version: 1.2.0
Text Domain: startup-cpt-charts
*/

//GitHub Plugin Updater
function startup_cpt_charts_updater() {
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

add_action( 'init', 'startup_cpt_charts_updater' );

//CPT
function startup_cpt_charts() {
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
		'supports'            => array( 'title', 'revisions' ),
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

add_action( 'init', 'startup_cpt_charts', 0 );

//Flusher les permalink à l'activation du plugin pour qu'ils fonctionnent sans mise à jour manuelle
function startup_cpt_charts_rewrite_flush() {
    startup_cpt_charts();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'startup_cpt_charts_rewrite_flush' );

// Capabilities
function startup_cpt_charts_caps() {
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

register_activation_hook( __FILE__, 'startup_cpt_charts_caps' );

// Metaboxes
function startup_cpt_charts_meta() {
    
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_startup_cpt_charts_';

	$cmb_box = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Chart details', 'startup-cpt-charts' ),
		'object_types'  => array( 'charts' )
	) );
    
    $cmb_box->add_field( array(
            'name'             => __( 'Type', 'startup-cpt-charts' ),
            'id'               => $prefix . 'type',
            'type'             => 'select',
            'default'          => '',
            'show_option_none' => false,
            'options'          =>  array(
                'pie'             => __( 'Pie', 'startup-cpt-charts' ),
                'donut'             => __( 'Donut', 'startup-cpt-charts' ),
                'bar'             => __( 'Bars', 'startup-cpt-charts' ),
                )
    ) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Bar graph height', 'startup-cpt-charts' ),
        'desc'             => __( 'In px', 'startup-cpt-charts' ),
        'id'               => $prefix . 'height',
        'default'          => '140',
        'type'             => 'text'
    ) );
    
    $data = $cmb_box->add_field( array(
		'id'          => $prefix . 'data',
		'type'        => 'group',
		'options'     => array(
			'group_title'   => __( 'Data {#}', 'startup-cpt-charts' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add Another Data', 'startup-cpt-charts' ),
			'remove_button' => __( 'Remove Data', 'startup-cpt-charts' ),
			'sortable'      => true // beta
			// 'closed'     => true, // true to have the groups closed by default
		)
	) );
    
    $cmb_box->add_group_field( $data, array(
        'name'             => __( 'Name', 'startup-cpt-charts' ),
        'id'               => 'name',
        'type'             => 'text'
    ) );
    
    $cmb_box->add_group_field( $data, array(
        'name'             => __( 'Value', 'startup-cpt-charts' ),
        'desc'             => __( 'In %. Forget the % symbol.', 'startup-cpt-charts' ),
        'id'               => 'value',
        'type'             => 'text'
    ) );
    
//    $cmb_box->add_group_field( $data, array(
//		'name'             => __( 'Highlight', 'startup-cpt-charts' ),
//		'id'               => 'highlight',
//		'type'             => 'checkbox'
//	) );
}

add_action( 'cmb2_admin_init', 'startup_cpt_charts_meta' );

// Shortcode
function startup_cpt_charts_shortcode( $atts ) {

	// Attributes
    $atts = shortcode_atts(array(
            'bg' => ''
        ), $atts);
    
	// Code
        ob_start();
        require get_template_directory() . '/template-parts/content-charts.php';
        return ob_get_clean();    
}
add_shortcode( 'charts', 'startup_cpt_charts_shortcode' );

// Enqueue scripts and styles.
function startup_cpt_charts_scripts() {
    wp_enqueue_style( 'startup-cpt-charts-pizza', plugins_url( '/css/pizza.min.css', __FILE__ ), array( ), false, 'all' );
    wp_enqueue_script( 'startup-cpt-charts-pizza', plugins_url( '/js/pizza.min.js', __FILE__ ), array( ), false, 'all' );
    wp_enqueue_script( 'startup-cpt-charts-pizza-dependencies', plugins_url( '/js/dependencies.js', __FILE__ ), array( ), false, 'all' );
    //wp_enqueue_script( 'startup-cpt-charts-modernizr', plugins_url( '/js/modernizr.js', __FILE__ ), array( ), false, 'all' );
}

add_action( 'wp_enqueue_scripts', 'startup_cpt_charts_scripts' );
?>