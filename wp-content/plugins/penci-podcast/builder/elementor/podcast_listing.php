<?php
use Elementor\Group_Control_Typography;
use PenciSoledadElementor\Base\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}



class PenciPodcastElementor extends Base_Widget {

	public function get_title() {
		return esc_html__( 'Penci Podcast - Listing', 'penci-podcast' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return [ 'penci-elements' ];
	}

	public function get_keywords() {
		return [ 'podcast', 'listing' ];
	}

	protected function get_html_wrapper_class() {
		return 'pencipdc-element elementor-widget-' . $this->get_name();
	}

	public function get_name() {
		return 'penci-podcast-listing';
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

		$this->add_control( 'podcast_cat', [
			'label'       => esc_html__( 'Categories', 'penci-podcast' ),
			'type'        => 'penci_el_autocomplete',
			'search'      => 'penci_get_taxonomies_by_query',
			'render'      => 'penci_get_taxonomies_title_by_id',
			'taxonomy'    => 'podcast-category',
			'multiple'    => true,
			'label_block' => true,
		] );

		$this->add_control( 'podcast_series', [
			'label'       => esc_html__( 'Series', 'penci-podcast' ),
			'type'        => 'penci_el_autocomplete',
			'search'      => 'penci_get_taxonomies_by_query',
			'render'      => 'penci_get_taxonomies_title_by_id',
			'taxonomy'    => 'podcast-series',
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
				'default' => '2',
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
					'2' => esc_html__( '2 Columns', 'penci-podcast' ),
					'3' => esc_html__( '3 Columns', 'penci-podcast' ),
					'4' => esc_html__( '4 Columns', 'penci-podcast' ),
				),
			)
		);

		$this->add_control(
			'mobile_columns', array(
				'label'   => __( 'Mobile Columns', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'1' => esc_html__( '1 Column', 'penci-podcast' ),
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
					'{{WRAPPER}} .pencipdc-item-wrapper'                => 'margin-left: -{{SIZE}}px;margin-right: -{{SIZE}}px;margin-bottom: -{{SIZE}}px;',
					'{{WRAPPER}} .pencipdc-item-wrapper .pencipdc-item' => 'padding-left: {{SIZE}}px;padding-right: {{SIZE}}px;margin-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'featured_image', array(
				'label'   => __( 'Image Position', 'penci-podcast' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'top',
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
			'featured_image_width', array(
				'label'     => __( 'Featured Image Width', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 2000, ) ),
				'selectors' => array( '{{WRAPPER}} .pencipdc-item .pencipdc-thumb' => 'flex: 0 0 {{SIZE}}px;' ),
				'condition' => [ 'featured_image' => 'left' ],
			)
		);

		$this->add_responsive_control(
			'featured_image_ratio', array(
				'label'     => __( 'Featured Image Ratio', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 2000, ) ),
				'selectors' => array( '{{WRAPPER}} .pencipdc-item .pencipdc-thumb .penci-image-holder:before' => 'padding-top: {{SIZE}}px;' ),
			)
		);

		$this->add_responsive_control(
			'featured_image_bradius', array(
				'label'     => __( 'Featured Boder Radius', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'selectors' => array( '{{WRAPPER}} .pencipdc-item .pencipdc-thumb .penci-image-holder' => 'border-radius: {{SIZE}}px;overflow:hidden;' ),
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
			'meta_media', array(
				'label'   => __( 'Show Media Options', 'penci-podcast' ),
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
				'selector' => '{{WRAPPER}} .pencipdc-item .pencipdc-title a',
			)
		);

		$this->add_control(
			'_heading_typo_link', array(
				'label'     => __( 'Title Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-item .pencipdc-title a' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_heading_typo_linkc', array(
				'label'     => __( 'Title Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-item .pencipdc-title a:hover' => 'color: {{VALUE}};' ),
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
				'selector' => '{{WRAPPER}} .pencipdc-meta span',
			)
		);

		$this->add_control(
			'_meta_typo_color', array(
				'label'     => __( 'Meta Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-meta span' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_meta_typo_link', array(
				'label'     => __( 'Meta Link Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-meta span a' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'_meta_typo_linkh', array(
				'label'     => __( 'Meta Link Hover Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-meta span a:hover' => 'color: {{VALUE}};' ),
			)
		);

		// desc
		$this->add_control(
			'_desc_typo_heading', array(
				'label' => __( 'Description', 'penci-podcast' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), array(
				'name'     => '_desc_typo',
				'label'    => __( 'Description Typography', 'penci-podcast' ),
				'selector' => '{{WRAPPER}} .pencipdc-meta-desc p',
			)
		);

		$this->add_control(
			'_desc_typo_color', array(
				'label'     => __( 'Description Color', 'penci-podcast' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .pencipdc-meta-desc p' => 'color: {{VALUE}};' ),
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

		$this->end_controls_section();

		$this->register_block_title_style_section_controls();
	}

	protected function render() {
		$settings    = $this->get_settings();
		$list_series = [];

		if ( ! empty( $settings['podcast_cat'] ) ) {
			foreach ( $settings['podcast_cat'] as $cat_id ) {

				$series_ids = pencipdc_get_podcast_by_category( $cat_id );

				if ( is_array( $series_ids ) ) {
					$list_series = array_merge( $list_series, $series_ids );
				}
			}
		}


		if ( ! empty( $settings['podcast_series'] ) ) {
			$list_series = array_unique( array_merge( $settings['podcast_series'], $list_series ) );
		}

		$css_cl_class = 'col-' . $settings['columns'] . ' mobile-col-' . $settings['mobile_columns'] . ' tablet-col-' . $settings['tablet_columns'];


		if ( ! empty( $list_series ) ) {
			$class = $settings['featured_image'] . '-thumb';
			$this->markup_block_title( $settings, $this );
			?>
            <div class="pencipdc-item-wrapper <?php echo $class . ' ' . $css_cl_class; ?>">
				<?php
				$list_series = array_slice( $list_series, 0, $settings['number'] );
				foreach ( $list_series as $list ) {
					$id    = $list;
					$term  = get_term( $id );
					$link  = get_term_link( $term );
					$title = $term->name;
					$total = sprintf( _n( '%s Episode', '%s Episodes', $term->count ), number_format_i18n( $term->count ) );
					?>
                    <div class="pencipdc-item">
						<?php if ( pencipdc_get_series_image_id( $id ) && get_theme_mod( 'pencipodcast_single_show_featured', true ) ): ?>
                            <div class="pencipdc-thumb">
                                <div class="pencipdc-thumbin">
                                    <a title="<?php echo wp_strip_all_tags( $title ); ?>"
                                       href="<?php echo esc_url( $link ); ?>"
                                       class="penci-image-holder penci-lazy"
                                       data-bgset="<?php echo wp_get_attachment_image_url( pencipdc_get_series_image_id( $id ), $settings['featured_image_size'] ); ?>"></a>
                                </div>
                            </div>
						<?php endif; ?>
                        <div class="pencipdc-content">
                            <div class="pencipdc-heading">
                                <h2 class="pencipdc-title">
                                    <a title="<?php echo wp_strip_all_tags( $title ); ?>"
                                       href="<?php echo esc_url( $link ); ?>"><?php echo wp_strip_all_tags( $title ); ?></a>
                                </h2>
                            </div>
                            <div class="pencipdc-meta grid-post-box-meta">
								<?php if ( $settings['meta_episode'] ): ?>
                                    <span class="pencipdc-meta-episode"><?php echo $total; ?></span>
								<?php endif; ?>
								<?php if ( $settings['meta_author'] ):
									$author_id = pencipdc_get_podcast_author( $id );
									?>
                                    <span class="pencipdc-meta-author"><a class="author-url url fn n"
                                                                          href="<?php echo get_author_posts_url( $author_id ); ?>"><?php echo get_the_author_meta( 'display_name', $author_id ); ?></a></span>
								<?php endif; ?>
                            </div>
							<?php if ( $term->description && $settings['meta_desc'] ): ?>
                                <div class="pencipdc-meta-desc">
                                    <p><?php echo penci_trim_excerpt( $term->description, get_theme_mod( 'pencipodcast_single_excerpt_length', 20 ) ); ?></p>
                                </div>
							<?php endif; ?>
							<?php if ( $settings['meta_media'] ) {
								echo pencipdc_podcast_add_media_menu( $id, 'podcast' );
							} ?>
                        </div>
                    </div>
					<?php
				}
				?>
            </div>
			<?php
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
