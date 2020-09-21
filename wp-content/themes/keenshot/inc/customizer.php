<?php


function keenshot_customize_register( $wp_customize ) {

	$wp_customize->add_section( 'header_section',
		array(
			'title'              => __( 'Header Settings', 'keenshot' ),
			'capability'         => 'edit_theme_options',
			'description_hidden' => 'false',
		)
	);

	$wp_customize->add_setting( 'header_layout', array(
		'default'           => 'left_right',
		'transport'         => 'refresh',
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'dirty'             => false,
		'sanitize_callback' => 'keenshot_customizer_sanitize'
	) );

	$wp_customize->add_control( 'header_layout', array(
		'label'       => __( 'Navigation Layout', 'keenshot' ),
		'section'     => 'header_section',
		'settings'    => 'header_layout',
		'description' => 'Select the header layout.',
		'type'        => 'select',
		'choices'     => array(
			'left_right' => __( 'Left - Right', 'keenshot' ),
			'center'     => __( 'Center', 'keenshot' )
		),
	) );

}

add_action( 'customize_register', 'keenshot_customize_register' );