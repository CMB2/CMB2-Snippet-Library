<?php
/**
 * CMB2 Network Settings
 * @version 0.1.0
 */
class Prefix_Add_CMB2_To_Settings_Page {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	protected $key = 'myprefix_settings';

	/**
 	 * Settings page metabox id
 	 * @var string
 	 */
	protected $metabox_id = 'myprefix_setting_metabox';

	/**
 	 * Settings page screen id where metabox should show.
 	 * @var string
 	 */
	protected $screen_id = 'options-general';

	/**
	 * Holds an instance of the project
	 *
	 * @Prefix_Add_CMB2_To_Settings_Page
	 **/
	protected static $instance = null;

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	protected function __construct() {}

	/**
	 * Get the running object
	 *
	 * @return Prefix_Add_CMB2_To_Settings_Page
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'cmb2_admin_init', array( $this, 'register_metabox' ) );
		add_action( 'current_screen', array( $this, 'maybe_save' ) );
		add_filter( 'admin_footer' , array( $this , 'maybe_hookup_fields' ), 2 /* Early before all scripts are output. */ );
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function register_metabox() {
		$cmb = new_cmb2_box( array(
			'id'           => $this->metabox_id,
			'hookup'       => false,
			'object_types' => array( 'options-page' ),
		) );

		// Set our CMB2 fields

		$cmb->add_field( array(
			'name' => __( 'Test Text', 'myprefix' ),
			'desc' => __( 'field description (optional)', 'myprefix' ),
			'id'   => 'test_text',
			'type' => 'text',
			// 'default' => 'Default Text',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Test Color Picker', 'myprefix' ),
			'desc'    => __( 'field description (optional)', 'myprefix' ),
			'id'      => 'test_colorpicker',
			'type'    => 'colorpicker',
			'default' => '#bada55',
		) );

	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function maybe_save() {
		if ( empty( $_POST ) ) {
			return;
		}

		$url = wp_get_referer();
		// Check if our screen id is in the referrer url.
		if ( false === strpos( $url, $this->screen_id ) ) {
			return;
		}

		// Hook into whitelist_options as we know it's only called if the default save-checks have finished.
		add_filter( 'whitelist_options', array( $this, 'save_our_options' ) );
	}

	/**
	 * Simply used as a options.php life-cycle hook to save our settings
	 * (since there doesn't appear to be any proper hooks)
	 *
	 * @since  0.1.0
	 *
	 * @param  array  $whitelist_options
	 *
	 * @return array
	 */
	public function save_our_options( $whitelist_options ) {
		$cmb = cmb2_get_metabox( $this->metabox_id, $this->key );
		if ( $cmb ) {

			$hookup = new CMB2_hookup( $cmb );

			if ( $hookup->can_save( 'options-page' ) ) {
				$cmb->save_fields( $this->key, 'options-page', $_POST );
			}
		}

		// Our saving is done, so cleanup.
		remove_filter( 'whitelist_options', array( $this, 'save_our_options' ) );

		return $whitelist_options;
	}

	/**
	 * Maybe hookup our CMB2 fields.
	 *
	 * @since 0.1.0
	 */
	public function maybe_hookup_fields() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : (object) array( 'id' => null );

		// Only show on our screen.
		if ( $this->screen_id !== $screen->id ) {
			return;
		}

		CMB2_hookup::enqueue_cmb_css();
		$this->admin_page_display();
	}

	/**
	 * CMB2 fields output
	 * Wile hide by default in the footer, then use JS to move it inside the form. Hacky, yep.
	 *
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div id="cmb2-options-page-<?php echo $this->key; ?>" class="wrap cmb2-options-page <?php echo $this->key; ?>" style="display:none">
			<?php cmb2_get_metabox( $this->metabox_id, $this->key, 'options-page' )->show_form(); ?>
		</div>
		<script type="text/javascript">
			var cmb2 = document.getElementById( 'cmb2-options-page-<?php echo $this->key; ?>' );
			var submit = document.getElementById( 'submit' ).parentNode;
			submit.parentNode.insertBefore( cmb2, submit );
			cmb2.style.display = '';
		</script>
		<?php
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'screen_id' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Prefix_Add_CMB2_To_Settings_Page object
 * @since  0.1.0
 * @return Prefix_Add_CMB2_To_Settings_Page object
 */
function myprefix_cmb2_on_settings() {
	return Prefix_Add_CMB2_To_Settings_Page::get_instance();
}


/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function myprefix_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( myprefix_cmb2_on_settings()->key, $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( myprefix_cmb2_on_settings()->key, $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}

// Get it started
myprefix_cmb2_on_settings();