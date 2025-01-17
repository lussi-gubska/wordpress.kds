<?php

add_action( 'widgets_init', 'penci_sport_standing_widget' );

function penci_sport_standing_widget() {
	register_widget( 'penci_sport_standing_widget' );
}

if ( ! class_exists( 'penci_sport_standing_widget' ) ) {
	class penci_sport_standing_widget extends WP_Widget {

		/**
		 * Widget setup.
		 */
		function __construct() {
			/* Widget settings. */
			$widget_ops = array(
				'classname'   => 'penci_sport_standing_widget',
				'description' => esc_html__( 'A widget that displays the football league standing table.', 'penci-sport' )
			);

			/* Widget control settings. */
			$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'penci_sport_standing_widget' );

			parent::__construct( 'penci_sport_standing_widget', self::penci_get_theme_name( '.Soledad', true ) . esc_html__( 'Football Standing', 'penci-sport' ), $widget_ops, $control_ops );

		}

		function penci_get_theme_name( $name = 'Penci', $dot = false ) {

			$theme_name = get_theme_mod( 'admin_wel_page_sname' );

			if ( $theme_name && get_theme_mod( 'activate_white_label' ) ) {
				$name = $dot ? '.' . $theme_name : $theme_name;
			}

			return $name . ' ';
		}

		/**
		 * How to display the widget on the screen.
		 */
		function widget( $args, $instance ) {
			extract( $args );

			/* Our variables from the widget settings. */
			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			$title = apply_filters( 'widget_title', $title );


			/* Before widget (defined by themes). */
			echo ent2ncr( $before_widget );

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) {
				echo ent2ncr( $before_title ) . $title . ent2ncr( $after_title );
			}

			if ( empty( $instance['token'] ) && current_user_can( 'manage_options' ) ) {
				echo __( 'Please enter the Token Key', 'penci-sport' );
			} else {
				$settings = [
					'token'         => $instance['token'],
					'league'        => $instance['league'],
					'data_show'     => $instance['data_show'],
					'layout'        => isset( $instance['layout'] ) ? $instance['layout'] : 'style-1',
					'text_position' => $instance['text_position'],
					'text_club'     => $instance['text_club'],
					'text_played'   => $instance['text_played'],
					'text_won'      => $instance['text_won'],
					'text_drawn'    => $instance['text_drawn'],
					'text_lost'     => $instance['text_lost'],
					'text_gf'       => $instance['text_gf'],
					'text_ga'       => $instance['text_ga'],
					'text_gd'       => $instance['text_gd'],
					'text_points'   => $instance['text_points'],
				];
				include plugin_dir_path( __DIR__ ) . 'templates/standing.php';
			}

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
			return array(
				'title'         => esc_html__( 'Standing', 'penci-sport' ),
				'token'         => '',
				'league'        => 'PL',
				'layout'        => 'style-1',
				'data_show'     => [ 'position', 'club', 'points' ],
				'text_position' => __( 'Position', 'penci-sport' ),
				'text_club'     => __( 'Club', 'penci-sport' ),
				'text_played'   => __( 'Played', 'penci-sport' ),
				'text_won'      => __( 'Won', 'penci-sport' ),
				'text_drawn'    => __( 'Drawn', 'penci-sport' ),
				'text_lost'     => __( 'Lost', 'penci-sport' ),
				'text_gf'       => __( 'GF', 'penci-sport' ),
				'text_ga'       => __( 'GA', 'penci-sport' ),
				'text_gd'       => __( 'GD', 'penci-sport' ),
				'text_points'   => __( 'Points', 'penci-sport' ),
			);
		}

		public function isupdown( $value ) {
			return $value < 0 ? 'down' : 'up';
		}

		function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults       = $this->soledad_widget_defaults();
			$instance       = wp_parse_args( (array) $instance, $defaults );
			$instance_title = $instance['title'] ? str_replace( '"', '&quot;', $instance['title'] ) : '';
			$token          = isset( $instance['token'] ) ? $instance['token'] : '';
			$league         = isset( $instance['league'] ) ? $instance['league'] : 'PL';
			$data_show      = isset( $instance['data_show'] ) ? $instance['data_show'] : [];
			$layout         = isset( $instance['layout'] ) ? $instance['layout'] : 'style-1';
			?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'penci-sport' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo esc_attr( $instance_title ); ?>" class="widefat" type="text"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'token' ) ); ?>"><?php esc_html_e( 'Token', 'penci-sport' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'token' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'token' ) ); ?>"
                       value="<?php echo esc_attr( $token ); ?>" class="widefat" type="text"/>
                <span style="font-weight: bold;margin-top: 5px;display: inline-block;"><?php echo penci_sport_api_help(); ?></span>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'league' ) ); ?>"><?php esc_html_e( 'League', 'penci-sport' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'league' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'league' ) ); ?>">
					<?php
					$league_op = penci_sport_list_league();
					foreach ( $league_op as $name => $label ) {
						echo '<option ' . selected( $name, $league ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout', 'penci-sport' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<?php
					$layout_op = array(
						'style-1' => __( 'Table - Style 1', 'penci-sport' ),
						'style-2' => __( 'Table - Style 2', 'penci-sport' ),
					);
					foreach ( $layout_op as $name => $label ) {
						echo '<option ' . selected( $name, $layout ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'data_show' ) ); ?>"><?php esc_html_e( 'Showing Data', 'penci-sport' ) ?></label>
                <select multiple style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'data_show' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'data_show' ) ); ?>[]">
					<?php
					$data_cols = array(
						'position' => __( 'Position', 'penci-sport' ),
						'club'     => __( 'Club', 'penci-sport' ),
						'played'   => __( 'Played', 'penci-sport' ),
						'won'      => __( 'Won', 'penci-sport' ),
						'drawn'    => __( 'Drawn', 'penci-sport' ),
						'lost'     => __( 'Lost', 'penci-sport' ),
						'gf'       => __( 'GF', 'penci-sport' ),
						'ga'       => __( 'GA', 'penci-sport' ),
						'gd'       => __( 'GD', 'penci-sport' ),
						'points'   => __( 'Points', 'penci-sport' ),
					);
					foreach ( $data_cols as $name => $label ) {
						$selected = in_array( $name, $data_show ) ? 'selected' : '';
						echo '<option ' . $selected . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
			<?php
			foreach ( $data_cols as $id => $text ) {
				$field_id = 'text_' . $id;
				?>
                <p>
                    <label for="<?php echo esc_attr( $this->get_field_id( $field_id ) ); ?>"><?php echo esc_html__( 'Text: ', 'penci-sport' ) . $text; ?></label>
                    <input id="<?php echo esc_attr( $this->get_field_id( $field_id ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( $field_id ) ); ?>"
                           value="<?php echo esc_attr( $instance[ $field_id ] ); ?>" class="widefat" type="text"/>
                </p>
				<?php
			}
			?>
			<?php
		}
	}
}
?>
