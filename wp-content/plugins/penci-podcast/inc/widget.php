<?php
/**
 * Podcast Playlist Widget
 *
 * @package Soledad
 * @since   1.0
 */

add_action( 'widgets_init', 'penci_podcast_load_widget' );

function penci_podcast_load_widget() {
	register_widget( 'penci_podcast_widget' );
}

if ( ! class_exists( 'penci_podcast_widget' ) ) {
	class penci_podcast_widget extends WP_Widget {

		/**
		 * Widget setup.
		 */
		function __construct() {
			/* Widget settings. */
			$widget_ops = array(
				'classname'   => 'penci_podcast_widget',
				'description' => esc_html__( 'A widget that displays an podcast series content', 'soledad' )
			);

			/* Widget control settings. */
			$control_ops = array( 'id_base' => 'penci_podcast_widget' );

			$prefix = '.Soledad';
			if ( function_exists( 'penci_get_theme_name' ) ) {
				$prefix = penci_get_theme_name( '.Soledad', true );
			}

			/* Create the widget. */
			global $wp_version;
			if ( 4.3 > $wp_version ) {
				$this->WP_Widget( 'penci_podcast_widget', $prefix . ' '.esc_html__( 'Podcast', 'soledad' ), $widget_ops, $control_ops );
			} else {
				parent::__construct( 'penci_podcast_widget', $prefix . ' '.esc_html__( 'Podcast', 'soledad' ), $widget_ops, $control_ops );
			}
		}

		/**
		 * How to display the widget on the screen.
		 */
		function widget( $args, $instance ) {
			extract( $args );

			/* Our variables from the widget settings. */
			$title          = isset( $instance['title'] ) ? $instance['title'] : '';
			$title          = apply_filters( 'widget_title', $title );
			$podcast_series = isset( $instance['podcast_series'] ) ? $instance['podcast_series'] : '';
			$desc           = isset( $instance['desc'] ) ? $instance['desc'] : '';
			$size           = isset( $instance['size'] ) ? $instance['size'] : '';
			$sub            = isset( $instance['sub'] ) ? $instance['sub'] : '';
			$author         = isset( $instance['author'] ) ? $instance['author'] : '';
			$episode        = isset( $instance['episode'] ) ? $instance['episode'] : '';
			$img_pos        = isset( $instance['img_pos'] ) ? $instance['img_pos'] : '';

			/* Before widget (defined by themes). */
			echo ent2ncr( $before_widget );

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) {
				echo( $before_title . $title . $after_title );
			}
			echo do_shortcode( '[podcast desc="' . $desc . '" size="' . $size . '" sub="' . $sub . '" author="' . $author . '" episode="' . $episode . '" img_pos="' . $img_pos . '" id="' . $podcast_series . '"]' );
			/* After widget (defined by themes). */
			echo ent2ncr( $after_widget );
		}

		/**
		 * Update the widget settings.
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$data_instance = $this->soledad_widget_defaults();

			foreach ( $data_instance as $data => $value ) {
				$instance[ $data ] = ! empty( $new_instance[ $data ] ) ? $new_instance[ $data ] : '';
			}

			return $instance;
		}

		public function soledad_widget_defaults() {
			$defaults = array(
				'title'          => 'Podcast Series',
				'podcast_series' => '',
				'size'           => '',
				'author'         => '',
				'sub'            => '',
				'episode'        => '',
				'desc'           => '',
				'img_pos'        => '',
			);

			return $defaults;
		}

		function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults = $this->soledad_widget_defaults();
			$instance = wp_parse_args( (array) $instance, $defaults );

			$instance_title = $instance['title'] ? str_replace( '"', '&quot;', $instance['title'] ) : '';

			$series_list = get_terms( [
				'taxonomy'   => 'podcast-series',
				'hide_empty' => false,
			] );
			?>
            <!-- Widget Title: Text Input -->

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'soledad' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo $instance_title; ?>"/>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'podcast_series' ) ); ?>">Podcast Series:</label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'podcast_series' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'podcast_series' ) ); ?>"
                        class="widefat categories"
                        style="width:100%;">
					<?php foreach ( $series_list as $list ): ?>
                        <option value="<?php echo $list->term_id; ?>" <?php selected( $list->term_id, $instance['podcast_series'] ); ?>><?php echo $list->name; ?></option>
					<?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>"><?php esc_html_e( 'Show author name?', 'soledad' ); ?></label>
                <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'author' ) ); ?>" <?php checked( (bool) $instance['author'], true ); ?> />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'sub' ) ); ?>"><?php esc_html_e( 'Show subscribe button?', 'soledad' ); ?></label>
                <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sub' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'sub' ) ); ?>" <?php checked( (bool) $instance['sub'], true ); ?> />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'episode' ) ); ?>"><?php esc_html_e( 'Show episode count?', 'soledad' ); ?></label>
                <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'episode' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'episode' ) ); ?>" <?php checked( (bool) $instance['episode'], true ); ?> />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php esc_html_e( 'Show description?', 'soledad' ); ?></label>
                <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'desc' ) ); ?>" <?php checked( (bool) $instance['desc'], true ); ?> />
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'img_pos' ) ); ?>"><?php esc_html_e( 'Featured Image Position', 'soledad' ); ?></label>
                <select id="<?php echo esc_attr( $this->get_field_id( 'img_pos' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'img_pos' ) ); ?>" class="widefat orderby"
                        style="width:100%;">
                    <option value='' <?php if ( '' == $instance['img_pos'] ) {
						echo 'selected="selected"';
					} ?>><?php esc_html_e( "Left", 'soledad' ); ?></option>
                    <option value='top' <?php if ( 'top' == $instance['img_pos'] ) {
						echo 'selected="selected"';
					} ?>><?php esc_html_e( 'Top', 'soledad' ); ?></option>
                </select>
            </p>


			<?php
		}
	}
}
?>
