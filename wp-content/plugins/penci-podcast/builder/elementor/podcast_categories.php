<?php

use Elementor\Group_Control_Typography;
use PenciSoledadElementor\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class PenciPodcastCategoriesElementor extends Base_Widget {

	public function get_title() {
		return esc_html__( 'Penci Podcast - Categories', 'penci-podcast' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'podcast', 'categories' ];
	}

	protected function get_html_wrapper_class() {
		return 'pencipdc-categories-element elementor-widget-' . $this->get_name();
	}

	public function get_name() {
		return 'penci-podcast-categories';
	}

	protected function register_controls() {

		$this->start_controls_section( 'content_section', [
			'label' => esc_html__( 'General', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'podcast_cat', [
			'label'       => esc_html__( 'Categories', 'penci-podcast' ),
			'type'        => 'penci_el_autocomplete',
			'search'      => 'penci_get_taxonomies_by_query',
			'render'      => 'penci_get_taxonomies_title_by_id',
			'taxonomy'    => 'podcast-category',
			'multiple'    => true,
			'label_block' => true,
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control(
			'columns', array(
				'label'   => __( 'Columns', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'2' => esc_html__( '2 Columns', 'penci-podcast' ),
					'3' => esc_html__( '3 Columns', 'penci-podcast' ),
					'4' => esc_html__( '4 Columns', 'penci-podcast' ),
					'5' => esc_html__( '5 Columns', 'penci-podcast' ),
				),
			)
		);

		$this->add_control(
			'tablet_columns', array(
				'label'   => __( 'Tablet Columns', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '2',
				'options' => array(
					'1' => esc_html__( '1 Columns', 'penci-podcast' ),
					'2' => esc_html__( '2 Columns', 'penci-podcast' ),
					'3' => esc_html__( '3 Columns', 'penci-podcast' ),
				),
			)
		);

		$this->add_control(
			'mobile_columns', array(
				'label'   => __( 'Mobile Columns', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'1' => esc_html__( '1 Columns', 'penci-podcast' ),
					'2' => esc_html__( '2 Columns', 'penci-podcast' ),
					'3' => esc_html__( '3 Columns', 'penci-podcast' ),
				),
			)
		);

		$this->add_responsive_control(
			'item_gap', array(
				'label'     => __( 'Items Gap', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .pencipdc-categories-wrapper'                           => 'margin-left: -{{SIZE}}px;margin-right: -{{SIZE}}px;margin-bottom: -{{SIZE}}px;',
					'{{WRAPPER}} .pencipdc-categories-wrapper .pencipdc-categories-item' => 'padding-left: {{SIZE}}px;padding-right: {{SIZE}}px;margin-bottom: {{SIZE}}px;',
				),
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
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item .pencipdc-thumb .penci-image-holder:before' => 'padding-top: {{SIZE}}px;' ),
			)
		);

		$this->add_responsive_control(
			'featured_image_bradius', array(
				'label'     => __( 'Items Boder Radius', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item .pencipdc-thumb' => 'border-radius: {{SIZE}}px;overflow:hidden;' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'meta_section', [
			'label' => esc_html__( 'Podcast Meta', 'penci-podcast' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

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

		$this->end_controls_section();

		$this->register_block_title_section_controls();

		$this->start_controls_section( 'style_section', [
			'label' => esc_html__( 'Content Style', 'penci-podcast' ),
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
				'selector' => '{{WRAPPER}} .pencipdc-categories-item .pencipdc-content h4',
			)
		);

		$this->add_control(
			'_heading_typo_link', array(
				'label'     => __( 'Title Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item .pencipdc-content h4' => 'color: {{VALUE}};' ),
			)
		);

		// meta
		$this->add_control(
			'_meta_typo_heading', array(
				'label'     => __( 'Meta', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => [ 'meta_episode' => 'yes' ],
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'      => '_meta_typo',
				'label'     => __( 'Meta Typography', 'penci-podcast' ),
				'selector'  => '{{WRAPPER}} .pencipdc-categories-item .pencipdc-cat-meta',
				'condition' => [ 'meta_episode' => 'yes' ],
			)
		);

		$this->add_control(
			'_meta_typo_color', array(
				'label'     => __( 'Meta Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item .pencipdc-cat-meta' => 'color: {{VALUE}};' ),
				'condition' => [ 'meta_episode' => 'yes' ],
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
				'selector'  => '{{WRAPPER}} .pencipdc-categories-item .pencipdc-content p',
				'condition' => [ 'meta_desc' => 'yes' ],
			)
		);

		$this->add_control(
			'_desc_typo_color', array(
				'label'     => __( 'Description Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item .pencipdc-content p' => 'color: {{VALUE}};' ),
				'condition' => [ 'meta_desc' => 'yes' ],
			)
		);

		// overlay
		$this->add_control(
			'_overlay_heading', array(
				'label' => __( 'Overlay', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'_o_color', array(
				'label'     => __( 'Overlay Background Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item .pencipdc-thumb:before' => 'background: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_o_hcolor', array(
				'label'     => __( 'Hover Overlay Background Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-categories-item:hover .pencipdc-thumb:before' => 'background: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();
	}

	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['podcast_cat'] ) ) {
			return false;
		}

		$css_cl_class = 'col-' . $settings['columns'] . ' mobile-col-' . $settings['mobile_columns'] . ' tablet-col-' . $settings['tablet_columns'];

		$this->markup_block_title( $settings, $this );
		echo '<div class="pencipdc-categories-wrapper ' . $css_cl_class . '">';
		foreach ( $settings['podcast_cat'] as $cat ) {
			$cat_data = get_term( $cat );
			$cat_url  = get_term_link( $cat_data );
			$title    = $cat_data->name;
			$total    = sprintf( _n( '%s Episode', '%s Episodes', $cat_data->count ), number_format_i18n( $cat_data->count ) );
			?>
            <div class="pencipdc-categories-item">
                <div class="pencipdc-thumb">
                    <div class="pencipdc-thumbin">
                        <a title="<?php echo wp_strip_all_tags( $title ); ?>"
                           href="<?php echo esc_url( $cat_url ); ?>"
                           class="penci-image-holder penci-lazy"
                           data-bgset="<?php echo wp_get_attachment_image_url( pencipdc_get_category_image_id( $cat ), $settings['featured_image_size'] ); ?>"></a>
                    </div>
                </div>
                <a class="pencipdc-content" href="<?php echo esc_url( $cat_url ); ?>">
                    <h4><?php echo wp_strip_all_tags( $title ); ?></h4>
					<?php if ( $settings['meta_episode'] ): ?>
                        <span class="pencipdc-cat-meta cat-count"><?php echo esc_html( $total ); ?></span>
					<?php endif; ?>

					<?php if ( $settings['meta_desc'] && ! empty( $cat_data->description ) ): ?>
                        <p class="cat-desc"><?php echo esc_html( $cat_data->description ); ?></p>
					<?php endif; ?>
                </a>
            </div>
			<?php
		}
		echo '</div>';
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
