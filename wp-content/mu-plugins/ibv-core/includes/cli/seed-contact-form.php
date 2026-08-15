<?php
/**
 * Seeder — "Contact" Gravity Form (contact page template).
 *
 * Replaces the legacy old-site "Contact form" (id 3), which the contact page
 * embedded until Aug 2026. That form carried a reCAPTCHA field whose keys were
 * registered to the old .co.uk domain — Google rejects them on the new domains
 * ("ERROR for site owner: Invalid site key") — plus dead UTM hidden fields the
 * new theme never populates. This form keeps the same substance (fields,
 * notification recipient + subject, confirmation copy) minus the captcha:
 * antispam is GF's honeypot, matching the other new-site forms (33/34/35).
 * The legacy form is left untouched in case old entries need referencing.
 *
 * Idempotent UPSERT: updates the existing form (id in `ibv_contact_form_id`,
 * else title match) in place — this seeder is the single source of truth for
 * the form config (don't hand-edit it in the GF admin; edit here and re-apply).
 * After the upsert it re-points the contact page's ACF form-id field at this
 * form, so each environment self-heals even when a content push overwrites
 * `wp_postmeta` (GF tables are never pushed; ids can drift between envs).
 *
 * This file only DEFINES ibv_seed_contact_form(); nothing runs on include.
 * Apply it via `wp ibv seed` (local WP-CLI) or the one-shot deploy trigger
 * (seed-forms-once.php) on a no-SSH server.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ibv_seed_contact_form' ) ) {
	/**
	 * Create or update the "Contact" form to match this definition.
	 *
	 * @return string Human-readable status line.
	 */
	function ibv_seed_contact_form() {
		if ( ! class_exists( 'GFAPI' ) ) {
			return 'Contact: GFAPI unavailable — is Gravity Forms active?';
		}

		$title = 'Contact';

		// T&C link resolves per environment at seed time (the legacy form
		// hardcoded a single environment's URL into the choice text).
		$terms_page = get_page_by_path( 'terms-conditions' );
		$terms_url  = $terms_page ? get_permalink( $terms_page ) : home_url( '/terms-conditions/' );
		$terms_html = 'I agree to the <a href="' . esc_url( $terms_url ) . '" target="_blank" rel="noopener">Terms and Conditions</a>';

		$form = array(
			'title'          => $title,
			'description'    => '',
			'labelPlacement' => 'top_label',
			'markupVersion'  => 2, // modern markup — choices render as divs, not a bulleted <ul>.
			'enableHoneypot' => true,
			'is_active'      => 1,
			'button'         => array(
				'type' => 'text',
				'text' => 'Send enquiry',
			),
			'fields'         => array(
				array( 'id' => 1, 'type' => 'text',     'label' => 'Name',  'isRequired' => true ),
				array( 'id' => 2, 'type' => 'email',    'label' => 'Email', 'isRequired' => true ),
				array( 'id' => 3, 'type' => 'phone',    'label' => 'Phone', 'phoneFormat' => 'international' ),
				array( 'id' => 4, 'type' => 'number',   'label' => 'Number of guests' ),
				array( 'id' => 5, 'type' => 'textarea', 'label' => 'Message', 'placeholder' => 'Message...', 'isRequired' => true ),
				array( 'id' => 6, 'type' => 'text',     'label' => 'Where did you hear about us?' ),
				array(
					'id'             => 7,
					'type'           => 'checkbox',
					'label'          => 'Terms and Conditions',
					'labelPlacement' => 'hidden_label',
					'choices'        => array(
						array(
							'text'  => $terms_html,
							'value' => 'I agree to the Terms and Conditions',
						),
					),
					'inputs'         => array(
						array( 'id' => '7.1', 'label' => 'I agree to the Terms and Conditions' ),
					),
				),
				array(
					'id'             => 8,
					'type'           => 'checkbox',
					'label'          => 'Marketing preference',
					'labelPlacement' => 'hidden_label',
					'choices'        => array(
						array(
							'text'  => "I don't want to get special discounts to my inbox",
							'value' => 'No marketing emails',
						),
					),
					'inputs'         => array(
						array( 'id' => '8.1', 'label' => "I don't want to get special discounts to my inbox" ),
					),
				),
			),
			'confirmations'  => array(
				'ibv_contact_message' => array(
					'id'        => 'ibv_contact_message',
					'name'      => 'Default Confirmation',
					'isDefault' => true,
					'type'      => 'message',
					'message'   => 'Thanks for contacting us! We will get in touch with you shortly.',
				),
			),
			// Same recipient + subject as the legacy contact form, so existing
			// inbox rules keep matching.
			'notifications'  => array(
				'ibv_contact_admin' => array(
					'id'       => 'ibv_contact_admin',
					'isActive' => true,
					'name'     => 'Admin Notification',
					'event'    => 'form_submission',
					'toType'   => 'email',
					'to'       => 'bookings@ibizavillas2000.com',
					'replyTo'  => '{Email:2}',
					'subject'  => 'Ibiza Villas Enquiry - {Name:1}',
					'message'  => '{all_fields}',
				),
			),
		);

		// Resolve an existing form id: option first, then a title match.
		$existing_id = (int) get_option( 'ibv_contact_form_id' );
		if ( ! $existing_id || ! GFAPI::get_form( $existing_id ) ) {
			$existing_id = 0;
			foreach ( GFAPI::get_forms() as $candidate ) {
				if ( isset( $candidate['title'] ) && $candidate['title'] === $title ) {
					$existing_id = (int) $candidate['id'];
					break;
				}
			}
		}

		if ( $existing_id ) {
			$form['id'] = $existing_id;
			$result     = GFAPI::update_form( $form, $existing_id );
			if ( is_wp_error( $result ) ) {
				return 'Error updating Contact form: ' . $result->get_error_message();
			}
			update_option( 'ibv_contact_form_id', $existing_id );
			ibv_seed_contact_form_point_page( $existing_id );
			return 'Updated Contact form id: ' . $existing_id;
		}

		$result = GFAPI::add_form( $form );
		if ( is_wp_error( $result ) ) {
			return 'Error creating Contact form: ' . $result->get_error_message();
		}

		update_option( 'ibv_contact_form_id', (int) $result );
		ibv_seed_contact_form_point_page( (int) $result );
		return 'Created Contact form id: ' . (int) $result;
	}
}

if ( ! function_exists( 'ibv_seed_contact_form_point_page' ) ) {
	/**
	 * Point every page on the Contact template at the seeded form.
	 *
	 * The ACF key reference (`_contact_enquiry_gravity_form_id`) already exists
	 * on the page, so a plain meta update keeps ACF happy.
	 *
	 * @param int $form_id Seeded Gravity Form id.
	 */
	function ibv_seed_contact_form_point_page( $form_id ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'page-contact.php',
			)
		);

		foreach ( $pages as $page_id ) {
			update_post_meta( $page_id, 'contact_enquiry_gravity_form_id', (int) $form_id );
		}
	}
}
