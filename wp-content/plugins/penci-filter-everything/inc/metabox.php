<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$prefix_post_opts = '_penci_filter_set';

CSF::createMetabox( $prefix_post_opts, array(
	'title'        => __( 'Filter Settings', 'penci-filter-everything' ),
	'post_type'    => 'penci-filter',
	'show_restore' => true,
) );

CSF::createSection( $prefix_post_opts, array(
	'fields' => array(
		array(
			'id'       => 'post_type',
			'type'     => 'select',
			'chosen'   => true,
			'multiple' => true,
			'title'    => __( 'Post Type to filter', 'penci-filter-everything' ),
			'subtitle' => __( 'Select the Post Type you need to filter', 'penci-filter-everything' ),
			'options'  => 'post_types',
		),
		array(
			'id'           => 'filter_set',
			'type'         => 'repeater',
			'title'        => __( 'Filter Set', 'penci-filter-everything' ),
			'button_title' => __( 'Add New Filter', 'penci-filter-everything' ),
			'fields'       => array(

				array(
					'id'    => 'filter_title',
					'type'  => 'text',
					'title' => __( 'Filter Title', 'penci-filter-everything' ),
				),

				array(
					'id'      => 'filter_by',
					'type'    => 'select',
					'title'   => __( 'Filter by', 'penci-filter-everything' ),
					'options' => [
						'tax'          => __( 'Taxonomy', 'penci-filter-everything' ),
						'custom_field' => __( 'Custom Field', 'penci-filter-everything' ),
					]
				),

				array(
					'id'         => 'filter_tax',
					'type'       => 'select',
					'title'      => __( 'Select Taxonomy', 'penci-filter-everything' ),
					'options'    => 'penci_taxonomy',
					'dependency' => array( 'filter_by', '==', 'tax' ),
				),

				array(
					'id'         => 'filter_custom_field',
					'type'       => 'text',
					'title'      => __( 'Meta Key', 'penci-filter-everything' ),
					'dependency' => array( 'filter_by', '==', 'custom_field' ),
				),

				array(
					'id'      => 'filter_view',
					'type'    => 'select',
					'title'   => __( 'View in Widget', 'penci-filter-everything' ),
					'options' => [
						'checkbox'     => __( 'Checkboxes', 'penci-filter-everything' ),
						'radio'        => __( 'Radio Buttons', 'penci-filter-everything' ),
						'label'        => __( 'Label List', 'penci-filter-everything' ),
						'select'       => __( 'Select Boxed', 'penci-filter-everything' ),
						'multi-select' => __( 'Multi Select Boxed', 'penci-filter-everything' ),
					]
				),

			),
		),
	)
) );

// General Settings

$prefix_gnre_opts = '_penci_filter_options';

CSF::createMetabox( $prefix_gnre_opts, array(
	'title'        => __( 'General Settings', 'penci-filter-everything' ),
	'post_type'    => 'penci-filter',
	'show_restore' => true,
) );

CSF::createSection( $prefix_gnre_opts, array(
	'fields' => array(
		array(
			'id'       => 'filter_type',
			'type'     => 'select',
			'title'    => __( 'Filter Type', 'penci-filter-everything' ),
			'options'  => [
				'ajax' 		=> __( 'AJAX Filter', 'penci-filter-everything' ),
				'button'    => __( 'Button Filter', 'penci-filter-everything' ),
			]
		),
		array(
			'id'       => 'filter_conditions',
			'type'     => 'select',
			'title'    => __( 'Where to filter?', 'penci-filter-everything' ),
			'subtitle' => __( 'Specify page(s) where the Posts list should be filtered is located' ),
			'options'  => [
				'homepage' => __( 'Homepage/Front-page', 'penci-filter-everything' ),
				'pages'    => __( 'Pages', 'penci-filter-everything' ),
				'posts'    => __( 'Post Pages', 'penci-filter-everything' ),
				'archive'  => __( 'Archive Pages', 'penci-filter-everything' ),
			]
		),
		array(
			'id'         => 'filter_conditions_page_ids',
			'type'       => 'select',
			'chosen'     => true,
			'multiple'   => true,
			'title'      => __( 'Custom Pages', 'penci-filter-everything' ),
			'options'    => 'pages',
			'dependency' => array( 'filter_conditions', '==', 'pages' ),
		),
		array(
			'id'         => 'filter_conditions_post_ids',
			'type'       => 'select',
			'chosen'     => true,
			'multiple'   => true,
			'title'      => __( 'Custom Posts', 'penci-filter-everything' ),
			'options'    => 'posts',
			'dependency' => array( 'filter_conditions', '==', 'posts' ),
		),
		array(
			'id'      => 'tax_order',
			'type'    => 'select',
			'title'   => __( 'Taxonomy Order', 'penci-filter-everything' ),
			'options' => [
				'ASC'  => 'ASC',
				'DESC' => 'DESC',
			]
		),
		array(
			'id'      => 'tax_order_by',
			'type'    => 'select',
			'title'   => __( 'Taxonomy Order by', 'penci-filter-everything' ),
			'options' => [
				'term_id' => 'ID',
				'name'    => 'Name',
				'slug'    => 'Slug',
				'count'   => 'Count'
			]
		),
		array(
			'id'       => 'hide_empty',
			'type'     => 'switcher',
			'title'    => __( 'Empty Terms', 'penci-filter-everything' ),
			'subtitle' => __( 'To hide or not Filter terms that do not contain posts', 'penci-filter-everything' ),
		),
		array(
			'id'       => 'show_counter',
			'type'     => 'switcher',
			'title'    => __( 'Show counters', 'penci-filter-everything' ),
			'subtitle' => __( 'Displays the number of posts in a term', 'penci-filter-everything' ),
		),
		array(
			'id'    => 'number',
			'type'  => 'number',
			'title' => __( 'Max Items', 'penci-filter-everything' ),
		),
		array(
			'id'       => 'reset_button',
			'type'     => 'switcher',
			'title'    => __( 'Show Reset Button?', 'penci-filter-everything' ),
		),
	)
) );