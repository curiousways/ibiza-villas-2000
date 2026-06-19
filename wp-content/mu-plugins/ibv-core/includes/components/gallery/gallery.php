<?php
/**
 * Component: Property gallery — inline viewer (Figma node 1:6019).
 *
 * On-page: serif "Gallery" heading + sage "View all photos" button +
 * wide-cropped main image with prev/next chevrons + 6-up thumb strip.
 * Prev/next page the main image in place (wrapping across all images);
 * thumbs jump to their image; ArrowLeft/Right page while the gallery has
 * focus. No pop-up/lightbox — the main image is the viewer.
 *
 * Single-image villas render just the hero — no thumbs/nav.
 *
 * Uses ACF `property_images` gallery — see register-villa-fields.php.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_gallery( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	$rows = get_field( 'property_images', $villa_id );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}

	$images = [];
	foreach ( $rows as $row ) {
		$id = 0;
		if ( is_array( $row ) && ! empty( $row['ID'] ) ) {
			$id = (int) $row['ID'];
		}
		if ( ! $id ) {
			continue;
		}
		$src = wp_get_attachment_image_src( $id, 'large' );
		if ( empty( $src[0] ) ) {
			continue;
		}
		$alt = '';
		if ( is_array( $row ) && ! empty( $row['alt'] ) ) {
			$alt = (string) $row['alt'];
		}
		if ( '' === $alt ) {
			$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		}
		// Inline viewer swaps the main image src on prev/next. Thumbs render
		// server-side from the `id`, so we don't need a thumb URL in the JSON.
		$images[] = [
			'id'  => $id,
			'src' => $src[0],
			'alt' => $alt,
		];
	}

	if ( ! count( $images ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-gallery' );

	$uid          = wp_unique_id( 'ibv-gallery-' );
	$multiples    = count( $images ) > 1;
	$escaped_json = esc_attr( wp_json_encode( $images ) );

	if ( $multiples ) {
		wp_enqueue_script( 'ibv-gallery-script' );

		$inline = 'document.addEventListener("DOMContentLoaded",function(){var root=document.getElementById("' . esc_js( $uid ) . '");if(!root)return;var data=JSON.parse(root.getAttribute("data-images"));var mainImg=root.querySelector(".ibv-gallery__main-image");if(!mainImg)return;var thumbs=root.querySelectorAll(".ibv-gallery__thumb[data-ibv-gallery-show]");var ix=0;function show(i){ix=(i+data.length)%data.length;var cur=data[ix];mainImg.removeAttribute("srcset");mainImg.removeAttribute("sizes");mainImg.src=cur.src;mainImg.alt=cur.alt||"";thumbs.forEach(function(t){t.classList.toggle("is-active",parseInt(t.getAttribute("data-ibv-gallery-show"),10)===ix);});}root.querySelectorAll("[data-ibv-gallery-show]").forEach(function(btn){btn.addEventListener("click",function(){show(parseInt(btn.getAttribute("data-ibv-gallery-show"),10)||0);});});var prev=root.querySelector("[data-ibv-gallery-prev]");if(prev){prev.addEventListener("click",function(){show(ix-1);});}var next=root.querySelector("[data-ibv-gallery-next]");if(next){next.addEventListener("click",function(){show(ix+1);});}root.addEventListener("keydown",function(e){if(e.key==="ArrowRight"){show(ix+1);}else if(e.key==="ArrowLeft"){show(ix-1);}});});';

		wp_add_inline_script( 'ibv-gallery-script', $inline );
	}

	$hero   = $images[0];
	// Hero + images 1..6 → 6 thumbs, no duplication of the hero.
	$thumbs = array_slice( $images, 1, 6 );
	?>
	<div class="ibv-gallery" id="<?php echo esc_attr( $uid ); ?>"<?php echo $multiples ? ' data-images="' . $escaped_json . '"' : ''; ?>>
		<div class="ibv-gallery__header">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'Gallery', 'ibv' ),
					'level' => 'h2',
				]
			);
			?>
			<?php if ( $multiples ) : ?>
				<?php
				ibv_core_button(
					[
						'tag'        => 'button',
						'type'       => 'button',
						'label'      => __( 'View all photos', 'ibv' ),
						'variant'    => 'primary',
						'size'       => 'medium',
						'class'      => 'ibv-gallery__view-all',
						'attributes' => [
							'data-ibv-gallery-show' => '0',
						],
					]
				);
				?>
			<?php endif; ?>
		</div>

		<?php if ( $multiples ) : ?>
			<div class="ibv-gallery__main">
				<?php
				// `large` is uncropped — the on-page wide crop is applied
				// via aspect-ratio + object-fit in CSS. `ibv-hero` would
				// hard-crop to 16:9 before we cover-crop to 3:2 → double crop.
				ibv_core_image(
					$hero['id'],
					'large',
					[
						'class'    => 'ibv-gallery__main-image',
						'loading'  => 'eager',
						'decoding' => 'async',
					]
				);
				?>
				<button type="button" class="ibv-gallery__nav ibv-gallery__nav--prev" data-ibv-gallery-prev aria-label="<?php esc_attr_e( 'Previous photo', 'ibv' ); ?>">
					<?php ibv_core_the_icon( 'chevron-left', [ 'size' => 20 ] ); ?>
				</button>
				<button type="button" class="ibv-gallery__nav ibv-gallery__nav--next" data-ibv-gallery-next aria-label="<?php esc_attr_e( 'Next photo', 'ibv' ); ?>">
					<?php ibv_core_the_icon( 'chevron-right', [ 'size' => 20 ] ); ?>
				</button>
			</div>
		<?php else : ?>
			<div class="ibv-gallery__main ibv-gallery__main--static">
				<?php
				ibv_core_image(
					$hero['id'],
					'large',
					[
						'class'    => 'ibv-gallery__main-image',
						'loading'  => 'eager',
						'decoding' => 'async',
					]
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( count( $thumbs ) ) : ?>
			<ul class="ibv-gallery__thumbs">
				<?php foreach ( $thumbs as $i => $item ) : ?>
					<?php $global_index = $i + 1; ?>
					<li class="ibv-gallery__thumb-item">
						<button type="button" class="ibv-gallery__thumb" data-ibv-gallery-show="<?php echo esc_attr( (string) $global_index ); ?>">
							<?php
							ibv_core_image(
								$item['id'],
								'medium',
								[
									'class'    => 'ibv-gallery__thumb-image',
									'loading'  => 'lazy',
									'decoding' => 'async',
								]
							);
							?>
							<span class="ibv-u-visually-hidden"><?php esc_html_e( 'Show photo', 'ibv' ); ?></span>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php
}
