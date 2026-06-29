<?php
/**
 * Component: Property gallery — static teaser + pop-up viewer (Figma node 1:6019).
 *
 * On-page teaser: serif "Gallery" heading + sage "View all photos" button +
 * wide-cropped hero + a horizontally scrollable thumb carousel holding every
 * image. The on-page crop is purely cosmetic — the hero and every thumb open
 * the pop-up, which is the real viewer.
 *
 * Pop-up viewer: image at source shape held at a fixed height (no jump between
 * portrait/landscape), prev/next chevrons, X close, an image counter
 * ("4 / 18"), Esc + arrow-key support. The lightbox image carries the `full`
 * srcset/sizes so it stays responsive instead of always loading the largest
 * file. Two approved deviations from the Figma: on-page chevrons are dropped
 * (no in-place paging — every tile opens the pop-up); the pop-up gains a
 * counter.
 *
 * Single-image villas render just the hero — no thumbs/button/dialog.
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
		$full = wp_get_attachment_image_src( $id, 'full' );
		if ( empty( $full[0] ) ) {
			continue;
		}
		$alt = '';
		if ( is_array( $row ) && ! empty( $row['alt'] ) ) {
			$alt = (string) $row['alt'];
		}
		if ( '' === $alt ) {
			$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		}
		// The on-page hero + thumbs are server-rendered (responsive via
		// ibv_core_image), so the JSON only feeds the pop-up viewer. Carry the
		// source-shape `full` image plus its srcset/sizes so the lightbox stays
		// responsive instead of always loading the largest file.
		$srcset = wp_get_attachment_image_srcset( $id, 'full' );
		$sizes  = wp_get_attachment_image_sizes( $id, 'full' );
		$images[] = [
			'id'     => $id,
			'full'   => $full[0],
			'srcset' => is_string( $srcset ) ? $srcset : '',
			'sizes'  => is_string( $sizes ) ? $sizes : '',
			'alt'    => $alt,
		];
	}

	if ( ! count( $images ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-gallery' );

	$uid          = wp_unique_id( 'ibv-gallery-' );
	$dialog_id    = $uid . '-dialog';
	$multiples    = count( $images ) > 1;
	$escaped_json = esc_attr( wp_json_encode( $images ) );

	if ( $multiples ) {
		wp_enqueue_script( 'ibv-gallery-script' );

		$inline = sprintf(
			'document.addEventListener("DOMContentLoaded",function(){' .
				'var root=document.getElementById(%1$s);if(!root)return;' .
				'var data=JSON.parse(root.getAttribute("data-images"));' .
				'var dlg=document.getElementById(%2$s);if(!dlg)return;' .
				'var img=dlg.querySelector(".ibv-gallery__lightbox-img");' .
				'var counter=dlg.querySelector(".ibv-gallery__counter");' .
				'var ix=0;' .
				'function show(i){ix=(i+data.length)%%data.length;var cur=data[ix];' .
					'if(img){if(cur.srcset){img.srcset=cur.srcset;img.sizes=cur.sizes||"";}else{img.removeAttribute("srcset");img.removeAttribute("sizes");}img.src=cur.full;img.alt=cur.alt||"";}' .
					'if(counter){counter.textContent=(ix+1)+" / "+data.length;}}' .
				'function open(i){show(i);if(typeof dlg.showModal==="function"){dlg.showModal();}else{dlg.setAttribute("open","");}}' .
				'root.querySelectorAll("[data-ibv-gallery-open]").forEach(function(btn){btn.addEventListener("click",function(){open(parseInt(btn.getAttribute("data-ibv-gallery-open"),10)||0);});});' .
				'var prev=dlg.querySelector("[data-ibv-gallery-prev]");if(prev){prev.addEventListener("click",function(){show(ix-1);});}' .
				'var next=dlg.querySelector("[data-ibv-gallery-next]");if(next){next.addEventListener("click",function(){show(ix+1);});}' .
				'var close=dlg.querySelector("[data-ibv-gallery-close]");if(close){close.addEventListener("click",function(){if(typeof dlg.close==="function"){dlg.close();}else{dlg.removeAttribute("open");}});}' .
				'dlg.addEventListener("keydown",function(e){if(e.key==="ArrowRight"){show(ix+1);}else if(e.key==="ArrowLeft"){show(ix-1);}});' .
			'});',
			wp_json_encode( $uid ),
			wp_json_encode( $dialog_id )
		);

		wp_add_inline_script( 'ibv-gallery-script', $inline );
	}

	$hero = $images[0];
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
							'data-ibv-gallery-open' => '0',
						],
					]
				);
				?>
			<?php endif; ?>
		</div>

		<?php if ( $multiples ) : ?>
			<button type="button" class="ibv-gallery__main" data-ibv-gallery-open="0" aria-label="<?php esc_attr_e( 'Open photo gallery', 'ibv' ); ?>">
				<?php
				// `large` is uncropped — the on-page wide crop is applied via
				// aspect-ratio + object-fit in CSS. The pop-up shows source
				// shape regardless, so the on-page crop is cosmetic.
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
			</button>
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

		<?php if ( $multiples ) : ?>
			<div class="ibv-gallery__thumbs">
				<?php foreach ( $images as $i => $item ) : ?>
					<div class="ibv-gallery__thumb-item">
						<button type="button" class="ibv-gallery__thumb" data-ibv-gallery-open="<?php echo esc_attr( (string) $i ); ?>">
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
							<span class="ibv-u-visually-hidden"><?php esc_html_e( 'Open photo', 'ibv' ); ?></span>
						</button>
					</div>
				<?php endforeach; ?>
			</div>

			<dialog class="ibv-gallery__dialog" id="<?php echo esc_attr( $dialog_id ); ?>" aria-label="<?php esc_attr_e( 'Photo gallery', 'ibv' ); ?>">
				<div class="ibv-gallery__dialog-inner">
					<button type="button" class="ibv-gallery__close" data-ibv-gallery-close aria-label="<?php esc_attr_e( 'Close', 'ibv' ); ?>">
						<?php ibv_core_the_icon( 'x', [ 'size' => 24 ] ); ?>
					</button>
					<button type="button" class="ibv-gallery__lb-arrow ibv-gallery__lb-arrow--prev" data-ibv-gallery-prev aria-label="<?php esc_attr_e( 'Previous photo', 'ibv' ); ?>">
						<?php ibv_core_the_icon( 'chevron-left', [ 'size' => 28 ] ); ?>
					</button>
					<img class="ibv-gallery__lightbox-img" src="" alt="" decoding="async">
					<button type="button" class="ibv-gallery__lb-arrow ibv-gallery__lb-arrow--next" data-ibv-gallery-next aria-label="<?php esc_attr_e( 'Next photo', 'ibv' ); ?>">
						<?php ibv_core_the_icon( 'chevron-right', [ 'size' => 28 ] ); ?>
					</button>
					<p class="ibv-gallery__counter" aria-live="polite"></p>
				</div>
			</dialog>
		<?php endif; ?>
	</div>
	<?php
}
