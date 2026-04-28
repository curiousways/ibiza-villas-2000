<?php

define('THEME_DIR', get_stylesheet_directory_uri());

add_action('after_setup_theme', 'core_theme_setup');
function core_theme_setup()
{
	require_once(get_template_directory() . '/functions/widgets.php');
	require_once(get_template_directory() . '/functions/acf-options.php');
	require_once(get_template_directory() . '/functions/properties-cpt.php');
	require_once(get_template_directory() . '/functions/related-properties-widget.php');
	require_once(get_template_directory() . '/functions/featured-properties-widget.php');
	require_once(get_template_directory() . '/functions/custom-admin-columns.php');
	require_once(get_template_directory() . '/inc/image-dimensions.php');
}

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
add_image_size('iv2000_1024x685', 1024, 685, true);
add_image_size('iv2000_420x280', 420, 280, true);
add_image_size('iv2000_840x560', 840, 560, true);

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
		wp_enqueue_script('jquery-migrate', 'https://code.jquery.com/jquery-migrate-3.4.1.min.js', ['jquery'], '3.4.1', true);
		if (file_exists(get_template_directory() . '/js/rude-app.js')) {
			wp_enqueue_script('rude-app', JS . '/rude-app.js', ['jquery'], null, true);
		}
		if (file_exists(get_template_directory() . '/css/new-style.css')) {
			wp_enqueue_style('style-new', CSS . 'css/new-style.css', [], null);
		}
	}
}

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


// REMOVE MENU ITEMS FROM ADMIN

function remove_menu_items()
{
	if (!current_user_can('administrator')):
		remove_menu_page('tools.php');
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

	if ($wp_query->max_num_pages <= 1)
		return;

	$paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
	$max   = intval($wp_query->max_num_pages);

	if ($paged >= 1)
		$links[] = $paged;

	if ($paged >= 3) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if (($paged + 2) <= $max) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="pagination"><ul>' . "\n";

	if (get_previous_posts_link())
		printf('<li>%s</li>' . "\n", get_previous_posts_link('Previous'));

	if (!in_array(1, $links)) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

		if (!in_array(2, $links))
			echo '<li>…</li>';
	}

	sort($links);
	foreach ((array)$links as $link) {
		$class = $paged == $link ? ' class="active"' : '';
		printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
	}

	if (!in_array($max, $links)) {
		if (!in_array($max - 1, $links))
			echo '<li>…</li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
	}

	if (get_next_posts_link())
		printf('<li>%s</li>' . "\n", get_next_posts_link('Next'));

	echo '</ul></div>' . "\n";
}


// get the the role object
$role_object = get_role('editor');
$role_object->add_cap('edit_theme_options');


/**
 * Add custom taxonomies
 */
function add_custom_villa_taxonomies()
{
	register_taxonomy('villa_type', 'villas', array(
		'hierarchical' => true,
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
		'rewrite' => array(
			'slug' => 'villatype',
			'with_front' => false,
			'hierarchical' => true
		),
	));
}
add_action('init', 'add_custom_villa_taxonomies', 0);
