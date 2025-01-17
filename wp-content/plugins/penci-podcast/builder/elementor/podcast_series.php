<?php

use Elementor\Group_Control_Typography;
use PenciSoledadElementor\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class PenciPodcastSeriesElementor extends Base_Widget {

	public function get_title() {
		return esc_html__( 'Penci Podcast - Series', 'penci-podcast' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'podcast', 'series' ];
	}

	protected function get_html_wrapper_class() {
		return 'pencipdc-series-element elementor-widget-' . $this->get_name();
	}

	public function get_name() {
		return 'penci-podcast-series';
	}

	protected function register_controls() {

		$this->start_controls_section( 'content_section', [
			'label' => esc_html__( 'General', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'number', array(
				'label'   => __( 'Number of Posts', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => '5',
			)
		);

		$this->add_control( 'podcast_series', [
			'label'       => esc_html__( 'Series', 'penci-podcast' ),
			'type'        => 'penci_el_autocomplete',
			'search'      => 'penci_get_taxonomies_by_query',
			'render'      => 'penci_get_taxonomies_title_by_id',
			'taxonomy'    => 'podcast-series',
			'multiple'    => false,
			'label_block' => true,
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'featured_image', array(
				'label'   => __( 'Image Position', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'top'  => esc_html__( 'Top', 'penci-podcast' ),
					'left' => esc_html__( 'Left', 'penci-podcast' ),
				)
			)
		);

		$this->add_control(
			'featured_image_size', array(
				'label'   => __( 'Featured Image Size', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_list_image_sizes( true ),
			)
		);

		$this->add_responsive_control(
			'featured_image_ratio', array(
				'label'     => __( 'Featured Image Ratio', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 2000, ) ),
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .pcpd-splaylist-thumb .penci-image-holder:before' => 'padding-top: {{SIZE}}px;' ),
			)
		);

		$this->add_responsive_control(
			'featured_image_width', array(
				'label'     => __( 'Featured Image Width', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 2000, ) ),
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .pcpd-splaylist-thumb' => 'flex: 0 0 {{SIZE}}px;' ),
				'condition' => [ 'featured_image' => 'left' ],
			)
		);

		$this->add_responsive_control(
			'featured_image_bradius', array(
				'label'     => __( 'Featured Boder Radius', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .pcpd-splaylist-thumb .penci-image-holder' => 'border-radius: {{SIZE}}px;overflow:hidden;' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'meta_section', [
			'label' => esc_html__( 'Podcast Meta', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'meta_author', array(
				'label'   => __( 'Show Author', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'meta_episode', array(
				'label'   => __( 'Show Episode Count', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'meta_desc', array(
				'label'   => __( 'Show Description', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'meta_sub', array(
				'label'   => __( 'Show Subscribe Button', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section( 'style_section', [
			'label' => esc_html__( 'Podcast Style', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control(
			'_heading_typo_heading', array(
				'label' => __( 'Title', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_heading_typo',
				'label'    => __( 'Title Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top h3',
			)
		);

		$this->add_control(
			'_heading_typo_link', array(
				'label'     => __( 'Title Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top h3,{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top h3 a' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_sheading_typo_heading', array(
				'label' => __( 'Listing Title', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_sheading_typo',
				'label'    => __( 'Listing Title Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .header-list-style .grid-title',
			)
		);

		$this->add_control(
			'_sheading_typo_link', array(
				'label'     => __( 'Listing Title Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .header-list-style .grid-title a,{{WRAPPER}} .header-list-style .grid-title' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_sheading_typo_hlink', array(
				'label'     => __( 'Listing Title Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .header-list-style .grid-title a:hover' => 'color: {{VALUE}};' ),
			)
		);

		// meta
		$this->add_control(
			'_meta_typo_heading', array(
				'label' => __( 'Meta', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_meta_typo',
				'label'    => __( 'Meta Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .penci-podcast-series-meta',
			)
		);

		$this->add_control(
			'_meta_typo_color', array(
				'label'     => __( 'Meta Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .penci-podcast-series-meta span' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_meta_typo_link', array(
				'label'     => __( 'Meta Link Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .penci-podcast-series-meta span a' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_meta_typo_linkh', array(
				'label'     => __( 'Meta Link Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .pencipdc-splaylist-top .penci-podcast-series-meta span a:hover' => 'color: {{VALUE}};' ),
			)
		);

		// desc
		$this->add_control(
			'_desc_typo_heading', array(
				'label'     => __( 'Description', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [ 'meta_desc' => 'yes' ],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'      => '_desc_typo',
				'label'     => __( 'Description Typography', 'penci-podcast' ),
				'selector'  => '{{WRAPPER}} .pencipdc-splaylist .penci-category-description',
				'condition' => [ 'meta_desc' => 'yes' ],
			)
		);

		$this->add_control(
			'_desc_typo_color', array(
				'label'     => __( 'Description Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .penci-category-description, {{WRAPPER}} .pencipdc-splaylist .penci-category-description p' => 'color: {{VALUE}};' ),
				'condition' => [ 'meta_desc' => 'yes' ],
			)
		);

		// listmeta
		$this->add_control(
			'_smeta_typo_heading', array(
				'label' => __( 'Item Meta', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_smeta_typo',
				'label'    => __( 'Meta Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .pencipdc-splaylist .grid-post-box-meta',
			)
		);

		$this->add_control(
			'_smeta_typo_color', array(
				'label'     => __( 'Meta Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .grid-post-box-meta span' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_smeta_typo_link', array(
				'label'     => __( 'Meta Link Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-splaylist .grid-post-box-meta span a' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_smeta_typo_linkh', array(
				'label'     => __( 'Meta Link Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .post-entry li.penci-slistp .grid-post-box-meta span a:hover' => 'color: {{VALUE}};' ),
			)
		);

		// subscribe buttons
		$this->add_control(
			'_sbutton_typo_heading', array(
				'label'     => __( 'Subscribe Button', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'      => '_sbutton_typo',
				'label'     => __( 'Buttons Typography', 'penci-podcast' ),
				'selector'  => '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a',
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		$this->add_control(
			'_sbutton_typo_color', array(
				'label'     => __( 'Button Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a' => 'color: {{VALUE}};' ),
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		$this->add_control(
			'_sbutton_typo_hcolor', array(
				'label'     => __( 'Button Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a:hover' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_sbutton_typo_bgcolor', array(
				'label'     => __( 'Button Background Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a' => 'background-color: {{VALUE}};' ),
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		$this->add_control(
			'_sbutton_typo_bghcolor', array(
				'label'     => __( 'Button Background Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a:hover' => 'background-color: {{VALUE}};' ),
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		$this->add_control(
			'_sbutton_typo_bdcolor', array(
				'label'     => __( 'Button Border Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a' => 'border-color: {{VALUE}};' ),
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		$this->add_control(
			'_sbutton_typo_bdhcolor', array(
				'label'     => __( 'Button Border Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci_podcast_post_option .follow-wrapper a:hover' => 'border-color: {{VALUE}};' ),
				'condition' => [ 'meta_sub' => 'yes' ],
			)
		);

		// play buttons
		$this->add_control(
			'_button_typo_heading', array(
				'label' => __( 'Play Button', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_button_typo',
				'label'    => __( 'Buttons Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play',
			)
		);

		$this->add_control(
			'_button_typo_color', array(
				'label'     => __( 'Button Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_button_typo_hcolor', array(
				'label'     => __( 'Button Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play:hover' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_button_typo_bgcolor', array(
				'label'     => __( 'Button Background Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_button_typo_bghcolor', array(
				'label'     => __( 'Button Background Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play:hover' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_button_typo_bdcolor', array(
				'label'     => __( 'Button Border Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_button_typo_bdhcolor', array(
				'label'     => __( 'Button Border Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.play:hover' => 'border-color: {{VALUE}};' ),
			)
		);

		// more buttons
		$this->add_control(
			'_more_button_typo_heading', array(
				'label' => __( 'More Button', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_more_button_typo',
				'label'    => __( 'Buttons Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more',
			)
		);

		$this->add_control(
			'_more_button_typo_color', array(
				'label'     => __( 'Button Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_more_button_typo_hcolor', array(
				'label'     => __( 'Button Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more:hover' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_more_button_typo_bgcolor', array(
				'label'     => __( 'Button Background Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_more_button_typo_bghcolor', array(
				'label'     => __( 'Button Background Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more:hover' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_more_button_typo_bdcolor', array(
				'label'     => __( 'Button Border Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_more_button_typo_bdhcolor', array(
				'label'     => __( 'Button Border Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc_media_option .pencipdc_media_button.more:hover' => 'border-color: {{VALUE}};' ),
			)
		);

		// line border
		$this->add_control(
			'_line_border', array(
				'label' => __( 'Borders', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'_line_border_cl', array(
				'label'     => __( 'General Border Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .penci-grid li.list-post,{{WRAPPER}} .pencipdc-splaylist .penci-category-description,{{WRAPPER}} .pencipdc-splaylist .pencipdc_postblock_episode_detail' => 'border-color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_line_spacing', array(
				'label'     => __( 'Listing Spacing', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .penci-grid li.list-post.penci-slistp'                  => 'padding-bottom: {{SIZE}}px!important;margin-bottom: {{SIZE}}px!important;',
					'{{WRAPPER}} .pencipdc-splaylist .pencipdc_postblock_episode_detail' => 'padding-top: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();
	}

	protected function render() {
		$settings = $this->get_settings();

		$img_pos = $settings['featured_image'];
		$size    = $settings['featured_image_size'];
		$author  = $settings['meta_author'];
		$episode = $settings['meta_episode'];
		$sub     = $settings['meta_sub'];
		$desc    = $settings['meta_desc'];
		$num     = $settings['number'];

		if ( ! empty( $settings['podcast_series'] ) ) {
			$this->markup_block_title( $settings, $this );
			$id = $settings['podcast_series'];
			echo do_shortcode( '[podcast num="' . $num . '" desc="' . $desc . '" size="' . $size . '" sub="' . $sub . '" author="' . $author . '" episode="' . $episode . '" img_pos="' . $img_pos . '" id="' . $id . '"]' );
		}
	}

	/**
	 * Get image sizes.
	 *
	 * Retrieve available image sizes after filtering `include` and `exclude` arguments.
	 */
	public function get_list_image_sizes( $default = false ) {
		$wp_image_sizes = $this->get_all_image_sizes();

		$image_sizes = array();

		if ( $default ) {
			$image_sizes[''] = esc_html__( 'Default', 'soledad' );
		}

		foreach ( $wp_image_sizes as $size_key => $size_attributes ) {
			$control_title = ucwords( str_replace( '_', ' ', $size_key ) );
			if ( is_array( $size_attributes ) ) {
				$control_title .= sprintf( ' - %d x %d', $size_attributes['width'], $size_attributes['height'] );
			}

			$image_sizes[ $size_key ] = $control_title;
		}

		$image_sizes['full'] = esc_html__( 'Full', 'soledad' );

		return $image_sizes;
	}

	public function get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = [ 'thumbnail', 'medium', 'medium_large', 'large' ];

		$image_sizes = [];

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ] = [
				'width'  => (int) get_option( $size . '_size_w' ),
				'height' => (int) get_option( $size . '_size_h' ),
				'crop'   => (bool) get_option( $size . '_crop' ),
			];
		}

		if ( $_wp_additional_image_sizes ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		return $image_sizes;
	}
}
