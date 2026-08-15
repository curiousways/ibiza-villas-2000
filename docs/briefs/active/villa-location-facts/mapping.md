# Property Location → tags / facts / licence — mapping

Drafted 2026-08-15 from the live `property_summary` ("Property Location")
WYSIWYG bullets on the 12 published villas. Applied by `apply-mapping.php`;
full pre-migration HTML for **every** villa (any status) is in `backups/`.

Conventions applied:

- **Tags** (`villa_distances`, map-pin pills under the map, first also in
  the villa header): capped at 5, normalised to "{n} mins drive/walk to X".
  Travel mode kept — a 5-minute walk and a 5-minute drive are different
  facts. "Playa den Bossa" normalised to "Playa d'en Bossa".
- **Facts** (`property_features`, ✓ chips under Villa Overview): practical
  notes — car advice, WiFi, air conditioning, security.
- **Licence** (`villa_rental_licence`, small print under the tags): the
  licence code only; the parenthesised house names ("Can Nebot") were
  admin breadcrumbs, not visitor content.
- **Dropped**: lines already covered by a kept tag, and public-transport
  walk times (weak signal next to café/supermarket walkability; can be
  reinstated per villa if the team disagrees).

## villa-alexa (3174) — licence ETV2345E

- Tags: 10 mins walk to San Josep · 5 mins drive to San Antonio · 5–10 mins drive to beaches · 15 mins drive to Ibiza Town · 10 mins walk to shops and bars
- Facts: Car recommended
- Dropped: "10 min drive to Playa den Bossa" (6th tag), "10 min walk to Supermarket" (covered by shops line), "10 min walk to Public transport"

## villa-bella-vista (3186) — licence ET1048E

- Tags: 10 mins drive to Playa d'en Bossa · 12 mins drive to beaches · 15 mins drive to Ibiza Town · 8 mins drive to supermarket and bars · 20 mins drive to San Antonio
- Facts: Car necessary
- Dropped: none (supermarket + bar lines merged into one tag)

## villa-tunicu (3322) — licence ET0514E

- Tags: 5 mins drive to San Antonio · 5 mins drive to beaches · 20 mins drive to Ibiza Town · 5 mins drive to supermarket · 15 mins walk to cafés and restaurants
- Facts: Car recommended
- Dropped: the "/ 35 min walk" alternates (kept the primary drive time)

## villa-can-vicente (3524) — licence ETV1697E

- Tags: 20 mins walk to San Antonio Bay · 20 mins walk to beach · 10 mins walk to restaurants and bars · 20 mins drive to Ibiza Town · 10 mins drive to Privilege and Amnesia
- Facts: Car recommended
- Dropped: "20 min walk to nearest supermarket", "10 min walk to Public Transport"

## villa-daniel (2782) — licence ET0405E

- Tags: 5 mins drive to Playa d'en Bossa · 5 mins drive to beach · 7 mins drive to Ibiza Town · 7 mins walk to cafés and bars · 7 mins walk to supermarket
- Facts: Car not necessary
- Dropped: "15 min drive to San Antonio", "7 min walk to Public Transport"

## villa-km2 (4067) — licence ET0326E

- Tags: 5 mins drive to Ibiza Town · 5 mins drive to Playa d'en Bossa · 10 mins drive to Cala Jondal and Salinas beaches · 5 mins walk to cafés and bars · 12 mins walk to supermarket
- Facts: Car recommended
- Dropped: "15 min drive to San Antonio", "7 min walk to nearest restaurant" (covered by cafés/bars), "5 min walk to Public transport"

## villa-nieves (2818) — licence ET0537E

- Tags: 5 mins drive to San Rafael · 10 mins drive to San Antonio · 10 mins drive to Ibiza Town · 10 mins drive to beaches · 5 mins drive to supermarket
- Facts: Car recommended
- Dropped: "5 min drive to Public Transport" ("San Rafel" typo fixed)

## villa-pep-luis-can-pep-mortera (3155) — licence VTV0225EIF

- Tags: 5 mins drive to Playa d'en Bossa beach · 7 mins drive to Ibiza Town · 10 mins drive to Sa Caleta and Cala Jondal · 5 mins drive to supermarket · 15 mins drive to San Antonio
- Facts: Car recommended
- Dropped: duplicate "5 min drive to Playa Den Bossa" (town + beach lines merged)

## villa-savines (6998) — licence ETV2564E

- Tags: 3 mins drive to Ibiza Town · 2 mins to Talamanca Beach · 5 mins drive to Playa d'en Bossa · 5 mins walk to supermarket and cafés · 2 mins to Pacha
- Facts: Air conditioning throughout · WiFi · Security system inside and out
- Dropped: "15 minutes to Sn Antonio" (typo'd, 6th tag)

## villa-tegui (9082) — licence ET0808E

- Tags: 5 mins drive to San Rafael · 15 mins drive to Ibiza Town · 15 mins drive to Playa d'en Bossa · 15 mins drive to beaches · 15 mins drive to San Antonio
- Facts: Peaceful rural location · Car recommended · Good public transport links · WiFi · Air conditioning in the bedrooms
- Dropped: "25 mins walk into San Rafael" (drive tag kept)
- Note: replaced the existing test facts ("OK", "Another")

## villa-tom (2894) — licence ET0406E

- Tags: 5 mins drive to Playa d'en Bossa · 7 mins drive to Ibiza Town · 5–10 mins drive to beaches · 5 mins walk to bars and restaurants · 5 mins walk to supermarket
- Facts: Car not necessary
- Dropped: "15 min drive to San Antonio", "5 min walk to Public Transport" (beach list "Playa den Bossa, Salinas, Sa Caleta and Talamanca" condensed)

## villa-torres (4435) — licence 2016007184/ETV2079E

- Tags: 10 mins walk to Playa d'en Bossa · 7 mins walk to San Jordi · 15 mins walk to beaches · 5 mins drive to Ibiza Town · 5 mins walk to supermarket and bars
- Facts: Car not necessary
- Dropped: "15 min drive to San Antonio"
