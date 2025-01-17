<?php

add_action( 'widgets_init', 'penci_finance_stock_widget' );

function penci_finance_stock_widget() {
	register_widget( 'penci_finance_stock_widget' );
}

if ( ! class_exists( 'penci_finance_stock_widget' ) ) {
	class penci_finance_stock_widget extends WP_Widget {

		/**
		 * Widget setup.
		 */
		function __construct() {
			/* Widget settings. */
			$widget_ops = array(
				'classname'   => 'penci_finance_stock_widget',
				'description' => esc_html__( 'A widget that displays the stock data.', 'penci-finance' )
			);

			/* Widget control settings. */
			$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'penci_finance_stock_widget' );

			parent::__construct( 'penci_finance_stock_widget', self::penci_get_theme_name( '.Soledad', true ) . esc_html__( 'Stock', 'penci-finance' ), $widget_ops, $control_ops );

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
			$style = isset( $instance['style'] ) && $instance['style'] ? $instance['style'] : 'rounded';


			/* Before widget (defined by themes). */
			echo ent2ncr( $before_widget );

			/* Display the widget title if one was input (before and after defined by themes). */
			if ( $title ) {
				echo ent2ncr( $before_title ) . $title . ent2ncr( $after_title );
			}

			$symbol_lists = $instance['symbols'] ? explode( ',', $instance['symbols'] ) : [];
			$data_show    = $instance['data_show'] ? $instance['data_show'] : [];
			$layout       = $instance['layout'] ? $instance['layout'] : 'style-1';

			$symbol_attr  = [];
			$symbol_class = [];

			if ( ! empty ( $symbol_lists ) ) {
				foreach ( $symbol_lists as $index => $symbol ) {
					$symbol_attr[]  = sanitize_title( $symbol );
					$symbol_class[] = 'widget-repeater-item-' . $index;
				}
				$finance_data = Penci_Finance_Stock::getQuotes( $symbol_attr );
				include plugin_dir_path( __DIR__ ) . 'templates/style-1.php';
			}

			/* After widget (defined by themes). */
			echo ent2ncr( $after_widget );

			$styles = [
				'upcolor'   => [
					'color' => '#' . $this->id . ' .penci-fnlt-item.up .pcfnlt-symbol-ask'
				],
				'downcolor' => [
					'color' => '#' . $this->id . ' .penci-fnlt-item.down .pcfnlt-symbol-ask'
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
				'title'     => esc_html__( 'Stock Data', 'penci-finance' ),
				'data_show' => [ 'longname', 'sname', 'ask', 'cur' ],
				'symbols'   => '',
				'layout'    => '',
				'upcolor'   => '',
				'downcolor' => '',
				'fsize'     => '',
			);
		}


		function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults       = $this->soledad_widget_defaults();
			$instance       = wp_parse_args( (array) $instance, $defaults );
			$instance_title = $instance['title'] ? str_replace( '"', '&quot;', $instance['title'] ) : '';
			$data_show      = isset( $instance['data_show'] ) ? $instance['data_show'] : [
				'longname',
				'sname',
				'ask',
				'cur'
			];
			$layout         = isset( $instance['layout'] ) ? $instance['layout'] : 'style-1';
			?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'penci-finance' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo esc_attr( $instance_title ); ?>" class="widefat" type="text"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'symbols' ) ); ?>"><?php esc_html_e( 'Symbols (Stock Code)', 'penci-finance' ) ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'symbols' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'symbols' ) ); ?>"
                       value="<?php echo esc_attr( $instance['symbols'] ); ?>" class="widefat" type="text"/>
            <p style="font-style: italic;"><?php _e( 'Enter the list of symbols, separated by commas. Example: <span style="color:red">AAPL,AMZN,NVDA,TSLA</span>', 'penci-finance' ) ?></p>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'data_show' ) ); ?>"><?php esc_html_e( 'Showing Term Data', 'penci-finance' ) ?></label>
                <select multiple style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'data_show' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'data_show' ) ); ?>[]">
					<?php
					$post_meta = array(
						'longname'  => __( 'Name', 'penci-finance' ),
						'sname'     => __( 'Symbol Name ( Stock Code )', 'penci-finance' ),
						'ename'     => __( 'Exchange Name', 'penci-finance' ),
						'ask'       => __( 'Price', 'penci-finance' ),
						'cur'       => __( 'Financial Currency', 'penci-finance' ),
						'mkchange'  => __( 'Regular Market Change', 'penci-finance' ),
						'mkchangep' => __( 'Regular Market Change Percent', 'penci-finance' ),
					);
					foreach ( $post_meta as $name => $label ) {
						$selected = in_array( $name, $data_show ) ? 'selected' : '';
						echo '<option ' . $selected . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout', 'penci-finance' ) ?></label>
                <select style="width:100%;"
                        id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"
                        name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<?php
					$layouts = array(
						'style-1' => __( 'Row', 'penci-finance' ),
						'style-2' => __( 'Columns', 'penci-finance' ),
					);
					foreach ( $layouts as $name => $label ) {
						echo '<option ' . selected( $name, $layout ) . ' value="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</option>';
					} ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'upcolor' ); ?>"
                       style="display:block;"><?php _e( 'Up Color:' ); ?></label>
                <input class="widefat pcwoo-color-picker color-picker"
                       id="<?php echo $this->get_field_id( 'upcolor' ); ?>"
                       name="<?php echo $this->get_field_name( 'upcolor' ); ?>" type="text"
                       value="<?php echo $instance['upcolor']; ?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'downcolor' ); ?>"
                       style="display:block;"><?php _e( 'Down Color:' ); ?></label>
                <input class="widefat pcwoo-color-picker color-picker"
                       id="<?php echo $this->get_field_id( 'downcolor' ); ?>"
                       name="<?php echo $this->get_field_name( 'downcolor' ); ?>" type="text"
                       value="<?php echo $instance['downcolor']; ?>"/>
            </p>
			<?php
		}
	}
}
?>
