<?php
	function OWN_Theme_Setup() {
		add_theme_support( 'post-thumbnails' );
		remove_theme_support( 'post-formats' );
		remove_theme_support( 'custom-background' );
		remove_theme_support( 'custom-header' );

		update_option( 'thumbnail_size_w', 128 );
		update_option( 'thumbnail_size_h', 128 );
		update_option( 'medium_size_w', 512 );
		update_option( 'medium_size_h', 512 );
		update_option( 'large_size_w', 1024 );
		update_option( 'large_size_h', 1024 );	
	}
	add_action( 'after_setup_theme', 'OWN_Theme_Setup' );

	function OWN_Theme_Styles() {
		wp_register_style( 'normalize', get_stylesheet_directory_uri() . '/css/normalize.min.css', array(), '1', 'all' );
		wp_register_style( 'mapbox', 'https://api.tiles.mapbox.com/mapbox.js/v2.1.5/mapbox.css', array(), '1', 'all' );
		wp_register_style( 'own-main-style', get_stylesheet_directory_uri() . '/css/main.css', array(), '1', 'all' );
		wp_register_style( 'own-desktop-style', get_stylesheet_directory_uri() . '/css/desktop.css', array(), '1', 'all' );
		
		wp_enqueue_style( 'normalize' );
		wp_enqueue_style( 'mapbox' );
		wp_enqueue_style( 'own-main-style' );
		wp_enqueue_style( 'own-desktop-style' );		
	}

	function OWN_Theme_Scripts() {
		wp_register_script( 'mapbox', 'https://api.tiles.mapbox.com/mapbox.js/v2.1.5/mapbox.js' );
		wp_register_script( 'own-main', get_stylesheet_directory_uri() . '/js/main.js' );

		$home_id = get_option( 'page_on_front' );

		$variables = array( 'map' => array(
			'center' => array(
				'lat' => get_field( 'map_center', $home_id )['lat'],
				'lng' => get_field( 'map_center', $home_id )['lng']
			),
			'zoom' => get_field( 'map_zoom_level', $home_id ) )
		);

		wp_localize_script( 'own-main', 'OWN', $variables );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'mapbox' );
		wp_enqueue_script( 'own-main' );
	}
	add_action( 'wp_enqueue_scripts', 'OWN_Theme_Styles' );
	add_action( 'wp_enqueue_scripts', 'OWN_Theme_Scripts' );
	
	function OWN_Theme_Tax_List( $tax ) {
		$list = "";
		$list_array = wp_get_post_terms( get_the_ID(), $tax );
		$list_count = count( $list_array );

		for ( $i = 0; $i < $list_count; $i++ ) {
			$url = get_term_link( $list_array[$i] );
			$list .= '<a href="' . $url. '">' . $list_array[$i]->name . '</a>';
			if ( $list_count > 1 && $i < $list_count - 2 ) $list .= ", ";
			if ( $list_count > 1 && $i == $list_count - 2 ) $list .= " and ";
		}
		echo $list;
	}

	function OWN_Theme_Archive_Order( $query ) {
		if ( $query->get( 'post_type' ) ) {
			$query->set( 'orderby' , 'title' );
			$query->set( 'order' , 'asc' );
		}
		return $query;
	}
	add_filter( 'pre_get_posts' , 'OWN_Theme_Archive_Order' );

?>