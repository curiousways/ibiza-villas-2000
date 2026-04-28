<?php
/**
 * Template Name: Special Offers
 */
get_header();
?>

<style>
.nomds-imag-padds{padding-bottom:40px}.villa-image-container img{width:-webkit-fill-available;width:100%;height:100%;object-fit:cover;border:0;max-width:100%;-ms-interpolation-mode:bicubic}h5{line-height:0.9}.villa-card{display:flex;flex-wrap:wrap;border:1px solid #ddd;border-radius:10px;overflow:hidden;margin-bottom:40px;transition:transform 0.3s;flex-direction:column;margin:20px}.villa-card:hover{transform:translateY(-5px)}.villa-image-container{width:100%;display:flex;justify-content:center;align-items:center}.villa-image{width:100%;height:auto;object-fit:cover}.villa-details{width:100%;padding:30px;display:flex;flex-direction:column;justify-content:space-between;position:relative}.villa-name{font-size:24px;font-weight:bold;margin-bottom:15px}.villa-location{font-size:18px;color:#666;margin-bottom:15px}.villa-price{font-size:20px;color:#e67e22;font-weight:bold;margin-bottom:15px}.villa-button{display:inline-block;padding:10px 20px;background-color:#3498db;color:white;text-align:center;border-radius:5px;text-decoration:none;transition:background-color 0.3s}.villa-button:hover{background-color:#2980b9}.slider-location-sleeps{margin-bottom:0}.button-container{position:sticky;bottom:0;left:0;right:0}.full-width-button{width:100%;justify-content:center;background-color:#2DB999;color:white;padding:10px;text-align:center;border-radius:5px}.full-width-button a{display:block;width:100%;text-decoration:none;color:white}.full-width-button:hover{background-color:#2DB999}h3 a{font-size:24px}.villa-image-container,.villa-details{width:100%}.offer-price{font-size:150%;color:#ffc029;font-weight:800}.offer-guarantee{color:#16a664}.offer-guarantee i{color:#16a664}@media (min-width:768px){.villa-card{flex-direction:row}.villa-image-container{width:40%;object-fit:env();width:40%;height:100%px;overflow:hidden}.villa-details{width:60%}}@media (min-width:992px){.villa-name{font-size:28px}.villa-details{padding:40px;width:40%}.villa-image-container{width:60%}}@media (min-width:1200px){.villa-name{font-size:30px}.villa-details{padding:50px}h3 a{font-size:30px}}
</style>

<?php $offers_new = array(); ?>

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

                        $offers_new[$villaname]['link'] = get_permalink();
                        $offers_new[$villaname]['image'] = get_the_post_thumbnail();
                        $property_sleeps = get_field('property_sleeps');
                        $offers_new[$villaname]['sleeps'] = $property_sleeps ? 'Sleeps ' . $property_sleeps : 'N/A';

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

            <div class="row">
                <?php foreach ($offers_new as $offer_villa): ?>
                    <div class="villa-card flex flex-wrap bg-white rounded-lg shadow-lg overflow-hidden mb-4">
                        <div class="villa-image-container">
                            <?php echo $offer_villa['image']; ?>
                        </div>

                        <div class="villa-details p-4 flex flex-col justify-between">
                            <div>
                                <div style="margin-bottom:-10px" class="slider-location-sleeps">
                                    <?php echo '<p>' . esc_html( $offer_villa['sleeps'] ) . '</p>'; ?>
                                </div>
                                <div class="villa-name text-2xl font-bold">
                                    <?php echo '<h3><a href="' . esc_url( $offer_villa['link'] ) . '" title="' . esc_attr( $offer_villa['name'] ) . '">' . esc_html( $offer_villa['name'] ) . '</a></h3>'; ?>
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
                                            $price_val = isset($offer_date['price']) ? $offer_date['price'] : '';
                                            ?>
                                            <h5>
                                                <?php echo esc_html( $offer_date['dates'] ); ?> from just
                                                <span class="offer-price">
                                                    €<?php echo esc_html( $price_val ); ?>
                                                </span>
                                            </h5>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <hr>
                            </div>

                            <div class="button-container mt-4">
                                <a href="<?php echo esc_url( $offer_villa['link'] ); ?>" class="full-width-button">Enquire now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
