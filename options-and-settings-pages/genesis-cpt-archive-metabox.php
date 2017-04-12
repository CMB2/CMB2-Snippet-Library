<?php
/**
 * CMB2 Genesis CPT Archive Metabox
 * @version 0.1.0
 */
class Myprefix_Genesis_CPT_Settings_Metabox {

	/**
 	 * Mmetabox id
 	 * @var string
 	 */
	protected $metabox_id = 'genesis-cpt-archive-settings-metabox-%1$s';

	/**
 	 * CPT slug
 	 * @var string
 	 */
	public $post_type = 'post';

	/**
 	 * CPT slug
 	 * @var string
 	 */
	protected $admin_hook = '%1$s_page_genesis-cpt-archive-%1$s';

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	protected $key = 'genesis-cpt-archive-%1$s';

	/**
	 * Holds an instance of CMB2
	 *
	 * @var CMB2
	 */
	protected $cmb = null;

	/**
	 * Holds an instance of the object
	 *
	 * @var Myprefix_Genesis_CPT_Settings_Metabox
	 */
	protected static $instance = null;

	/**
	 * Returns the running object
	 *
	 * @return Myprefix_Genesis_CPT_Settings_Metabox
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	protected function __construct() {
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_hooks' ) );
		add_action( 'cmb2_admin_init', array( $this, 'register_metabox' ) );
	}


	/**
	 * Add hooks.
	 * @since  0.1.0
	 */
	public function init() {
		// Add custom archive support for CPT
		add_post_type_support( $this->post_type, 'genesis-cpt-archives-settings' );
	}

	/**
	 * Add admin hooks.
	 * @since 0.1.0
	 */
	public function admin_hooks() {

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->admin_hook()}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		add_action( 'genesis_cpt_archives_settings_metaboxes', array( $this, 'add_meta_box' ) );
	}

	public function add_meta_box( $page_hook ) {
		$cmb = $this->register_metabox();

		add_meta_box( $cmb->cmb_id, $cmb->prop( 'title' ), array( $this, 'output_metabox' ), $page_hook, 'main', $cmb->prop( 'priority' ) );
	}

	public function output_metabox() {
		$cmb = $this->register_metabox();
		$cmb->show_form( $cmb->object_id(), $cmb->object_type() );
	}

	/**
	 * Register the cpt arvhive metabox.
	 * @since  0.1.0
	 */
	function register_metabox() {
		if ( null !== $this->cmb ) {
			return $this->cmb;
		}

		$metabox_id = $this->metabox_id();

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$this->cmb = new_cmb2_box( array(
			'id'         => $metabox_id,
			'title'      => __( 'I\'m a Genesis Archive Settings CMB2 metabox', 'myprefix' ),
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key(), )
			),
		) );

		// Set our CMB2 fields

		$this->cmb->add_field( array(
			'name' => __( 'Test Text', 'myprefix' ),
			'desc' => __( 'field description (optional)', 'myprefix' ),
			'id'   => 'test_text',
			'type' => 'text',
			// 'default' => 'Default Text',
		) );

		$this->cmb->add_field( array(
			'name'    => __( 'Test Color Picker', 'myprefix' ),
			'desc'    => __( 'field description (optional)', 'myprefix' ),
			'id'      => 'test_colorpicker',
			'type'    => 'colorpicker',
			'default' => '#bada55',
		) );

	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		$key = $this->key();
		if ( $object_id !== $key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $key . '-notices' );
	}

	public function admin_hook() {
		return sprintf( $this->admin_hook, $this->post_type );
	}

	public function key() {
		return sprintf( $this->key, $this->post_type );
	}

	public function metabox_id() {
		return sprintf( $this->metabox_id, $this->post_type );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'metabox_id', 'key', 'admin_hook' ), true ) ) {
			return $this->{$field}();
		}

		if ( in_array( $field, array( 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Myprefix_Genesis_CPT_Settings_Metabox object
 * @since  0.1.0
 * @return Myprefix_Genesis_CPT_Settings_Metabox object
 */
function myprefix_genesis_cpt_settings() {
	return Myprefix_Genesis_CPT_Settings_Metabox::get_instance();
}


// Get it started
myprefix_genesis_cpt_settings();
