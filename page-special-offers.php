<?php
/**
 * Template Name: Special Offers
 */
get_header(); ?>

<?php global $spanish_law; ?>

<style>
.nomds-imag-padds{padding-bottom:40px}.villa-image-container img{width:-webkit-fill-available;width:100%;height:100%;object-fit:cover;border:0;max-width:100%;-ms-interpolation-mode:bicubic}h5{line-height:0.9}.villa-card{display:flex;flex-wrap:wrap;border:1px solid #ddd;border-radius:10px;overflow:hidden;margin-bottom:40px;transition:transform 0.3s;flex-direction:column;margin:20px}.villa-card:hover{transform:translateY(-5px)}.villa-image-container{width:100%;display:flex;justify-content:center;align-items:center}.villa-image{width:100%;height:auto;object-fit:cover}.villa-details{width:100%;padding:30px;display:flex;flex-direction:column;justify-content:space-between;position:relative}.villa-name{font-size:24px;font-weight:bold;margin-bottom:10px}.villa-location{font-size:18px;color:#666;margin-bottom:15px}.villa-price{font-size:20px;color:#e67e22;font-weight:bold;margin-bottom:10px}.villa-button{display:inline-block;padding:10px 20px;background-color:#3498db;color:white;text-align:center;border-radius:5px;text-decoration:none;transition:background-color 0.3s}.villa-button:hover{background-color:#2980b9}.slider-location-sleeps{margin-bottom:0}.button-container{position:sticky;bottom:0;left:0;right:0}.full-width-button{width:100%;justify-content:center;background-color:#2DB999;color:white;padding:10px;text-align:center;border-radius:5px}.full-width-button a{display:block;width:100%;text-decoration:none;color:white}.full-width-button:hover{background-color:#2DB999}h3 a{font-size:24px}.villa-image-container,.villa-details{width:100%}.offer-price{font-size:150%;color:#ffc029;font-weight:800}.offer-guarantee{color:#16a664}.offer-guarantee i{color:#16a664}@media (min-width:768px){.villa-card{flex-direction:row}.villa-image-container{width:40%;object-fit:env();width:40%;height:100%px;overflow:hidden}.villa-details{width:60%}}@media (min-width:992px){.villa-name{font-size:28px}.villa-details{padding:40px;width:40%}.villa-image-container{width:60%}}@media (min-width:1200px){.villa-name{font-size:30px}.villa-details{padding:50px}h3 a{font-size:30px}}
</style>

<?php
function get_user_location_ipinfo() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $access_token = '6c7b55ae21eb0a'; // Your ipinfo.io token

    $location = @file_get_contents("http://ipinfo.io/{$ip}?token={$access_token}");
    if ($location === FALSE) {
        return 'GB'; // Default to GB if API fails
    }

    $location = json_decode($location, true);

    return $location['country'] ?? 'GB'; // Returns country code, e.g., 'FR' for France, default to GB
}

function get_exchange_rate_exchangerate_api() {
    $api_key = 'f92b859be0d9c3cac8625899'; // Your exchangerate-api.com key
    $response = @file_get_contents("https://v6.exchangerate-api.com/v6/{$api_key}/latest/GBP");
    if ($response === FALSE) {
        return null; // Return null if API fails
    }

    $data = json_decode($response, true);

    return $data['conversion_rates']['EUR'] ?? null; // Returns conversion rate from GBP to EUR, null if not available
}

// Détection de localisation et taux de change
$user_country = get_user_location_ipinfo();
$exchange_rate = ($user_country != 'GB') ? get_exchange_rate_exchangerate_api() : null;

// Initialisation
global $current_user;
get_currentuserinfo();
$offers_new = [];
?>

<div class="site-content">
    <div class="row">
        <div class="large-12 columns">
            <?php if (have_rows('offer_table')) : ?>
                <?php while (have_rows('offer_table')) : the_row(); ?>
                    <?php
                    $post_object = get_sub_field('villa');
                    if ($post_object):
                        $post = $post_object;
                        setup_postdata($post);

                        // Initialisation des données de villa
                        $villaname = get_field('villa_pretty_name');
                        if (!isset($offers_new[$villaname])) {
                            $offers_new[$villaname] = [
                                'name' => $villaname,
                                'offers' => [],
                                'sleeps' => '',
                                'link' => '',
                                'image' => '',
                            ];
                        }

                        // Extraction des données de villa
                        $offers_new[$villaname]['link'] = get_permalink();
                        $offers_new[$villaname]['image'] = get_the_post_thumbnail();
                        $property_sleeps = get_field('property_sleeps');
                        $offers_new[$villaname]['sleeps'] = $property_sleeps ? 'Sleeps ' . $property_sleeps : 'N/A';

                        // Offres
                        $num = count($offers_new[$villaname]['offers']);
                        $offers_new[$villaname]['offers'][$num] = [
                            'dates' => get_sub_field('dates'),
                            'price' => get_sub_field('special_offer_price'),
                        ];

                        wp_reset_postdata();
                    endif;
                    ?>
                <?php endwhile; ?>
            <?php endif; ?>

            <!-- Affichage des villas -->
            <div class="row">
                <?php foreach ($offers_new as $offer_villa): ?>
                    <div class="villa-card flex flex-wrap bg-white rounded-lg shadow-lg overflow-hidden mb-4">
                        <div class="villa-image-container">
                            <?php echo $offer_villa['image']; ?>
                        </div>

                        <div class="villa-details p-4 flex flex-col justify-between">
                            <div>
                                <div style="margin-bottom:-10px" class="slider-location-sleeps">
                                    <?php echo '<p>' . $offer_villa['sleeps'] . '</p>'; ?>
                                </div>
                                <div class="villa-name text-2xl font-bold">
                                    <?php echo '<h3><a href="' . $offer_villa['link'] . '" title="' . $offer_villa['name'] . '">' . $offer_villa['name'] . '</a></h3>'; ?>
                                </div>
                                <div class="mt-2 text-sm offer-guarantee">
                                    <i class="fas fa-check-circle"></i> Best price guarantee
                                </div>

                                <hr>

                                <div>
                                    <?php if (!empty($offer_villa['offers']) && is_array($offer_villa['offers'])): ?>
                                        <h4>Dates on offer:</h4>
                                        <?php foreach ($offer_villa['offers'] as $offer_date): ?>
                                            <?php
                                            $price_in_gbp = $offer_date['price'];
                                            $price_to_display = ($exchange_rate !== null) 
                                                ? '€' . round($price_in_gbp * $exchange_rate)
                                                : '£' . $price_in_gbp;
                                            ?>
                                            <h5>
                                                <?php echo $offer_date['dates'] . ' from just '; ?>
                                                <span class="offer-price">
                                                    <?php echo $price_to_display; ?>
                                                </span>
                                            </h5>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <hr>
                            </div>

                            <div class="button-container mt-4">
                                <a href="<?php echo $offer_villa['link']; ?>" class="full-width-button">Enquire now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>