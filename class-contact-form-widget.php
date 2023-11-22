<?php
/**
 * Contact shortcode
 *
 * @category Wodrpress-Plugins
 * @package  WP-FoodTec
 * @author   FoodTec Solutions <info@foodtecsolutions.com>
 * @license  GPLv2 or later
 * @link     https://gitlab.foodtecsolutions.com/fts/wordpress/plugins/wp-foodtec
 * @since    1.0.0
 */

namespace WP_FoodTec\Includes\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact shortcode class
 */
class Contact_Form {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'foodtec_contact', array( $this, 'contact_callback' ) );

		add_action( 'wp_ajax_contact', array( $this, 'contact' ) );
		add_action( 'wp_ajax_nopriv_contact', array( $this, 'contact' ) );

		add_action( 'register_shortcode_ui', array( $this, 'shortcode_foodtec_contact' ) );
	}

	/**
	 * The foodtec_contact shortcode callback function.
	 *
	 * @param array $atts The shortcode attributes.
	 *
	 * @return string
	 */
	public function contact_callback( $atts ): string {
		if ( ! is_ssl() && ! WP_DEBUG ) {
			( new Includes\Libraries\Error )->report_not_secure();
			return '';
		}

		$atts = shortcode_atts(
			array(
				'theme'         => 'light',
				'has_phone'     => false,
				'store_select'  => false,
				'needs_subject' => false,
			),
			$atts
		);

		$html_helpers = new Includes\Libraries\Html_Helpers();

		$config = ( new Includes\Libraries\Requests\Marketing\Config )->request();
		$stores = $config->response->stores;

		return ( new Includes\Libraries\Template )->load(
			'contact.php',
			array(
				'recaptcha'     => ( new Includes\Libraries\Google_Recaptcha )->html( $atts['theme'] ),
				'store_select'  => filter_var( $atts['store_select'], FILTER_VALIDATE_BOOLEAN ) ? $html_helpers->get_simple_store_select( $stores ) : null,
				'has_phone'     => filter_var( $atts['has_phone'], FILTER_VALIDATE_BOOLEAN ),
				'needs_subject' => filter_var( $atts['needs_subject'], FILTER_VALIDATE_BOOLEAN ),
				'nonce'         => wp_create_nonce( 'contact_nonce' ),
			)
		);
	}

	/**
	 * Sends the email.
	 *
	 * @return void
	 */
	public function contact() {
		$ajax_options = array(
			'nonce'           => 'contact_nonce',
			'recaptcha'       => true,
			'ssl'             => true,
			'query_arguments' => array(
				'fullName' => array(
					'display_name'      => __( 'Name', 'wp-foodtec' ),
					'sanitize_function' => 'sanitize_text_field',
					'required'          => false,
				),
				'phone'    => array(
					'display_name'      => __( 'Phone Number', 'wp-foodtec' ),
					'sanitize_function' => 'sanitize_text_field',
					'required'          => false,
				),
				'store'    => array(
					'display_name'      => __( 'Location', 'wp-foodtec' ),
					'sanitize_function' => 'sanitize_text_field',
					'encoding_function' => 'base64_decode',
					'required'          => false,
				),
				'email'    => array(
					'display_name'      => __( 'Email', 'wp-foodtec' ),
					'sanitize_function' => 'sanitize_email',
					'required'          => true,
				),
				'subject'  => array(
					'display_name'      => __( 'Subject', 'wp-foodtec' ),
					'sanitize_function' => 'sanitize_text_field',
					'required'          => false,
				),
				'message'  => array(
					'display_name'      => __( 'Message', 'wp-foodtec' ),
					'sanitize_function' => 'sanitize_textarea_field',
					'encoding_function' => 'stripslashes',
					'required'          => true,
				),
			),
		);

		$params = ( new Includes\Libraries\Ajax_Validator )->validate( $ajax_options );

		$message = '';

		foreach ( $params as $key => $value ) {
			$message .= "<strong>{$ajax_options['query_arguments'][$key]['display_name']}:</strong> $value<br>";
		}

		$headers = array( "Reply-To: {$params['fullName']} <{$params['email']}>" );

		$sendmail = wp_mail( get_option( 'foodtec_corporate_email' ), 'Corporate Contact Form', $message, $headers );

		if ( $sendmail ) {
			exit( json_encode( 200 ) );
		}

		exit( 'Something went wrong. Please try again.' );
	}

	/**
	 * Registers the shortcode UI.
	 *
	 * @see https://github.com/wp-shortcake/Shortcake/blob/master/dev.php
	 *
	 * @return void
	 */
	public function shortcode_foodtec_contact() {
		$fields = array(
			array(
				'label'   => esc_html__( 'Recaptcha Theme', 'wp-foodtec' ),
				'attr'    => 'theme',
				'type'    => 'select',
				'value'   => 'light',
				'options' => array(
					array(
						'value' => 'light',
						'label' => esc_html__( 'Light', 'wp-foodtec' ),
					),
					array(
						'value' => 'dark',
						'label' => esc_html__( 'Dark', 'wp-foodtec' ),
					),
				),
			),
			array(
				'label' => esc_html__( 'Ask Location', 'wp-foodtec' ),
				'attr'  => 'store_select',
				'type'  => 'checkbox',
			),
			array(
				'label' => esc_html__( 'Ask Phone Number', 'wp-foodtec' ),
				'attr'  => 'has_phone',
				'type'  => 'checkbox',
			),
			array(
				'label' => esc_html__( 'Require Subject', 'wp-foodtec' ),
				'attr'  => 'needs_subject',
				'type'  => 'checkbox',
			),
		);

		$shortcode_ui_args = array(
			'label'         => esc_html__( 'Contact Form', 'wp-foodtec' ),
			'listItemImage' => 'dashicons-email',
			'attrs'         => $fields,
		);

		// @phan-suppress-next-line PhanUndeclaredFunction
		shortcode_ui_register_for_shortcode( 'foodtec_contact', $shortcode_ui_args );
	}
}
