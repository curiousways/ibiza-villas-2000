<?php
if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'General Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Explore Destinations Settings',
		'menu_title'	=> 'Explore Destinations',
		'parent_slug'	=> 'theme-general-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Global Villa Settings',
		'menu_title'	=> 'Global Villa Settings',
		'parent_slug'	=> 'theme-general-settings',
	));

	
}