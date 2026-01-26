/*
Template Name: All Villas 2
*/

<?php get_header(); ?>

<style>
.widget_wpc_filters_widget{display:flex;justify-content:center;align-items:center;background-color:#ffffff;border-radius:50px;box-shadow:0 4px 8px rgba(0,0,0,0.1);padding:15px 20px;margin:20px auto;max-width:1200px}.date-picker{margin:0 10px;text-align:center}.date-picker label{font-size:14px;color:#333;margin-bottom:5px;display:block;text-align:center}.search-button{background-color:#ff5670;color:#fff;border:none;border-radius:50px;padding:10px 20px;font-size:16px;cursor:pointer;transition:background-color 0.3s ease}.search-button:hover{background-color:#ff4560}.date-form{display:flex;justify-content:center;align-items:center;margin:20px 0}.no-results{text-align:center;color:#666;font-size:18px;margin:40px 0}.property-grid{list-style:none;padding:0;margin:0}
</style>

<!-- Formulaire Check-in / Check-out -->
<form method="GET" action="" class="date-form">
    <div class="date-picker">
        <label for="check_in">Check-in</label>
        <input type="date" id="check_in" name="check_in" value="<?php echo isset($_GET['check_in']) ? esc_attr($_GET['check_in']) : ''; ?>">
    </div>
    <div class="date-picker">
        <label for="check_out">Check-out</label>
        <input type="date" id="check_out" name="check_out" value="<?php echo isset($_GET['check_out']) ? esc_attr($_GET['check_out']) : ''; ?>">
    </div>
    <button type="submit" class="search-button">Search</button>
</form>

<!-- Widget des filtres -->
<?php echo do_shortcode('[fe_widget]'); ?>

<div class="site-content">
    <div class="row">
        <div class="small-12 columns">
            <h1><?php the_title(); ?></h1>
        </div>

        <?php
        // Connexion à la base de données des disponibilités
        $db_host = 'localhost:3306';
        $db_name = 'iv2000_live';
        $db_user = 'iv2k';
        $db_password = '1v2kLIVE';

        // Récupération des dates Check-in et Check-out
        $check_in = isset($_GET['check_in']) ? esc_attr($_GET['check_in']) : null;
        $check_out = isset($_GET['check_out']) ? esc_attr($_GET['check_out']) : null;

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Initialisation des villas disponibles
            $available_villas = [];

            if ($check_in && $check_out) {
                // Vérification des disponibilités pour chaque villa
                $villa_names = [
                    'alberto', 'alexandra', 'antonio', 'bellevista', 'benimussa', 'blanca', 'carolle', 'cordonna',
                    'cox', 'cristo', 'daniel', 'elizabeth', 'emilio', 'garcia', 'jermanji', 'jordi', 'juanito', 
                    'km2', 'lad', 'maria', 'marieta', 'marin', 'maymo', 'miguel', 'luis', 'nieves', 'oxum', 'patxi', 
                    'pedriki', 'peppe', 'porchada', 'primavera', 'rafa', 'reiet', 'rikh', 'rose', 'rosie', 'royo',
                    'sacarroca', 'salada', 'savi', 'ahlvar', 'silvia', 'sofia', 'tom', 'toni', 'tunicu', 'vincent',
                    'sunset2b', 'sunset2c', 'sunset3b', 'sunset4c', 'coastline', 'mint', 'babalia', 'louisa', 
                    'brisol', 'victoria', 'paquita', 'rosello', 'payes', 'carinosa', 'llonga', 'delpinar', 'sol', 
                    'parron', 'saroca', 'paco', 'olivos', 'buenas', 'ania', 'alexa', 'torres', 'galop', 'tinto', 
                    'mosan', 'david', 'caleta', 'josie', 'carlos', 'oasis', 'felix', 'pallazo', 'marc', 'cortes', 
                    'savinas', 'jesus', 'evie', 'bali', 'chiara', 'tegui', 'stella', 'george', 'angel', 'luna', 
                    'xocolata', 'reyadesol', 'sacaleta', 'mestre', 'bruno', 'roig', 'zouzou', 'lacabana', 'martha', 
                    'repaire'
                ];

                foreach ($villa_names as $villa_name) {
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) AS available_days
                        FROM `2025`
                        WHERE `$villa_name` IS NULL
                        AND `date` BETWEEN :check_in AND :check_out
                    ");
                    $stmt->execute([
                        ':check_in' => $check_in,
                        ':check_out' => $check_out,
                    ]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Si toutes les dates sont disponibles, ajouter le nom de la villa
                    $days_requested = (new DateTime($check_out))->diff(new DateTime($check_in))->days + 1;
                    if ($result['available_days'] == $days_requested) {
                        $available_villas[] = $villa_name;
                    }
                }
            }

            // Préparation des arguments pour WP_Query
            $args = [
                'post_type' => 'villas',
                'posts_per_page' => -1,
            ];

            if (!empty($available_villas)) {
                $args['meta_query'] = [
                    [
                        'key' => 'property_id',
                        'value' => $available_villas,
                        'compare' => 'IN',
                    ]
                ];
            } else {
                $args['post__in'] = [0]; // Aucun résultat
            }

            // Exécution de la requête WordPress
            $property_query = new WP_Query($args);

            // Affichage des résultats
            if ($property_query->have_posts()) : ?>
                <ul class="property-grid">
                    <?php while ($property_query->have_posts()): $property_query->the_post(); ?>
                        <li id="property-<?php echo $post->ID; ?>" class="small-12 medium-6 large-4 columns">
                            <?php get_template_part('templates/loop-grid-part'); ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else : ?>
                <p class="no-results">Désolé, aucune villa n'est disponible pour les dates sélectionnées.</p>
            <?php endif; ?>

        <?php
        } catch (PDOException $e) {
            echo '<p class="no-results">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>