<?php
/**
 * Contact form template
 *
 * @category Wodrpress-Plugins
 * @package  WP-FoodTec
 * @author   FoodTec Solutions <info@foodtecsolutions.com>
 * @license  GPLv2 or later
 * @link     https://gitlab.foodtecsolutions.com/fts/wordpress/plugins/wp-foodtec
 * @since    1.0.0
 *
 * @phan-file-suppress PhanUndeclaredGlobalVariable $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<form method="post" data-nonce="<?php echo $args['nonce']; ?>" class="contact-form">

	<div class="form-group form-group-lg">
		<label class="control-label" for="contact-name"><?php _e( 'Name:', 'wp-foodtec' ); ?></label>
		<input class="form-control" placeholder="<?php _e( 'enter your name', 'wp-foodtec' ); ?>" type="text" name="fullName" id="contact-name">
	</div>

	<?php
	if ($args['has_phone']) :
		?>
		<div class="form-group form-group-lg required">
		<label class="control-label" for="contact-email"><?php _e( 'Phone Number:', 'wp-foodtec' ); ?></label>
		<input class="form-control" placeholder="<?php _e( 'enter your phone number', 'wp-foodtec' ); ?>" type="tel" name="phone" id="contact-phone" maxlength="14" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
		</div>
		<?php
	endif;
	?>

	<?php
	if ($args['store_select']) :
		?>
		<div class="form-group form-group-lg">
			<label class="control-label" for="contact-subject"><?php _e( 'Location:', 'wp-foodtec' ); ?></label>
			<div class="row"><?php echo $args['store_select']; ?></div>
		</div>
		<?php
	endif;
	?>

	<div class="form-group form-group-lg required">
		<span class="required-asterisk" aria-hidden="true">*</span>
		<span class="sr-only"><?php _e( 'Required', 'wp-foodtec' ); ?></span>
		<label class="control-label" for="contact-email"><?php _e( 'Email:', 'wp-foodtec' ); ?></label>
		<input class="form-control" placeholder="<?php _e( 'enter your email', 'wp-foodtec' ); ?>" type="email" name="email" id="contact-email" required>
	</div>

	<?php
	if ($args['needs_subject']) :
		?>
		<div class="form-group form-group-lg required">
			<span class="required-asterisk" aria-hidden="true">*</span>
			<span class="sr-only"><?php _e( 'Required', 'wp-foodtec' ); ?></span>
			<label class="control-label" for="contact-subject"><?php _e( 'Subject:', 'wp-foodtec' ); ?></label>
			<input class="form-control" placeholder="<?php _e( 'enter the subject', 'wp-foodtec' ); ?>" type="text" name="subject" id="contact-subject" required>
		</div>
		<?php
	endif;
	?>

	<div class="form-group form-group-lg required">
		<span class="required-asterisk" aria-hidden="true">*</span>
		<span class="sr-only"><?php _e( 'Required', 'wp-foodtec' ); ?></span>
		<label class="control-label" for="contact-message"><?php _e( 'Message:', 'wp-foodtec' ); ?></label>
		<textarea class="form-control" rows="3" placeholder="<?php _e( 'enter your message', 'wp-foodtec' ); ?>" name="message" id="contact-message" required></textarea>
	</div>

	<div class="form-group">
		<?php echo $args['recaptcha']; ?>
	</div>

	<p class="alert hidden" tabindex="-1"></p>

	<div class="form-group">
		<button type="submit" class="btn btn-lg btn-primary"><?php _e( 'Submit', 'wp-foodtec' ); ?></button>
	</div>

</form>
