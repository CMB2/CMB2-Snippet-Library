<?php
/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class myprefix_Admin {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'myprefix_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'myprefix_option_metabox';

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected $option_metabox = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Site Options', 'myprefix' );

		// Set our CMB2 fields
		$this->fields = array(
			array(
				'name' => __( 'Test Text', 'myprefix' ),
				'desc' => __( 'field description (optional)', 'myprefix' ),
				'id'   => 'test_text',
				'type' => 'text',
			),
			array(
				'name'    => __( 'Test Color Picker', 'myprefix' ),
				'desc'    => __( 'field description (optional)', 'myprefix' ),
				'id'      => 'test_colorpicker',
				'type'    => 'colorpicker',
				'default' => '#ffffff'
			),
		);
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_filter( 'cmb2_meta_boxes', array( $this, 'add_options_page_metabox' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2_options_page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 * @param  array $meta_boxes
	 * @return array $meta_boxes
	 */
	function add_options_page_metabox( array $meta_boxes ) {
		$meta_boxes[] = $this->option_metabox();

		return $meta_boxes;
	}

	/**
	 * Defines the theme option metabox and field configuration
	 * @since  0.1.0
	 * @return array
	 */
	public function option_metabox() {
		return array(
			'id'      => $this->metabox_id,
			'fields'  => $this->fields,
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		);
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'fields', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		if ( 'option_metabox' === $field ) {
			return $this->option_metabox();
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

// Get it started
$GLOBALS['myprefix_Admin'] = new myprefix_Admin();
$GLOBALS['myprefix_Admin']->hooks();

/**
 * Helper function to get/return the myprefix_Admin object
 * @since  0.1.0
 * @return myprefix_Admin object
 */
function myprefix_Admin() {
	global $myprefix_Admin;
	return $myprefix_Admin;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function myprefix_get_option( $key = '' ) {
	global $myprefix_Admin;
	return cmb2_get_option( $myprefix_Admin->key, $key );
}
