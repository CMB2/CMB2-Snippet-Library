<?php
/**
 * Associates a WP menu with a post, and provides links between the post/menu editing interfaces.
 * Screenshots:
 * - metabox: http://b.ustin.co/Gxhf
 * - widget: http://b.ustin.co/lD30
 */

function cmb2_register_post_menu_box() {

	$menu_assoc = CMB2_Post_Menu_Association::get_instance();
	$menu_assoc->menu_types    = array( 'post', 'page' );
	$menu_assoc->metabox_id    = 'associated_post_menu_box';
	$menu_assoc->menu_meta_key = 'associated_post_menu';

	if ( is_admin() ) {
		// Register menu association metabox to post types
		$cmb = $menu_assoc->register_post_menu_box();
	}
}
add_action( 'cmb2_init', 'cmb2_register_post_menu_box' );

class CMB2_Post_Menu_Association {

	public $menu_types    = array();
	public $metabox_id    = '';
	public $menu_meta_key = '';
	public $menu_title_meta_key = '';

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
		add_action( 'all_admin_notices', array( $this, 'associate_menu_post_message' ) );
		add_action( 'wp_create_nav_menu', array( $this, 'set_menu_to_post' ) );
	}

	public function register_post_menu_box() {
		return new_cmb2_box( $this->register_post_menu_box_args() );
	}

	public function register_post_menu_box_args() {
		$title_key = $this->menu_title_meta_key();
		return apply_filters( 'register_post_menu_box_args', array(
			'id'           => $this->metabox_id,
			'title'        => 'Associated Menu',
			'object_types' => $this->menu_types, // Post type
			'context'      => 'side',
			'priority'     => 'low',
			'fields'       => array(
				$title_key => array(
					'name' => 'Menu Widget Title',
					'id'   => $title_key,
					'type' => 'text',
				),
				$this->menu_meta_key => array(
					'desc'         => 'If no menu is selected, associated menu widget will not show.',
					'id'           => $this->menu_meta_key,
					'type'         => 'select',
					'options_cb'   => array( $this, 'get_menus_list_options' ),
					'after'        => array( $this, 'post_menu_edit_or_create_link' ),
				),
			),
		) );
	}

	public function get_menus_list_options() {
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
		static $script_added = false;

		$menu_id = get_post_meta( $field->object_id(), $this->menu_meta_key, 1 );

		$url = admin_url( '/nav-menus.php?action=edit&menu=' . absint( $menu_id ) . '&post_association=' . $field->object_id() );
		$link_title = $menu_id ? 'Edit Selected Menu' : 'Create New Menu';

		?>
		<a href="<?php echo esc_url( $url ); ?>"><?php echo $link_title; ?></a>

		<p class="cmb2-metabox-description explain-widget-necessary"><strong>Note:</strong> In order for this menu to display, you will need to ensure the "Associated Post Menu" widget is placed in the <a href="<?php echo admin_url( 'widgets.php' ); ?>">widget area</a> for this template.</p>

		<?php if ( ! $script_added ) : ?>
			<script type="text/javascript">
				if ( '#<?php echo $this->metabox_id; ?>' === window.location.hash ) {
					var el = document.getElementById( '<?php echo $this->metabox_id; ?>' );
					el.className += ' post-menu-box-highlighted';
				}
			</script>
			<style type="text/css">
				.post-menu-box-highlighted {
					box-shadow: 0 0 10px 5px rgba(226, 73, 73, 0.28);
				}
				#side-sortables #<?php echo $this->metabox_id; ?> .explain-widget-necessary {
					padding-bottom: 0;
					padding-top: 10px;
					margin-bottom: -15px;
				}
			</style>
		<?php endif;

		$script_added = true;
	}

	public function associate_menu_post_message() {
		if ( empty( $_REQUEST['post_association'] ) || ! $this->menu_meta_key ) {
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
				get_edit_post_link( $post->ID ) .'#'. $this->metabox_id,
				get_the_title( $post->ID ),
				$post->ID
			)
		);
	}

	public function set_menu_to_post( $menu_id ) {
		if ( empty( $_REQUEST['post_association'] ) || empty( $menu_id ) || ! $this->menu_meta_key ) {
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

	public function menu_title_meta_key() {
		return $this->menu_title_meta_key ? $this->menu_title_meta_key : $this->menu_meta_key . '_widget_title';
	}

}
CMB2_Post_Menu_Association::get_instance();

/**
 * Handles the Post Menu Widget
 *
 * @since 3.0.0
 *
 * @see WP_Widget
 */
class CMB2_Post_Menu_Widget extends WP_Widget {

	/**
	 * Sets up a new Associated Post Menu widget instance.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'customize_selective_refresh' => true,
			'description' => 'Shows an associated custom menu in your sidebar, if it is set.',
			'classname'   => 'associated-post-menu',
		);
		parent::__construct( 'associated-post-menu', 'Associated Post Menu', $widget_ops );
	}

	/**
	 * Outputs the content for the associated Menu widget instance.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Associated Post Menu widget instance.
	 */
	public function widget( $args, $instance ) {
		// Get menu
		$menu_id = absint( get_post_meta( get_the_ID(), CMB2_Post_Menu_Association::get_instance()->menu_meta_key, 1 ) );
		$nav_menu = ! empty( $menu_id ) ? wp_get_nav_menu_object( $menu_id ) : false;

		if ( ! $nav_menu ) {
			return;
		}

		$widget_title = get_post_meta( get_the_ID(), CMB2_Post_Menu_Association::get_instance()->menu_title_meta_key(), 1 );
		if ( empty( $widget_title ) ) {
			$widget_title = isset( $instance['title'] ) ? $instance['title'] : '';
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$widget_title = apply_filters( 'widget_title', empty( $widget_title ) ? '' : $widget_title, $instance, $this->id_base );

		echo $args['before_widget'];

		if ( ! empty( $widget_title ) ) {
			echo $args['before_title'] . $widget_title . $args['after_title'];
		}

		$nav_menu_args = array(
			'fallback_cb' => '',
			'menu'        => $nav_menu,
		);

		/**
		 * Filters the arguments for the Associated Post Menu widget.
		 *
		 * @since 4.2.0
		 * @since 4.4.0 Added the `$instance` parameter.
		 *
		 * @param array    $nav_menu_args {
		 *     An array of arguments passed to wp_nav_menu() to retrieve a custom menu.
		 *
		 *     @type callable|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
		 *     @type mixed         $menu        Menu ID, slug, or name.
		 * }
		 * @param WP_Term  $nav_menu      Nav menu object for the current menu.
		 * @param array    $args          Display arguments for the current widget.
		 * @param array    $instance      Array of settings for the current widget.
		 */
		wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance ) );

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Associated Post Menu widget instance.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		if ( ! empty( $new_instance['title'] ) ) {
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
		}

		return $instance;
	}

	/**
	 * Outputs the settings form for the Associated Post Menu widget.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';

		// If no menus exists, direct the user to go and create some.
		?>
		<div class="nav-menu-widget-form-controls">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">Fallback Title:</label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
			</p>
		</div>
		<?php
	}
}

function cmb2_register_post_menu_widget() {
	register_widget('CMB2_Post_Menu_Widget');
}
add_action( 'widgets_init', 'cmb2_register_post_menu_widget' );
