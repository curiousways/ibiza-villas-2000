<?php

/* Add shortcode filter to widgets */

define( 'SHORTCODE_PRIORITY', 11 );
if (!is_admin())
		add_filter('widget_text', 'do_shortcode', SHORTCODE_PRIORITY);


/* Register widget areas */
// You can change the widget area names in here but if you change the id I believe it kicks all the widgets back out of an active sidebar so you'll have to redrag them in
// Try to keep the names unique and its likely more will be added so name appropriately with -1 ect if its likely there will become more

if ( !function_exists( 'register_widgets' ) ) :

function register_widgets(){
	if (function_exists('register_sidebar')) {
		register_sidebar( array(
			'name'=> __('Primary Sidebar'),
			'id' => 'primary-sidebar',
			'description' => __('The primary sidebar.'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar( array(
			'name' => __( 'Villa Sidebar 1'),
			'id' => 'villa-sidebar-1',
			'description' => __( 'Villa Sidebar 1'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
		register_sidebar( array(
			'name'=> __('Footer Widget Area 1'),
			'id' => 'footer-widget-1',
			'class' => 'footer-widget-1',
			'description' => __('Footer Widget 1'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar( array(
			'name'=> __('Footer Widget Area 2'),
			'id' => 'footer-widget-2',
			'class' => 'footer-widget-2',
			'description' => __('Footer Widget 2'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar( array(
			'name'=> __('Footer Widget Area 3'),
			'id' => 'footer-widget-3',
			'class' => 'footer-widget-1',
			'description' => __('Footer Widget 1'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
	}
}

endif;
add_action( 'widgets_init', 'register_widgets' );