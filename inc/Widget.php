<?php
/**
 * @package SimpleGoogleAds
 */

namespace SimpleGoogleAds;

use WP_Widget;

/**
 * Ad widget class.
 */
class Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'simple-google-ads',
			__( 'Simple Google Ads', 'simple-google-ads' )
		);
	}

	public $args = [
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
		'before_widget' => '<div class="widget-wrap">',
		'after_widget'  => '</div></div>',
	];

	/**
	 * Displays the ad widget on the front end.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ): void {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		/** This action is documented in inc/namespace.php */
		do_action( 'simple-google-ads.ad_tag', $instance['tag'] );

		echo $args['after_widget'];
	}

	/**
	 * Displays the widget's settings form.
	 *
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance ): void {
		$title  = $instance['title'] ?? '';
		$ad_tag = $instance['tag'] ?? '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_attr_e( 'Title', 'simple-google-ads' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>">
				<?php esc_attr_e( 'Ad tag', 'simple-google-ads' ); ?>
			</label>
			<select
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>"
			>
				<?php foreach ( get_ad_tags() as $tag ) : ?>
					<option <?php selected( $tag['tag'], $ad_tag ); ?>><?php echo esc_html( $tag['tag'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p><?php _e( 'Choose the ad tag that you want to display', 'simple-google-ads' ); ?></p>
		<?php

	}

	/**
	 * Handles widget settings updates.
	 *
	 * @param array $new_instance Old widget instance.
	 * @param array $old_instance New widget instance.
	 * @return array Updated widget instance.
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance = [];

		$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['tag']   = isset( $new_instance['tag'] ) ? sanitize_text_field( $new_instance['tag'] ) : '';

		return $instance;
	}

}
