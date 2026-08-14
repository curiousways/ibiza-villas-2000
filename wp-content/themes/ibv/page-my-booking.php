<?php
/**
 * Template Name: My Booking
 *
 * Booking-reference lookup page. A single section collects the guest's
 * reference and submits it to Bob's booking form (see
 * `ibv_core_section_my_booking()` in ibv-core). Replaces the old theme's
 * "My Booking" jQuery modal.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_section_my_booking();

endwhile;

get_footer();
