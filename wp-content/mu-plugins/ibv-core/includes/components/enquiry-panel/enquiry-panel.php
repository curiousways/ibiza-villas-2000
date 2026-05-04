<?php
/**
 * Component: Enquiry / RTB panel (Bob shell).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_enquiry_panel( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id || 'villas' !== get_post_type( $villa_id ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-enquiry-panel' );
	wp_enqueue_style( 'ibv-button' );

	$villa_slug   = (string) get_post_field( 'post_name', $villa_id );
	$property_id  = (string) get_field( 'property_id', $villa_id );
	$confirm_url  = ibv_get_booking_confirmation_url( $villa_slug );
	$endpoint_url = 'https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl';

	$prefill_from = '';
	$prefill_to   = '';
	$prefill_pax  = '';
	if ( isset( $_GET['date_from'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['date_from'] ) );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
			$prefill_from = $raw;
		}
	}
	if ( isset( $_GET['date_to'] ) ) {
		$raw = sanitize_text_field( wp_unslash( $_GET['date_to'] ) );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
			$prefill_to = $raw;
		}
	}
	if ( isset( $_GET['pax'] ) ) {
		$pax = absint( wp_unslash( $_GET['pax'] ) );
		if ( $pax > 0 ) {
			$prefill_pax = (string) $pax;
		}
	}
	?>
	<?php /* ─────────────────────────────────────────────────────────────
	       BOB API INTEGRATION SHELL — enquiry panel
	       ─────────────────────────────────────────────────────────────
	       Form fields:
	         - villa_id     (hidden, from current post)
	         - date_from    (required, YYYY-MM-DD)
	         - date_to      (required, YYYY-MM-DD)
	         - pax          (required, integer)
	         - message      (optional, text)

	       On date/pax change:
	         - Fetch in detail mode:
	           {endpoint}?villa={property_id}&date_from=...&date_to=...&pax=...
	         - Update [data-bob-total-eur], [data-bob-base-rental],
	           [data-bob-adw], [data-bob-cleaning]
	         - Enable/disable .ibv-enquiry-panel__submit based on dates+pax filled

	       On submit:
	         - TODO: POST to API enquiry endpoint when Steve confirms URL
	         - Currently: redirect to /booking-confirmation/?villa={slug}&...

	       Endpoint reference: https://ibizavillas2000.co.uk/cgi-bin/api/web_availability.pl
	       Spec: Notion → IBZ002 → API Integration Spec
	       ──────────────────────────────────────────────────────────── */ ?>

	<div
		class="ibv-enquiry-panel"
		data-bob-enquiry-panel
		data-villa-id="<?php echo esc_attr( (string) $villa_id ); ?>"
		data-bob-property-id="<?php echo esc_attr( $property_id ); ?>"
		data-bob-endpoint="<?php echo esc_url( $endpoint_url ); ?>"
		data-bob-confirm-url="<?php echo esc_url( $confirm_url ); ?>"
		data-bob-villa-slug="<?php echo esc_attr( $villa_slug ); ?>"
	>
		<h2 class="ibv-enquiry-panel__title"><?php esc_html_e( 'Enquire about this villa', 'ibv' ); ?></h2>

		<form class="ibv-enquiry-panel__form" method="get" action="<?php echo esc_url( $confirm_url ); ?>">
			<input type="hidden" name="villa" value="<?php echo esc_attr( $villa_slug ); ?>">
			<input type="hidden" name="villa_id" value="<?php echo esc_attr( (string) $villa_id ); ?>">

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-from"><?php esc_html_e( 'Arrive', 'ibv' ); ?></label>
				<input type="date" id="ibv-ep-from" name="date_from" value="<?php echo esc_attr( $prefill_from ); ?>" required>
			</div>

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-to"><?php esc_html_e( 'Depart', 'ibv' ); ?></label>
				<input type="date" id="ibv-ep-to" name="date_to" value="<?php echo esc_attr( $prefill_to ); ?>" required>
			</div>

			<div class="ibv-enquiry-panel__field">
				<label for="ibv-ep-pax"><?php esc_html_e( 'Guests', 'ibv' ); ?></label>
				<input type="number" id="ibv-ep-pax" name="pax" min="1" max="30" value="<?php echo esc_attr( $prefill_pax ); ?>" required>
			</div>

			<div class="ibv-enquiry-panel__field ibv-enquiry-panel__field--message">
				<label for="ibv-ep-message"><?php esc_html_e( 'Message (optional)', 'ibv' ); ?></label>
				<textarea id="ibv-ep-message" name="message" rows="3"></textarea>
			</div>

			<div class="ibv-enquiry-panel__price-block">
				<p class="ibv-enquiry-panel__price-label"><?php esc_html_e( 'Total price', 'ibv' ); ?></p>
				<p class="ibv-enquiry-panel__price-eur" data-bob-total-eur><?php echo esc_html( '—' ); ?></p>
				<ul class="ibv-enquiry-panel__breakdown">
					<li><span data-bob-base-rental><?php echo esc_html( '—' ); ?></span> <?php esc_html_e( 'base rental', 'ibv' ); ?></li>
					<li><span data-bob-adw><?php echo esc_html( '—' ); ?></span> <?php esc_html_e( 'ADW (damage waiver)', 'ibv' ); ?></li>
					<li><span data-bob-cleaning><?php echo esc_html( '—' ); ?></span> <?php esc_html_e( 'cleaning fee', 'ibv' ); ?></li>
				</ul>
				<p class="ibv-enquiry-panel__eco-note"><?php esc_html_e( 'Total does not include the government Eco Tax of €2.20 per person, per night, payable in resort.', 'ibv' ); ?></p>
			</div>

			<div class="ibv-enquiry-panel__error" data-bob-error hidden></div>

			<?php
			ibv_core_button(
				[
					'tag'         => 'button',
					'type'        => 'submit',
					'label'       => __( 'Request to book', 'ibv' ),
					'variant'     => 'primary',
					'size'        => 'large',
					'class'       => 'ibv-enquiry-panel__submit',
					'attributes'  => [
						'data-bob-submit' => '1',
						'disabled'        => 'disabled',
					],
				]
			);
			?>

			<p class="ibv-enquiry-panel__response-note"><?php esc_html_e( '✓ We respond within 20 minutes during our business hours', 'ibv' ); ?></p>
		</form>

		<hr class="ibv-enquiry-panel__divider">

		<div class="ibv-enquiry-panel__chat">
			<p class="ibv-enquiry-panel__chat-label"><?php esc_html_e( 'Prefer to chat?', 'ibv' ); ?></p>
			<?php
			$whatsapp = get_field( 'whatsapp_number', 'option' );
			if ( $whatsapp ) :
				$digits       = preg_replace( '/[^0-9]/', '', (string) $whatsapp );
				$whatsapp_url = $digits ? 'https://wa.me/' . $digits : '';
				if ( $whatsapp_url ) :
					?>
					<a class="ibv-enquiry-panel__chat-link" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'WhatsApp us', 'ibv' ); ?>
						<span class="ibv-enquiry-panel__chat-number"><?php echo esc_html( (string) $whatsapp ); ?></span>
					</a>
					<?php
				endif;
			endif;

			$phones = [
				[ 'label' => __( 'Call UK', 'ibv' ),     'value' => get_field( 'phone_uk', 'option' ) ],
				[ 'label' => __( 'Call Ibiza', 'ibv' ), 'value' => get_field( 'phone_ibiza', 'option' ) ],
			];
			foreach ( $phones as $phone ) :
				if ( empty( $phone['value'] ) ) {
					continue;
				}
				$tel_digits = preg_replace( '/[^0-9+]/', '', (string) $phone['value'] );
				if ( ! $tel_digits ) {
					continue;
				}
				?>
				<a class="ibv-enquiry-panel__chat-link" href="<?php echo esc_url( 'tel:' . $tel_digits ); ?>">
					<?php echo esc_html( $phone['label'] ); ?>
					<span class="ibv-enquiry-panel__chat-number"><?php echo esc_html( (string) $phone['value'] ); ?></span>
				</a>
				<?php
			endforeach;

			$email = get_field( 'contact_email', 'option' );
			if ( $email ) :
				$email_safe = sanitize_email( (string) $email );
				if ( $email_safe ) :
					?>
					<a class="ibv-enquiry-panel__chat-link" href="<?php echo esc_url( 'mailto:' . $email_safe ); ?>">
						<?php esc_html_e( 'Or send us an email', 'ibv' ); ?>
					</a>
					<?php
				endif;
			endif;
			?>
		</div>
	</div>

	<?php /* ─────────── END BOB SHELL ─────────── */ ?>
	<?php

	wp_enqueue_script( 'ibv-enquiry-panel' );
	$inline = <<<'JS'
