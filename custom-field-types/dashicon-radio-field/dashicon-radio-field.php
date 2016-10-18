<?php
/*
Plugin Name: CMB2 Dashicon Radio Field
Description: https://github.com/modemlooper/cmb2-dashicon-radio
Version: 1.0.0
Author: modemlooper
Author URI: http://twitter.com/modemlooper
License: GPL-2.0+
*/

/**
 * Custom render for dashicon_radio field
 *
 * @param  object $field
 * @param  string $escaped_value
 * @param  string $object_id
 * @param  string $object_type
 * @param  object $field_type_object
 * @return void
 */
function ml_cmb2_render_dashicon_radio_callback( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	if ( 'dashicon_radio' === $field->args['type'] ) {
		$field->args['options'] = ml_cmb2_dashicons_return_array();
	}
	echo $field_type_object->radio();

}
add_action( 'cmb2_render_dashicon_radio', 'ml_cmb2_render_dashicon_radio_callback', 10, 5 );

/**
 * Custom labels for dashicon_radio inputs
 *
 * @param  array $args
 * @param  array $defaults
 * @param  object $field
 * @param  object $cmb
 * @return array
 */
function ml_cmb2_dashicon_radio_attributes( $args, $defaults, $field, $cmb ) {

	if ( 'dashicon_radio' === $field->args['type'] ) {
		foreach ( cmb2_dashicons_return_array() as $dashicon => $name ) {
			if ( $dashicon === $args['value'] ) {
				$args['label'] = '<span class="dashicons ' . $dashicon . '"></span> ' . $name ;
			}
		}
	}
	return $args;
}
add_filter( 'cmb2_list_input_attributes', 'ml_cmb2_dashicon_radio_attributes', 10, 4 );

/**
 * Custom CMB2 css for dashicon_radio field
 *
 * @return void
 */
function ml_cmb2_dashicon_radio_css() {
	?>
	<style type="text/css" media="screen">
		.cmb-type-dashicon-radio .cmb-td {
			height: 200px;
			overflow: scroll;
			background-color: rgb(240, 240, 240);
		}
		.cmb-type-dashicon-radio .cmb2-radio-list {
			padding: 10px;

		}
	</style>
	<?php
}
add_action( 'admin_head', 'ml_cmb2_dashicon_radio_css' );

/**
 * Returns array of dashicon data
 *
 * @return array
 */
