<?php
/**
 * Component: Property gallery (horizontal images + lightbox).
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
		$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
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
		$images[] = [
			'id'    => $id,
			'full'  => $full[0],
			'thumb' => ! empty( $thumb[0] ) ? $thumb[0] : $full[0],
			'alt'   => $alt,
		];
	}

	if ( ! count( $images ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-gallery' );

	$uid         = wp_unique_id( 'ibv-gallery-' );
	$dialog_id   = $uid . '-dialog';
	$multiples   = count( $images ) > 1;
	$escaped_json = esc_attr( wp_json_encode( $images ) );

	if ( $multiples ) {
		wp_enqueue_script( 'ibv-gallery-script' );

		$inline = 'document.addEventListener("DOMContentLoaded",function(){var root=document.getElementById("' . esc_js( $uid ) . '");if(!root)return;var data=JSON.parse(root.getAttribute("data-images"));var dlg=document.getElementById("' . esc_js( $dialog_id ) . '");if(!dlg)return;var img=dlg.querySelector(".ibv-gallery__lightbox-img");if(!img)return;var ix=0;function show(i){ix=(i+data.length)%data.length;var cur=data[ix];img.src=cur.full;img.alt=cur.alt||"";}function openAt(i){show(i);if(typeof dlg.showModal==="function"){dlg.showModal();}else{dlg.setAttribute("open","");}}root.querySelectorAll("[data-ibv-gallery-open]").forEach(function(btn){btn.addEventListener("click",function(){openAt(parseInt(btn.getAttribute("data-ibv-gallery-open"),10)||0);});});var prev=dlg.querySelector("[data-ibv-gallery-prev]");if(prev){prev.addEventListener("click",function(){show(ix-1);});}var next=dlg.querySelector("[data-ibv-gallery-next]");if(next){next.addEventListener("click",function(){show(ix+1);});}var close=dlg.querySelector("[data-ibv-gallery-close]");if(close){close.addEventListener("click",function(){if(typeof dlg.close==="function"){dlg.close();}else{dlg.removeAttribute("open");}});}});';

		wp_add_inline_script( 'ibv-gallery-script', $inline );
	}

	$hero   = $images[0];
	$thumbs = array_slice( $images, 1, 5 );
	?>
	<div class="ibv-gallery" id="<?php echo esc_attr( $uid ); ?>"<?php echo $multiples ? ' data-images="' . $escaped_json . '"' : ''; ?>>
		<div class="ibv-gallery__hero">
			<?php
			ibv_core_image(
				$hero['id'],
				'ibv-hero',
				[
					'class'    => 'ibv-gallery__hero-image',
					'loading'  => 'eager',
					'decoding' => 'async',
				]
			);
			?>
		</div>

		<?php if ( count( $images ) > 1 ) : ?>
			<ul class="ibv-gallery__thumbs">
				<?php foreach ( $thumbs as $i => $item ) : ?>
					<?php
					// Index in full $images: 1..5 when slice started at 1.
					$global_index = $i + 1;
					?>
					<li class="ibv-gallery__thumb-item">
						<button type="button" class="ibv-gallery__thumb" data-ibv-gallery-open="<?php echo esc_attr( (string) $global_index ); ?>">
							<?php
							ibv_core_image(
								$item['id'],
								'thumbnail',
								[
									'class'    => 'ibv-gallery__thumb-image',
									'loading'  => 'lazy',
									'decoding' => 'async',
								]
							);
							?>
							<span class="ibv-u-visually-hidden"><?php esc_html_e( 'Open photo', 'ibv' ); ?></span>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $multiples ) : ?>
			<?php
			ibv_core_button(
				[
					'tag'        => 'button',
					'type'       => 'button',
					'label'      => __( 'View all photos', 'ibv' ),
					'variant'    => 'secondary',
					'size'       => 'medium',
					'class'      => 'ibv-gallery__view-all',
					'attributes' => [
						'data-ibv-gallery-open' => '0',
					],
				]
			);
			?>
		<?php endif; ?>

		<?php if ( $multiples ) : ?>
			<dialog class="ibv-gallery__dialog" id="<?php echo esc_attr( $dialog_id ); ?>" aria-label="<?php esc_attr_e( 'Photo gallery', 'ibv' ); ?>">
				<div class="ibv-gallery__dialog-inner">
					<button type="button" class="ibv-gallery__close" data-ibv-gallery-close>
						<?php esc_html_e( 'Close', 'ibv' ); ?>
					</button>
					<img class="ibv-gallery__lightbox-img" src="" alt="">
					<div class="ibv-gallery__nav">
						<button type="button" class="ibv-gallery__nav-btn" data-ibv-gallery-prev><?php esc_html_e( 'Previous', 'ibv' ); ?></button>
						<button type="button" class="ibv-gallery__nav-btn" data-ibv-gallery-next><?php esc_html_e( 'Next', 'ibv' ); ?></button>
					</div>
				</div>
			</dialog>
		<?php endif; ?>
	</div>
	<?php
}
