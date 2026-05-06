<?php
/**
 * Section: About — FAQ accordion.
 *
 * Native <details> / <summary> per item. Multi-open by default;
 * no JavaScript.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_about_faq() {
	$items = get_field( 'about_faq_items' );
	if ( ! is_array( $items ) || ! count( $items ) ) {
		return;
	}

	$eyebrow = (string) get_field( 'about_faq_eyebrow' );
	$title   = (string) get_field( 'about_faq_title' );

	wp_enqueue_style( 'ibv-section-about-faq' );
	?>
	<section class="ibv-section-about-faq ibv-section ibv-section--surface-white">
		<div class="ibv-container ibv-section-about-faq__inner">
			<header class="ibv-section-about-faq__header">
				<?php if ( $eyebrow ) : ?>
					<p class="ibv-section-about-faq__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="ibv-section-about-faq__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
			</header>

			<ol class="ibv-section-about-faq__list">
				<?php
				$i = 0;
				foreach ( $items as $item ) :
					$question = (string) ( $item['question'] ?? '' );
					$answer   = (string) ( $item['answer'] ?? '' );
					if ( ! $question || ! $answer ) {
						continue;
					}
					++$i;
					$num = str_pad( (string) $i, 2, '0', STR_PAD_LEFT );
					?>
					<li class="ibv-section-about-faq__item">
						<details class="ibv-section-about-faq__details">
							<summary class="ibv-section-about-faq__summary">
								<span class="ibv-section-about-faq__icon" aria-hidden="true"></span>
								<span class="ibv-section-about-faq__question"><?php echo esc_html( $question ); ?></span>
								<span class="ibv-section-about-faq__num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
							</summary>
							<div class="ibv-section-about-faq__answer">
								<?php echo wp_kses_post( $answer ); ?>
							</div>
						</details>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>
	<?php
}
