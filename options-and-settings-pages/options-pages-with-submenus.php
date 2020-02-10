<?php

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function yourprefix_register_main_options_metabox() {

	/**
	 * Registers main options page menu item and form.
	 */
	$main_options = new_cmb2_box( array(
		'id'           => 'yourprefix_main_options_page',
		'title'        => esc_html__( 'Main Options', 'cmb2' ),
		'object_types' => array( 'options-page' ),

		/*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */

		'option_key'      => 'yourprefix_main_options', // The option key and admin menu page slug.
		// 'icon_url'        => 'dashicons-palmtree', // Menu icon. Only applicable if 'parent_slug' is left empty.
		// 'menu_title'      => esc_html__( 'Options', 'cmb2' ), // Falls back to 'title' (above).
		// 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
		// 'capability'      => 'manage_options', // Cap required to view options-page.
		// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
		// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
		// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
		// 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
		// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
		// 'message_cb'      => 'yourprefix_options_page_message_callback',
	) );

	/**
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */
	$main_options->add_field( array(
		'name'    => esc_html__( 'Site Background Color', 'cmb2' ),
		'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => 'bg_color',
		'type'    => 'colorpicker',
		'default' => '#ffffff',
	) );

	/**
	 * Registers secondary options page, and set main item as parent.
	 */
	$secondary_options = new_cmb2_box( array(
		'id'           => 'yourprefix_secondary_options_page',
		'title'        => esc_html__( 'Secondary Options', 'cmb2' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'yourprefix_secondary_options',
		'parent_slug'  => 'yourprefix_main_options',
	) );

	$secondary_options->add_field( array(
		'name'    => esc_html__( 'Test Radio', 'cmb2' ),
		'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'      => 'radio',
		'type'    => 'radio',
		'options' => array(
			'option1' => esc_html__( 'Option One', 'cmb2' ),
			'option2' => esc_html__( 'Option Two', 'cmb2' ),
			'option3' => esc_html__( 'Option Three', 'cmb2' ),
		),
	) );

	/**
	 * Registers tertiary options page, and set main item as parent.
	 */
	$tertiary_options = new_cmb2_box( array(
		'id'           => 'yourprefix_tertiary_options_page',
		'title'        => esc_html__( 'Tertiary Options', 'cmb2' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'yourprefix_tertiary_options',
		'parent_slug'  => 'yourprefix_main_options',
	) );

	$tertiary_options->add_field( array(
		'name' => esc_html__( 'Test Text Area for Code', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => 'textarea_code',
		'type' => 'textarea_code',
	) );

}
add_action( 'cmb2_admin_init', 'yourprefix_register_main_options_metabox' );
