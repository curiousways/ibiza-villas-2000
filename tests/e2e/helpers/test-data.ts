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

/** A stable published villa (Villa Daniel). */
export const VILLA_ID = Number( process.env.E2E_VILLA_ID || 2782 );
export const VILLA_NAME = process.env.E2E_VILLA_NAME || 'Villa Daniel';
export const VILLA_SLUG = process.env.E2E_VILLA_SLUG || 'villa-daniel';

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
