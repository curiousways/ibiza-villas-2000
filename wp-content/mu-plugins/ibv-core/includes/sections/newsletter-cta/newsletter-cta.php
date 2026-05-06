<?php
/**
 * Section: Newsletter CTA (footer; copy in Site Options, form via component).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer newsletter block: intro/body from options, Gravity Form via helper.
 */
function ibv_core_section_newsletter_cta() {
	$intro = (string) get_field( 'newsletter_intro', 'option' );
	$body  = (string) get_field( 'newsletter_body', 'option' );

	if ( ! $intro && ! $body ) {
		return;
	}

	if ( ! $intro ) {
		$intro = __( 'Newsletter set-up', 'ibv' );
	}
	if ( ! $body ) {
		$body = __( 'Sign up to receive marketing from Ibiza Villas 2000', 'ibv' );
	}

	ibv_core_newsletter_form(
		[
			'title'       => $intro,
			'description' => $body,
			'variant'     => 'footer',
		]
	);
}
