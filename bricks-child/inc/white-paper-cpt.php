<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register White Paper Custom Post Type
 */
function register_white_paper_cpt() {
    $labels = array(
        'name'                  => _x( 'White Papers', 'Post Type General Name', 'bricks' ),
        'singular_name'         => _x( 'White Paper', 'Post Type Singular Name', 'bricks' ),
        'menu_name'             => __( 'White Papers', 'bricks' ),
        'name_admin_bar'        => __( 'White Paper', 'bricks' ),
        'archives'              => __( 'White Paper Archives', 'bricks' ),
        'attributes'            => __( 'White Paper Attributes', 'bricks' ),
        'parent_item_colon'     => __( 'Parent White Paper:', 'bricks' ),
        'all_items'             => __( 'All White Papers', 'bricks' ),
        'add_new_item'          => __( 'Add New White Paper', 'bricks' ),
        'add_new'               => __( 'Add New', 'bricks' ),
        'new_item'              => __( 'New White Paper', 'bricks' ),
        'edit_item'             => __( 'Edit White Paper', 'bricks' ),
        'update_item'           => __( 'Update White Paper', 'bricks' ),
        'view_item'             => __( 'View White Paper', 'bricks' ),
        'view_items'            => __( 'View White Papers', 'bricks' ),
        'search_items'          => __( 'Search White Paper', 'bricks' ),
        'not_found'             => __( 'Not found', 'bricks' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'bricks' ),
        'featured_image'        => __( 'Featured Image', 'bricks' ),
        'set_featured_image'    => __( 'Set featured image', 'bricks' ),
        'remove_featured_image' => __( 'Remove featured image', 'bricks' ),
        'use_featured_image'    => __( 'Use as featured image', 'bricks' ),
        'insert_into_item'      => __( 'Insert into white paper', 'bricks' ),
        'uploaded_to_this_item' => __( 'Uploaded to this white paper', 'bricks' ),
        'items_list'            => __( 'White Papers list', 'bricks' ),
        'items_list_navigation' => __( 'White Papers list navigation', 'bricks' ),
        'filter_items_list'     => __( 'Filter white papers list', 'bricks' ),
    );
    $args = array(
        'label'                 => __( 'White Paper', 'bricks' ),
        'description'           => __( 'White Papers and downloads', 'bricks' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-media-document',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type( 'white-paper', $args );
}
add_action( 'init', 'register_white_paper_cpt', 0 );

/**
 * Register ACF Field Group for White Paper PDF
 */
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_white_paper_pdf',
	'title' => 'White Paper Details',
	'fields' => array(
		array(
			'key' => 'field_white_paper_pdf_file',
			'label' => 'PDF File',
			'name' => 'white_paper_pdf',
			'type' => 'file',
			'instructions' => 'Upload the PDF file for this white paper.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'library' => 'all',
			'min_size' => '',
			'max_size' => '',
			'mime_types' => 'pdf',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'white-paper',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;
