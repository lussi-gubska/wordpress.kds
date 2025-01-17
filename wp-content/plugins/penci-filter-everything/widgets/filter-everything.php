<?php

add_action( 'widgets_init', 'penci_filter_everything_load_widget' );

function penci_filter_everything_load_widget() {
	register_widget( 'penci_filter_everything_widget' );
}

if ( ! class_exists( 'penci_filter_everything_widget' ) ) {
	class penci_filter_everything_widget extends WP_Widget {

		/**
		 * Widget setup.
		 */

		private $classname = [];

		function __construct() {
			/* Widget settings. */
			$this->classname[] = 'penci_filter_everything_widget pcptf-mt';
			$widget_ops        = array(
				'classname'   => implode( ',', $this->classname ),
				'description' => esc_html__( 'A widget that displays a filter listing', 'penci-filter-everything' )
			);

			/* Widget control settings. */
			$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'penci_filter_everything_widget' );
			parent::__construct( 'penci_filter_everything_widget', penci_get_theme_name( '.Soledad', true ) . esc_html__( ' Filter Everything', 'penci-filter-everything' ), $widget_ops, $control_ops );

		}

		/**
		 * How to display the widget on the screen.
		 */
		function widget( $args, $instance ) {
			extract( $args );

			/* Our variables from the widget settings. */
			$title        = isset( $instance['title'] ) ? $instance['title'] : '';
			$title        = apply_filters( 'widget_title', $title );
			$filter       = isset( $instance['filter'] ) ? $instance['filter'] : '';
			
			if ( ! penci_fe_should_render( $filter ) ) {
				return;
			}

			$this->classname[] = 'widget_categories';
			/* Before widget (defined by themes). */
			echo ent2ncr( $before_widget );

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) {
				echo ent2ncr( $before_title ) . $title . ent2ncr( $after_title );
			}
			
			include PENCI_FTE_DIR . '/templates/filter.php';

			/* After widget (defined by themes). */
			echo ent2ncr( $after_widget );
		}

		/**
		 * Update the widget settings.
		 */
		function update( $new_instance, $old_instance ) {
			$instance      = $old_instance;
			$data_instance = $this->soledad_widget_defaults();
			foreach ( $data_instance as $data => $value ) {
				$instance[ $data ] = ! empty( $new_instance[ $data ] ) ? $new_instance[ $data ] : '';
			}

			return $instance;
		}

		public function soledad_widget_defaults() {
			return array(
				'title'        => esc_html__( 'Filter Categories', 'penci-filter-everything' ),
				'filter'       => '',
				'color'        => '',
				'hcolor'       => '',
				'bgcolor'      => '',
				'bghcolor'     => '',
				'bdcolor'      => '',
				'bdhcolor'     => '',
				'fsize'        => '',
				'fsizec'       => '',
			);
		}


		function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults = $this->soledad_widget_defaults();
			$instance = wp_parse_args( (array) $instance, $defaults );

			$instance_title = $instance['title'] ? str_replace( '"', '&quot;', $instance['title'] ) : '';
			$filter         = isset( $instance['filter'] ) ? $instance['filter'] : '';
			?>

            <!-- Widget Title: Text Input -->
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'penci-filter-everything' ); ?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo $instance_title; ?>"/>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>"><?php esc_html_e( 'Select Filter Set:', 'penci-filter-everything' ); ?></label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'filter' ) ); ?>"
                        class="widefat categories" style="width:100%;">
					<option value=""><?php _e( '- Select filter set -', 'penci-filter-everything' ); ?></option>
					<?php
					$filter_lists = get_posts( [
						'post_type'      => 'penci-filter',
						'posts_per_page' => -1,
					] );
					foreach ( $filter_lists as $filter_data ) {
                        $fname = $filter_data->ID;
                        $ftitle = $filter_data->post_title;
						echo '<option value="'.esc_attr( $fname ).'" '.selected( $fname, $filter ).'>'.sanitize_text_field( $ftitle ).'</option>';
					} ?>
                </select>
            </p>

			<p style="padding: 10px;" class="notice">
				<?php
				$link = admin_url( 'customize.php?autofocus[section]=penci_fte_general_section' );
				_e('You can customize the color and font size by following <a target="_blank" href="'.$link.'">this link</a>.','penci-filter-everything');?>
			</p>
			<?php
		}
	}
}
?>
