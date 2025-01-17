<?php

add_action( 'widgets_init', 'penci_sport_fixture_widget' );

function penci_sport_fixture_widget() {
	register_widget( 'penci_sport_fixture_widget' );
}

if ( ! class_exists( 'penci_sport_fixture_widget' ) ) {
	class penci_sport_fixture_widget extends WP_Widget {

		/**
		 * Widget setup.
		 */
		function __construct() {
			/* Widget settings. */
			$widget_ops = array(
				'classname'   => 'penci_sport_fixture_widget',
				'description' => esc_html__( 'A widget that displays the football fixture/result data.', 'penci-sport' )
			);

			/* Widget control settings. */
			$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'penci_sport_fixture_widget' );

			parent::__construct( 'penci_sport_fixture_widget', self::penci_get_theme_name( '.Soledad', true ) . esc_html__( 'Football Fixture', 'penci-sport' ), $widget_ops, $control_ops );

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
					'token'  => $instance['token'],
					'league' => $instance['league'],
					'status' => $instance['status'],
					'limit'  => $instance['limit'],
				];
				include plugin_dir_path( __DIR__ ) . 'templates/fixture.php';
			}

			$styles = [
				'gaps' => [
					'--gap' => '#' . $this->id . ' .penci-football-matches'
				],
				'cols' => [
					'--col' => '#' . $this->id . ' .penci-football-matches'
				],
			];

			$out = '';

			foreach ( $styles as $option => $selectors ) {
				$value = isset( $instance[ $option ] ) ? $instance[ $option ] : '';
				if ( $value ) {
					foreach ( $selectors as $prop => $selector ) {
						$prefix = 'font-size' == $prop ? 'px' : '';
						$out    .= $selector . '{' . $prop . ':' . $value . $prefix . '}';
					}
				}
			}

			if ( $out ) {
				echo '<style>' . $out . '</style>';
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
				'title'  => esc_html__( 'Upcoming Matches', 'penci-sport' ),
				'status' => 'SCHEDULED',
				'cols'   => '1',
				'league' => 'PL',
				'token'  => '',
				'limit'  => 10,
				'gaps'   => '10px',
			);
		}

		function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults       = $this->soledad_widget_defaults();
			$instance       = wp_parse_args( (array) $instance, $defaults );
			$instance_title = $instance['title'] ? str_replace( '"', '&quot;', $instance['title'] ) : '';
			$status         = isset( $instance['status'] ) ? $instance['status'] : 'SCHEDULED';
			$cols           = isset( $instance['cols'] ) ? $instance['cols'] : '1';
			$league         = isset( $instance['league'] ) ? $instance['league'] : 'PL';
			$token          = isset( $instance['token'] ) ? $instance['token'] : '';
			$gaps           = isset( $instance['gaps'] ) ? $instance['gaps'] : '10px';
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
                <span style="font-weight: bold;margin-top: 5px;display: inline-block;"><?php echo penci_sport_api_help();?></span>
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
                <label for="<?php echo esc_attr( $this->get_field_id( 'status' ) ); ?>"><?php esc_html_e( 'Status', 'penci-sport' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'status' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'status' ) ); ?>">
					<?php
					$status_op = array(
						'SCHEDULED' => __( 'Scheduled', 'penci-sport' ),
						'FINISHED'  => __( 'Finished', 'penci-sport' ),
					);
					foreach ( $status_op as $name => $label ) {
						echo '<option ' . selected( $name, $status ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of Items to Show', 'penci-sport' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>"
                       value="<?php echo esc_attr( $instance['limit'] ); ?>" class="widefat" type="text"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'cols' ) ); ?>"><?php esc_html_e( 'Number of Columns', 'penci-sport' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'cols' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'cols' ) ); ?>">
					<?php
					$cols_op = array(
						'1' => __( '1 Column', 'penci-sport' ),
						'2' => __( '2 Columns', 'penci-sport' ),
						'3' => __( '3 Columns', 'penci-sport' ),
					);
					foreach ( $cols_op as $name => $label ) {
						echo '<option ' . selected( $name, $cols ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'gaps' ) ); ?>"><?php esc_html_e( 'Spacing Between Items', 'penci-sport' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'gaps' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'gaps' ) ); ?>"
                       value="<?php echo esc_attr( $instance['gaps'] ); ?>" class="widefat" type="text"/>
            </p>
			<?php
		}
	}
}
?>
