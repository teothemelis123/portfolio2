<?php
/**
 * Contact Form widget
 *
 * @category Wodrpress-Plugins
 * @package  WP-FoodTec
 * @author   FoodTec Solutions <info@foodtecsolutions.com>
 * @license  GPLv2 or later
 * @link     https://gitlab.foodtecsolutions.com/fts/wordpress/plugins/wp-foodtec
 * @since    1.0.0
 */

namespace WP_FoodTec\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds Contact Form widget.
 */
class Contact_Form extends \WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct( 'wp_foodtec_contact_widget', __( 'FoodTec Contact', 'wp-foodtec' ) );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->get_defaults() );

		$title         = esc_attr( $instance['title'] );
		$needs_subject = esc_attr( $instance['needs_subject'] );
		$theme         = esc_attr( $instance['theme'] ) ?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'wp-foodtec' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		<input class="widefat" id="<?php echo $this->get_field_id( 'needs_subject' ); ?>" name="<?php echo $this->get_field_name( 'needs_subject' ); ?>" type="checkbox" <?php checked( $needs_subject, 'on', true ); ?> />
		<label for="<?php echo $this->get_field_id( 'needs_subject' ); ?>"><?php _e( 'Ask for subject', 'wp-foodtec' ); ?></label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'theme' ); ?>"><?php _e( 'Recaptcha Theme', 'wp-foodtec' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'theme' ); ?>" name="<?php echo $this->get_field_name( 'theme' ); ?>">
			<option value="light" <?php selected( $theme, 'light' ); ?>><?php _e( 'Light', 'wp-foodtec' ); ?></option>
			<option value="dark" <?php selected( $theme, 'dark' ); ?>><?php _e( 'Dark', 'wp-foodtec' ); ?></option>
		</select>
		</p>

		<?php
		return 'noform';
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['needs_subject'] = strip_tags( $new_instance['needs_subject'] );
		$instance['theme']         = strip_tags( $new_instance['theme'] );

		return $instance;
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->get_defaults() );

		$title         = apply_filters( 'widget_title', $instance['title'] );
		$needs_subject = esc_attr( $instance['needs_subject'] );
		$theme         = esc_attr( $instance['theme'] );

		echo $args['before_widget'];

		echo '<div class="widget-contact wp_widget_plugin_box">';

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo do_shortcode( '[foodtec_contact needs_subject="' . $needs_subject . '"  theme="' . $theme . '"/]' );

		echo '</div>';

		echo $args['after_widget'];
	}

	/**
	 * Returns the widget default options.
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'title'         => __( 'Contact', 'wp-foodtec' ),
			'needs_subject' => false,
			'theme'         => 'light',
		);
	}
}
