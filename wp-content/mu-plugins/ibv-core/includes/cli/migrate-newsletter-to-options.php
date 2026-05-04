<?php
/**
 * One-shot migration: copy newsletter fields from Front Page meta to Site Options.
 *
 * Run via WP-CLI after deploying the field-relocation pass:
 *
 *     wp ibv migrate-newsletter
 *
 * Idempotent — running it multiple times produces the same result.
 * Empty source values are skipped; non-empty source values overwrite the
 * options destination only if the destination is empty (no clobber).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_CLI', false ) ) {
	return;
}

WP_CLI::add_command(
	'ibv migrate-newsletter',
	static function () {
		$front_id = (int) get_option( 'page_on_front' );
		if ( ! $front_id ) {
			WP_CLI::warning( 'No front page set; nothing to migrate.' );
			return;
		}

		if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
			WP_CLI::error( 'ACF is not active; cannot migrate.' );
		}

		$same_name_fields = array( 'newsletter_intro', 'newsletter_body' );
		$moved            = array();
		$skipped_empty    = array();
		$skipped_present  = array();

		foreach ( $same_name_fields as $name ) {
			$src = get_field( $name, $front_id );
			$dst = get_field( $name, 'option' );

			if ( null === $src || '' === $src || 0 === $src ) {
				$skipped_empty[] = $name;
				continue;
			}

			if ( null !== $dst && '' !== $dst && 0 !== $dst ) {
				$skipped_present[] = $name;
				continue;
			}

			update_field( $name, $src, 'option' );
			$moved[] = $name;
		}

		// Legacy front-page field name → globals option field (Brief 03).
		$src_form = get_field( 'newsletter_form_id', $front_id );
		$dst_form = get_field( 'newsletter_gravity_form_id', 'option' );

		if ( null === $src_form || '' === $src_form || 0 === $src_form ) {
			$skipped_empty[] = 'newsletter_form_id (front) → newsletter_gravity_form_id';
		} elseif ( null !== $dst_form && '' !== $dst_form && 0 !== $dst_form ) {
			$skipped_present[] = 'newsletter_gravity_form_id';
		} else {
			update_field( 'newsletter_gravity_form_id', $src_form, 'option' );
			$moved[] = 'newsletter_gravity_form_id (from front newsletter_form_id)';
		}

		if ( $moved ) {
			WP_CLI::success( 'Migrated to options: ' . implode( ', ', $moved ) );
		}
		if ( $skipped_empty ) {
			WP_CLI::log( 'Skipped (empty on source): ' . implode( ', ', $skipped_empty ) );
		}
		if ( $skipped_present ) {
			WP_CLI::log( 'Skipped (destination already set): ' . implode( ', ', $skipped_present ) );
		}
	}
);
