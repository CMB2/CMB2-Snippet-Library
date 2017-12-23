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
		'display_cb'      => 'yourprefix_options_display_with_tabs', // Override the options-page form output (CMB2_Hookup::options_page_output()).
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
		'display_cb'   => 'yourprefix_options_display_with_tabs',
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
		'display_cb'   => 'yourprefix_options_display_with_tabs',
	) );

	$tertiary_options->add_field( array(
		'name' => esc_html__( 'Test Text Area for Code', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => 'textarea_code',
		'type' => 'textarea_code',
	) );

}
add_action( 'cmb2_admin_init', 'yourprefix_register_main_options_metabox' );

/**
 * A CMB2 options-page display callback override which adds tab navigation among
 * CMB2 options pages which share this same display callback.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 */
function yourprefix_options_display_with_tabs( $cmb_options ) {
	$tabs = yourprefix_options_page_tabs( $cmb_options->cmb->prop( 'display_cb' ) );
	?>
	<div class="wrap cmb2-options-page option-<?php echo $cmb_options->option_key; ?>">
		<?php // You can optionally remove this get_admin_page_title() line to use the tabs only ?>
		<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $option_key => $tab ) : ?>
				<a class="<?php echo $tab['class']; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab['title'] ); ?></a>
			<?php endforeach; ?>
		</h2>
		<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo $cmb_options->cmb->cmb_id; ?>" enctype="multipart/form-data" encoding="multipart/form-data">
			<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
			<?php $cmb_options->options_page_metabox(); ?>
			<?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Gets navigation tabs array for CMB2 options pages which share the given
 * display_cb param.
 *
 * @param  mixed  $display_cb The display_cb CMB2 property.
 *
 * @return array              Array of tab information.
 */
function yourprefix_options_page_tabs( $display_cb ) {
	// $boxes = CMB2_Boxes::get_by( 'display_cb', $display_cb )
	$boxes = CMB2_Boxes::get_all();
	$tabs  = array();

	foreach ( $boxes as $cmb_id => $cmb ) {
		if ( $display_cb === $cmb->prop( 'display_cb' ) ) {

			$option_key = $cmb->options_page_keys()[0];

			$tabs[ $option_key ] = array(
				'class' => 'nav-tab' . ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ? ' nav-tab-active' : '' ),
				'title' => $cmb->prop( 'title' ),
			);
		}
	}

	return $tabs;
}
