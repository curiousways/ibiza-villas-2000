/**
 * Environment-specific fixtures.
 *
 * Staging was pushed from this local database (wp_posts/wp_postmeta),
 * so post IDs match across both environments. Override via env vars if
 * they ever diverge.
 */
export const LISTING_PATH = process.env.E2E_LISTING_PATH || '/our-villas/';

export const CONFIRMATION_PATH =
	process.env.E2E_CONFIRMATION_PATH || '/booking-request-received/';

/**
 * A stable published villa with a Bob property_id ("savines").
 *
 * NOT Villa Daniel (2782): its bare permalink carries a legacy redirect
 * to the old /villas/rental/{location}/{slug}/ URL shape, which then
 * falls through to the homepage — any test navigating to the detail
 * page without query params would land on the wrong page.
 *
 * NOT 18125 "Stunning Villa in Playa d'en Bossa" (the previous fixture):
 * it is one of the six parked legacy listings from the villa-content
 * migration and was set to draft in the 14 Aug content pass.
 */
export const VILLA_ID = Number( process.env.E2E_VILLA_ID || 6998 );
export const VILLA_NAME = process.env.E2E_VILLA_NAME || 'Villa Savines';
export const VILLA_SLUG = process.env.E2E_VILLA_SLUG || 'villa-savines';

/**
 * Villa detail permalink. The `villas` CPT registers no custom `rewrite`
 * arg (post-types/villa.php), so WordPress uses the post type name as
 * the rewrite slug: /villas/{post_name}/.
 */
export const VILLA_PATH = process.env.E2E_VILLA_PATH || `/villas/${ VILLA_SLUG }/`;

/** A published post that is NOT a villa (the villa listing page). */
export const NON_VILLA_POST_ID = Number(
	process.env.E2E_NON_VILLA_POST_ID || 18540
);