(function () {
	function init(panel) {
		var endpoint   = panel.getAttribute('data-bob-endpoint') || '';
		var propertyId = panel.getAttribute('data-bob-property-id') || '';
		var confirmUrl = panel.getAttribute('data-bob-confirm-url') || '';
		var villaSlug  = panel.getAttribute('data-bob-villa-slug') || '';

		var form = panel.querySelector('.ibv-enquiry-panel__form');
		if (!form) return;

		var fromEl = form.querySelector('[name="date_from"]');
		var toEl   = form.querySelector('[name="date_to"]');
		var paxEl  = form.querySelector('[name="pax"]');
		var submitEl = panel.querySelector('[data-bob-submit]');

		var totalEl    = panel.querySelector('[data-bob-total-eur]');
		var rentalEl   = panel.querySelector('[data-bob-base-rental]');
		var adwEl      = panel.querySelector('[data-bob-adw]');
		var cleaningEl = panel.querySelector('[data-bob-cleaning]');

		var EUR = (typeof Intl !== 'undefined' && Intl.NumberFormat)
			? new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 })
			: { format: function (n) { return '€' + Math.round(n); } };

		function isValidDate(s) { return /^\d{4}-\d{2}-\d{2}$/.test(s || ''); }

		function readState() {
			return {
				date_from: fromEl ? fromEl.value : '',
				date_to:   toEl ? toEl.value : '',
				pax:       paxEl ? parseInt(paxEl.value, 10) || 0 : 0
			};
		}

		function gateReady(s) {
			return isValidDate(s.date_from) && isValidDate(s.date_to) && s.pax > 0;
		}

		function updateGate() {
			if (!submitEl) return;
			if (gateReady(readState())) {
				submitEl.removeAttribute('disabled');
			} else {
				submitEl.setAttribute('disabled', 'disabled');
			}
		}

		function setText(el, txt) { if (el) el.textContent = txt; }

		function resetPrices() {
			setText(totalEl, '—');
			setText(rentalEl, '—');
			setText(adwEl, '—');
			setText(cleaningEl, '—');
		}

		function pickNumber(obj, keys) {
			for (var i = 0; i < keys.length; i++) {
				var v = obj && obj[keys[i]];
				if (typeof v === 'number') return v;
				if (typeof v === 'string' && v !== '' && !isNaN(parseFloat(v))) return parseFloat(v);
			}
			return null;
		}

		function paint(data) {
			var node = (data && data.villa) ? data.villa : data;
			var total = pickNumber(node, ['total', 'total_price', 'total_eur', 'price_total']);
			var rent  = pickNumber(node, ['base_rental', 'rental', 'weekly_rate', 'rate']);
			var adw   = pickNumber(node, ['adw', 'damage_waiver']);
			var clean = pickNumber(node, ['cleaning', 'cleaning_fee']);

			setText(totalEl,    total !== null ? EUR.format(total) : '—');
			setText(rentalEl,   rent  !== null ? EUR.format(rent)  : '—');
			setText(adwEl,      adw   !== null ? EUR.format(adw)   : '—');
			setText(cleaningEl, clean !== null ? EUR.format(clean) : '—');
		}

		var inflight = null;
		function fetchPricing() {
			if (!endpoint || !propertyId) return;
			var s = readState();
			if (!gateReady(s)) { resetPrices(); return; }

			var url = endpoint
				+ '?villa='     + encodeURIComponent(propertyId)
				+ '&date_from=' + encodeURIComponent(s.date_from)
				+ '&date_to='   + encodeURIComponent(s.date_to)
				+ '&pax='       + encodeURIComponent(String(s.pax));

			if (inflight && typeof inflight.abort === 'function') {
				try { inflight.abort(); } catch (e) {}
			}
			var ctrl = (typeof AbortController !== 'undefined') ? new AbortController() : null;
			inflight = ctrl;

			fetch(url, { method: 'GET', credentials: 'omit', signal: ctrl ? ctrl.signal : undefined })
				.then(function (r) {
					if (!r.ok) throw new Error('HTTP ' + r.status);
					return r.json();
				})
				.then(paint)
				.catch(function (err) {
					if (err && err.name === 'AbortError') return;
					console.warn('[ibv-enquiry-panel] pricing fetch failed', err);
					resetPrices();
				});
		}

		var debounceTimer = null;
		function schedule() {
			updateGate();
			if (debounceTimer) clearTimeout(debounceTimer);
			debounceTimer = setTimeout(fetchPricing, 300);
		}

		[fromEl, toEl, paxEl].forEach(function (el) {
			if (!el) return;
			el.addEventListener('change', schedule);
			el.addEventListener('input', schedule);
		});

		form.addEventListener('submit', function (ev) {
			ev.preventDefault();
			var s = readState();
			if (!gateReady(s)) { updateGate(); return; }
			// TODO: POST to enquiry endpoint when Steve confirms URL — for now redirect direct.
			var sep = confirmUrl.indexOf('?') === -1 ? '?' : '&';
			var target = confirmUrl
				+ (confirmUrl.indexOf('villa=') === -1 ? sep + 'villa=' + encodeURIComponent(villaSlug) : '')
				+ '&date_from=' + encodeURIComponent(s.date_from)
				+ '&date_to='   + encodeURIComponent(s.date_to)
				+ '&pax='       + encodeURIComponent(String(s.pax));
			window.location.assign(target);
		});

		updateGate();
		if (gateReady(readState())) {
			fetchPricing();
		}
	}

	function boot() {
		document.querySelectorAll('[data-bob-enquiry-panel]').forEach(init);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
}());
JS;
	wp_add_inline_script( 'ibv-enquiry-panel', $inline );
}