function ml_cmb2_dashicons_return_array() {

	$icons = array(
		'dashicons-menu' 				 	=> __( 'Menu', 'cmb2' ),
		'dashicons-dashboard' 				=> __( 'Dashboard', 'cmb2' ),
		'dashicons-admin-site' 				=> __( 'Admin Site', 'cmb2' ),
		'dashicons-admin-media'				=> __( 'Admin Media', 'cmb2' ),
		'dashicons-admin-page'				=> __( 'Admin Page', 'cmb2' ),
		'dashicons-admin-comments'			=> __( 'Admin Comments', 'cmb2' ),
		'dashicons-admin-appearance'		=> __( 'Admin Appearance', 'cmb2' ),
		'dashicons-admin-plugins'			=> __( 'Admin Plugins', 'cmb2' ),
		'dashicons-admin-users'				=> __( 'Admin Users', 'cmb2' ),
		'dashicons-admin-tools'				=> __( 'Admin Tools', 'cmb2' ),
		'dashicons-admin-settings'			=> __( 'Admin Settings', 'cmb2' ),
		'dashicons-admin-network'			=> __( 'Admin Network', 'cmb2' ),
		'dashicons-admin-generic'			=> __( 'Admin Generic', 'cmb2' ),
		'dashicons-admin-home'				=> __( 'Admin Home', 'cmb2' ),
		'dashicons-admin-collapse'			=> __( 'Admin Collapse', 'cmb2' ),
		'dashicons-admin-links'				=> __( 'Admin Links', 'cmb2' ),
		'dashicons-admin-post'				=> __( 'Admin Post', 'cmb2' ),
		'dashicons-format-standard'			=> __( 'Admin Plugins', 'cmb2' ),
		'dashicons-format-image'			=> __( 'Image Post Format', 'cmb2' ),
		'dashicons-format-gallery'			=> __( 'Gallery Post Format', 'cmb2' ),
		'dashicons-format-audio'			=> __( 'Audio Post Format', 'cmb2' ),
		'dashicons-format-video'			=> __( 'Video Post Format', 'cmb2' ),
		'dashicons-format-links'			=> __( 'Link Post Format', 'cmb2' ),
		'dashicons-format-chat'				=> __( 'Chat Post Format', 'cmb2' ),
		'dashicons-format-status'			=> __( 'Status Post Format', 'cmb2' ),
		'dashicons-format-aside'			=> __( 'Aside Post Format', 'cmb2' ),
		'dashicons-format-quote'			=> __( 'Quote Post Format', 'cmb2' ),
		'dashicons-welcome-write-blog'		=> __( 'Welcome Write Blog', 'cmb2' ),
		'dashicons-welcome-edit-page'		=> __( 'Welcome Edit Page', 'cmb2' ),
		'dashicons-welcome-add-page'		=> __( 'Welcome Add Page', 'cmb2' ),
		'dashicons-welcome-view-site'		=> __( 'Welcome View Site', 'cmb2' ),
		'dashicons-welcome-widgets-menus'	=> __( 'Welcome Widget Menus', 'cmb2' ),
		'dashicons-welcome-comments'		=> __( 'Welcome Comments', 'cmb2' ),
		'dashicons-welcome-learn-more'		=> __( 'Welcome Learn More', 'cmb2' ),
		'dashicons-image-crop'				=> __( 'Image Crop', 'cmb2' ),
		'dashicons-image-rotate-left'		=> __( 'Image Rotate Left', 'cmb2' ),
		'dashicons-image-rotate-right'		=> __( 'Image Rotate Right', 'cmb2' ),
		'dashicons-image-flip-vertical'		=> __( 'Image Flip Vertical', 'cmb2' ),
		'dashicons-image-flip-horizontal'	=> __( 'Image Flip Horizontal', 'cmb2' ),
		'dashicons-undo'					=> __( 'Undo', 'cmb2' ),
		'dashicons-redo'					=> __( 'Redo', 'cmb2' ),
		'dashicons-editor-bold'				=> __( 'Editor Bold', 'cmb2' ),
		'dashicons-editor-italic'			=> __( 'Editor Italic', 'cmb2' ),
		'dashicons-editor-ul'				=> __( 'Editor UL', 'cmb2' ),
		'dashicons-editor-ol'				=> __( 'Editor OL', 'cmb2' ),
		'dashicons-editor-quote'			=> __( 'Editor Quote', 'cmb2' ),
		'dashicons-editor-alignleft'		=> __( 'Editor Align Left', 'cmb2' ),
		'dashicons-editor-aligncenter'		=> __( 'Editor Align Center', 'cmb2' ),
		'dashicons-editor-alignright'		=> __( 'Editor Align Right', 'cmb2' ),
		'dashicons-editor-insertmore'		=> __( 'Editor Insert More', 'cmb2' ),
		'dashicons-editor-spellcheck'		=> __( 'Editor Spell Check', 'cmb2' ),
		'dashicons-editor-distractionfree'	=> __( 'Editor Distraction Free', 'cmb2' ),
		'dashicons-editor-expand'			=> __( 'Editor Expand', 'cmb2' ),
		'dashicons-editor-contract'			=> __( 'Editor Contract', 'cmb2' ),
		'dashicons-editor-kitchensink'		=> __( 'Editor Kitchen Sink', 'cmb2' ),
		'dashicons-editor-underline'		=> __( 'Editor Underline', 'cmb2' ),
		'dashicons-editor-justify'			=> __( 'Editor Justify', 'cmb2' ),
		'dashicons-editor-textcolor'		=> __( 'Editor Text Colour', 'cmb2' ),
		'dashicons-editor-paste-word'		=> __( 'Editor Paste Word', 'cmb2' ),
		'dashicons-editor-paste-text'		=> __( 'Editor Paste Text', 'cmb2' ),
		'dashicons-editor-removeformatting'	=> __( 'Editor Remove Formatting', 'cmb2' ),
		'dashicons-editor-video'			=> __( 'Editor Video', 'cmb2' ),
		'dashicons-editor-customchar'		=> __( 'Editor Custom Character', 'cmb2' ),
		'dashicons-editor-outdent'			=> __( 'Editor Outdent', 'cmb2' ),
		'dashicons-editor-indent'			=> __( 'Editor Indent', 'cmb2' ),
		'dashicons-editor-help'				=> __( 'Editor Help', 'cmb2' ),
		'dashicons-editor-strikethrough'	=> __( 'Editor Strikethrough', 'cmb2' ),
		'dashicons-editor-unlink'			=> __( 'Editor Unlink', 'cmb2' ),
		'dashicons-editor-rtl'				=> __( 'Editor RTL', 'cmb2' ),
		'dashicons-editor-break'			=> __( 'Editor Break', 'cmb2' ),
		'dashicons-editor-code'				=> __( 'Editor Code', 'cmb2' ),
		'dashicons-editor-paragraph'		=> __( 'Editor Paragraph', 'cmb2' ),
		'dashicons-align-left'				=> __( 'Align Left', 'cmb2' ),
		'dashicons-align-right'				=> __( 'Align Right', 'cmb2' ),
		'dashicons-align-center'			=> __( 'Align Center', 'cmb2' ),
		'dashicons-align-none'				=> __( 'Align None', 'cmb2' ),
		'dashicons-lock'					=> __( 'Lock', 'cmb2' ),
		'dashicons-calendar'				=> __( 'Calendar', 'cmb2' ),
		'dashicons-visibility'				=> __( 'Visibility', 'cmb2' ),
		'dashicons-post-status'				=> __( 'Post Status', 'cmb2' ),
		'dashicons-edit'					=> __( 'Edit', 'cmb2' ),
		'dashicons-post-trash'				=> __( 'Post Trash', 'cmb2' ),
		'dashicons-trash'					=> __( 'Trash', 'cmb2' ),
		'dashicons-external'				=> __( 'External', 'cmb2' ),
		'dashicons-arrow-up'				=> __( 'Arrow Up', 'cmb2' ),
		'dashicons-arrow-down'				=> __( 'Arrow Down', 'cmb2' ),
		'dashicons-arrow-left'				=> __( 'Arrow Left', 'cmb2' ),
		'dashicons-arrow-right'				=> __( 'Arrow Right', 'cmb2' ),
		'dashicons-arrow-up-alt'			=> __( 'Arrow Up (alt)', 'cmb2' ),
		'dashicons-arrow-down-alt'			=> __( 'Arrow Down (alt)', 'cmb2' ),
		'dashicons-arrow-left-alt'			=> __( 'Arrow Left (alt)', 'cmb2' ),
		'dashicons-arrow-right-alt'			=> __( 'Arrow Right (alt)', 'cmb2' ),
		'dashicons-arrow-up-alt2'			=> __( 'Arrow Up (alt 2)', 'cmb2' ),
		'dashicons-arrow-down-alt2'			=> __( 'Arrow Down (alt 2)', 'cmb2' ),
		'dashicons-arrow-left-alt2'			=> __( 'Arrow Left (alt 2)', 'cmb2' ),
		'dashicons-arrow-right-alt2'		=> __( 'Arrow Right (alt 2)', 'cmb2' ),
		'dashicons-leftright'				=> __( 'Arrow Left-Right', 'cmb2' ),
		'dashicons-sort'					=> __( 'Sort', 'cmb2' ),
		'dashicons-randomize'				=> __( 'Randomise', 'cmb2' ),
		'dashicons-list-view'				=> __( 'List View', 'cmb2' ),
		'dashicons-exerpt-view'				=> __( 'Excerpt View', 'cmb2' ),
		'dashicons-hammer'					=> __( 'Hammer', 'cmb2' ),
		'dashicons-art'						=> __( 'Art', 'cmb2' ),
		'dashicons-migrate'					=> __( 'Migrate', 'cmb2' ),
		'dashicons-performance'				=> __( 'Performance', 'cmb2' ),
		'dashicons-universal-access'		=> __( 'Universal Access', 'cmb2' ),
		'dashicons-universal-access-alt'	=> __( 'Universal Access (alt)', 'cmb2' ),
		'dashicons-tickets'					=> __( 'Tickets', 'cmb2' ),
		'dashicons-nametag'					=> __( 'Name Tag', 'cmb2' ),
		'dashicons-clipboard'				=> __( 'Clipboard', 'cmb2' ),
		'dashicons-heart'					=> __( 'Heart', 'cmb2' ),
		'dashicons-megaphone'				=> __( 'Megaphone', 'cmb2' ),
		'dashicons-schedule'				=> __( 'Schedule', 'cmb2' ),
		'dashicons-wordpress'				=> __( 'WordPress', 'cmb2' ),
		'dashicons-wordpress-alt'			=> __( 'WordPress (alt)', 'cmb2' ),
		'dashicons-pressthis'				=> __( 'Press This', 'cmb2' ),
		'dashicons-update'					=> __( 'Update', 'cmb2' ),
		'dashicons-screenoptions'			=> __( 'Screen Options', 'cmb2' ),
		'dashicons-info'					=> __( 'Info', 'cmb2' ),
		'dashicons-cart'					=> __( 'Cart', 'cmb2' ),
		'dashicons-feedback'				=> __( 'Feedback', 'cmb2' ),
		'dashicons-cloud'					=> __( 'Cloud', 'cmb2' ),
		'dashicons-translation'				=> __( 'Translation', 'cmb2' ),
		'dashicons-tag'						=> __( 'Tag', 'cmb2' ),
		'dashicons-category'				=> __( 'Category', 'cmb2' ),
		'dashicons-archive'					=> __( 'Archive', 'cmb2' ),
		'dashicons-tagcloud'				=> __( 'Tag Cloud', 'cmb2' ),
		'dashicons-text'					=> __( 'Text', 'cmb2' ),
		'dashicons-media-archive'			=> __( 'Media Archive', 'cmb2' ),
		'dashicons-media-audio'				=> __( 'Media Audio', 'cmb2' ),
		'dashicons-media-code'				=> __( 'Media Code)', 'cmb2' ),
		'dashicons-media-default'			=> __( 'Media Default', 'cmb2' ),
		'dashicons-media-document'			=> __( 'Media Document', 'cmb2' ),
		'dashicons-media-interactive'		=> __( 'Media Interactive', 'cmb2' ),
		'dashicons-media-spreadsheet'		=> __( 'Media Spreadsheet', 'cmb2' ),
		'dashicons-media-text'				=> __( 'Media Text', 'cmb2' ),
		'dashicons-media-video'				=> __( 'Media Video', 'cmb2' ),
		'dashicons-playlist-audio'			=> __( 'Audio Playlist', 'cmb2' ),
		'dashicons-playlist-video'			=> __( 'Video Playlist', 'cmb2' ),
		'dashicons-yes'						=> __( 'Yes', 'cmb2' ),
		'dashicons-no'						=> __( 'No', 'cmb2' ),
		'dashicons-no-alt'					=> __( 'No (alt)', 'cmb2' ),
		'dashicons-plus'					=> __( 'Plus', 'cmb2' ),
		'dashicons-plus-alt'				=> __( 'Plus (alt)', 'cmb2' ),
		'dashicons-minus'					=> __( 'Minus', 'cmb2' ),
		'dashicons-dismiss'					=> __( 'Dismiss', 'cmb2' ),
		'dashicons-marker'					=> __( 'Marker', 'cmb2' ),
		'dashicons-star-filled'				=> __( 'Star Filled', 'cmb2' ),
		'dashicons-star-half'				=> __( 'Star Half', 'cmb2' ),
		'dashicons-star-empty'				=> __( 'Star Empty', 'cmb2' ),
		'dashicons-flag'					=> __( 'Flag', 'cmb2' ),
		'dashicons-share'					=> __( 'Share', 'cmb2' ),
		'dashicons-share1'					=> __( 'Share 1', 'cmb2' ),
		'dashicons-share-alt'				=> __( 'Share (alt)', 'cmb2' ),
		'dashicons-share-alt2'				=> __( 'Share (alt 2)', 'cmb2' ),
		'dashicons-twitter'					=> __( 'twitter', 'cmb2' ),
		'dashicons-rss'						=> __( 'RSS', 'cmb2' ),
		'dashicons-email'					=> __( 'Email', 'cmb2' ),
		'dashicons-email-alt'				=> __( 'Email (alt)', 'cmb2' ),
		'dashicons-facebook'				=> __( 'Facebook', 'cmb2' ),
		'dashicons-facebook-alt'			=> __( 'Facebook (alt)', 'cmb2' ),
		'dashicons-networking'				=> __( 'Networking', 'cmb2' ),
		'dashicons-googleplus'				=> __( 'Google+', 'cmb2' ),
		'dashicons-location'				=> __( 'Location', 'cmb2' ),
		'dashicons-location-alt'			=> __( 'Location (alt)', 'cmb2' ),
		'dashicons-camera'					=> __( 'Camera', 'cmb2' ),
		'dashicons-images-alt'				=> __( 'Images', 'cmb2' ),
		'dashicons-images-alt2'				=> __( 'Images Alt', 'cmb2' ),
		'dashicons-video-alt'				=> __( 'Video (alt)', 'cmb2' ),
		'dashicons-video-alt2'				=> __( 'Video (alt 2)', 'cmb2' ),
		'dashicons-video-alt3'				=> __( 'Video (alt 3)', 'cmb2' ),
		'dashicons-vault'					=> __( 'Vault', 'cmb2' ),
		'dashicons-shield'					=> __( 'Shield', 'cmb2' ),
		'dashicons-shield-alt'				=> __( 'Shield (alt)', 'cmb2' ),
		'dashicons-sos'						=> __( 'SOS', 'cmb2' ),
		'dashicons-search'					=> __( 'Search', 'cmb2' ),
		'dashicons-slides'					=> __( 'Slides', 'cmb2' ),
		'dashicons-analytics'				=> __( 'Analytics', 'cmb2' ),
		'dashicons-chart-pie'				=> __( 'Pie Chart', 'cmb2' ),
		'dashicons-chart-bar'				=> __( 'Bar Chart', 'cmb2' ),
		'dashicons-chart-line'				=> __( 'Line Chart', 'cmb2' ),
		'dashicons-chart-area'				=> __( 'Area Chart', 'cmb2' ),
		'dashicons-groups'					=> __( 'Groups', 'cmb2' ),
		'dashicons-businessman'				=> __( 'Businessman', 'cmb2' ),
		'dashicons-id'						=> __( 'ID', 'cmb2' ),
		'dashicons-id-alt'					=> __( 'ID (alt)', 'cmb2' ),
		'dashicons-products'				=> __( 'Products', 'cmb2' ),
		'dashicons-awards'					=> __( 'Awards', 'cmb2' ),
		'dashicons-forms'					=> __( 'Forms', 'cmb2' ),
		'dashicons-testimonial'				=> __( 'Testimonial', 'cmb2' ),
		'dashicons-portfolio'				=> __( 'Portfolio', 'cmb2' ),
		'dashicons-book'					=> __( 'Book', 'cmb2' ),
		'dashicons-book-alt'				=> __( 'Book (alt)', 'cmb2' ),
		'dashicons-download'				=> __( 'Download', 'cmb2' ),
		'dashicons-upload'					=> __( 'Upload', 'cmb2' ),
		'dashicons-backup'					=> __( 'Backup', 'cmb2' ),
		'dashicons-clock'					=> __( 'Clock', 'cmb2' ),
		'dashicons-lightbulb'				=> __( 'Lightbulb', 'cmb2' ),
		'dashicons-microphone'				=> __( 'Microphone', 'cmb2' ),
		'dashicons-desktop'					=> __( 'Desktop', 'cmb2' ),
		'dashicons-tablet'					=> __( 'Tablet', 'cmb2' ),
		'dashicons-smartphone'				=> __( 'Smartphone', 'cmb2' ),
		'dashicons-smiley'					=> __( 'Smiley', 'cmb2' ),
	);

	return $icons;
}
