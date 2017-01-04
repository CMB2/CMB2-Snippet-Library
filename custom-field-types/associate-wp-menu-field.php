<?php
/**
 * Associates a WP menu with a post, and provides links between the post/menu editing interfaces.
 */

class CMB2_Post_Menu_Association {

	public $menu_types    = array( 'post', 'page' );
	public $menu_meta_key = 'associated_post_menu';

	protected static $single_instance = null;
	protected $associated_post        = null;
	protected $menu_id                = 0;

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return CMB2_Post_Menu_Association A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	protected function __construct() {
		add_action( 'cmb2_admin_init', array( $this, 'register_post_menu_box' ) );
		add_action( 'all_admin_notices', array( $this, 'associate_menu_post_message' ) );
		add_action( 'wp_create_nav_menu', array( $this, 'set_menu_to_post' ) );
	}

	public function register_post_menu_box() {

		// Add menu metabox to post types
		$cmb = new_cmb2_box( array(
			'id'           => 'post-menu-mb',
			'title'        => 'Associated Menu',
			'object_types' => $this->menu_types, // Post type
			'context'      => 'side',
			'priority'     => 'low',
		) );

		$cmb->add_field( array(
			'id'           => $this->menu_meta_key,
			'type'         => 'select',
			'options_cb'   => array( $this, 'get_menu_list_options' ),
			'after'        => array( $this, 'post_menu_edit_or_create_link' ),
		) );

	}

	public function get_menu_list_options() {
		$menus = wp_get_nav_menus();
		if ( ! empty( $menus ) ) {
			$menus = wp_list_pluck( $menus, 'name', 'term_id' );
		} else {
			$menus = array();
		}

		$menus = array( '' => 'Select Menu' ) + $menus;
		return $menus;
	}

	function post_menu_edit_or_create_link( $args, $field ) {
		$menu_id = get_post_meta( $field->object_id(), $this->menu_meta_key, 1 );

		$url = admin_url( '/nav-menus.php?action=edit&menu=' . absint( $menu_id ) . '&post_association=' . $field->object_id() );
		$link_title = $menu_id ? 'Edit Selected Menu' : 'Create New Menu';

		return '<a href="'. $url .'">'. $link_title .'</a>';
	}

	public function associate_menu_post_message() {
		if ( empty( $_REQUEST['post_association'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['menu'] ) ) {
			$this->associate_menu_message( $_REQUEST['post_association'], 'When created, this Menu will be set as the %s Menu for: <strong><a href="%s">%s</a></strong> (%d)' );
		} else {
			$this->associate_menu_message( $_REQUEST['post_association'], 'This Menu is set as the %s Menu for: <strong><a href="%s">%s</a></strong> (%d)' );
		}
	}

	public function associate_menu_message( $post_id, $message ) {
		global $pagenow;
		if ( empty( $post_id ) || 'nav-menus.php' !== $pagenow ) {
			return;
		}

		$post = get_post( absint( $post_id ) );

		if ( empty( $post ) ) {
			return;
		}

		$pt = get_post_type_object( $post->post_type );

		printf(
			'<div id="message" class="updated"><p>%s</p></div>',
			sprintf(
				$message,
				$pt->labels->singular_name,
				get_permalink( $post->ID ),
				get_the_title( $post->ID ),
				$post->ID
			)
		);
	}

	public function set_menu_to_post( $menu_id ) {
		if ( empty( $_REQUEST['post_association'] ) || empty( $menu_id ) ) {
			return;
		}

		$post = get_post( absint( $_REQUEST['post_association']  ) );

		if ( empty( $post ) || ! in_array( $post->post_type, $this->menu_types, true ) ) {
			return;
		}

		$this->menu_id = $menu_id;
		$this->associated_post = $post;

		update_post_meta( $post->ID, $this->menu_meta_key, $menu_id );

		// Modify the post-menu-save redirect to add our query var.
		add_filter( 'wp_redirect', array( $this, 'redirect_with_post_association' ) );
	}

	public function redirect_with_post_association( $location ) {
		if ( $this->menu_id && $location === admin_url( 'nav-menus.php?menu='. $this->menu_id ) ) {
			// Add our associated post id query var.
			$location = add_query_arg( 'post_association', $this->associated_post->ID, $location );
		}

		return $location;
	}

}
CMB2_Post_Menu_Association::get_instance();
