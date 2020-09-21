<?php

$main_menu = get_term_by( 'name', 'All Pages Flat', 'nav_menu' );

if ( ! function_exists( 'keenshot_import_files' ) ) {
	function keenshot_import_files() {
		return array(
			array(
				'import_file_name'           => __( 'Default Demo', 'keenshot' ),
				'categories'                 => array( 'Default' ),
				'import_file_url'            => 'http://themes.keendevs.com/keenshot/demos/default/content.xml',
				'import_customizer_file_url' => 'http://themes.keendevs.com/keenshot/demos/default/customizer.dat',
				'import_widget_file_url'     => 'http://themes.keendevs.com/keenshot/demos/default/widgets.wie',
				'import_preview_image_url'   => 'http://themes.keendevs.com/keenshot/demos/default/screenshot.png',
				'import_notice'              => __( 'Please wait a few minutes, do not close the window or refresh the page until the data is imported.', 'keenshot' ),
				'preview_url'                => 'https://themes.keendevs.com/keenshot/',
			),
			array(
				'import_file_name'           => __( 'Demo 2', 'keenshot' ),
				'categories'                 => array( 'Demo 2' ),
				'import_file_url'            => 'http://themes.keendevs.com/keenshot/demos/demo-2/content.xml',
				'import_customizer_file_url' => 'http://themes.keendevs.com/keenshot/demos/demo-2/customizer.dat',
				'import_widget_file_url'     => 'http://themes.keendevs.com/keenshot/demos/demo-2/widgets.wie',
				'import_preview_image_url'   => 'http://themes.keendevs.com/keenshot/demos/demo-2/screenshot.png',
				'import_notice'              => __( 'Please wait a few minutes, do not close the window or refresh the page until the data is imported.', 'keenshot' ),
				'preview_url'                => 'https://themes.keendevs.com/keenshot/demo-2',
			),
		);
	}
}
add_filter( 'pt-ocdi/import_files', 'keenshot_import_files' );

if ( ! function_exists( 'keenshot_after_import_setup' ) ) {
	function keenshot_after_import_setup($data) {

		// Assign menus to their locations.
		$primary_menu = get_term_by( 'name', 'Header Menu', 'nav_menu' );
		set_theme_mod( 'nav_menu_locations', array(
				'primary' => $primary_menu->term_id,
			)
		);

		// Assign front page and posts page (blog page).
		if($data['import_file_name'] == 'Demo 2'){
			$front_page_id = get_page_by_title( 'Demo 2' );
		}else{
			$front_page_id = get_page_by_title( 'Home' );
		}


		$blog_page_id  = get_page_by_title( 'Blog' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );

	}
}
add_action( 'pt-ocdi/after_import', 'keenshot_after_import_setup' );