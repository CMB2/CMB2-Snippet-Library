<?php
/*
 * Plugin Name: CMB2 - Front-end Shortcode
 * Description: Display CMB2 metabox forms on the front-end using a shortcode
 * Author: jtsternberg
 * Author URI: http://dsgnwrks.pro
 * Version: 0.1.0
 */

/**
 * Shortcode to display a CMB2 form for a post ID.
 * Adding this shortcode to your WordPress editor would look something like this:
 *
 * [cmb-form id="test_metabox" post_id=2]
 *
 * The shortcode requires a metabox ID, and (optionally) can take
 * a WordPress post ID (or user/comment ID) to be editing.
 *
 * @param  array  $atts Shortcode attributes
 * @return string       Form HTML markup
 */
function jt_cmb2_do_frontend_form_shortcode( $atts = array() ) {
    global $post;

    /**
     * Make sure a WordPress post ID is set.
     * We'll default to the current post/page
     */
    if ( ! isset( $atts['post_id'] ) ) {
        $atts['post_id'] = $post->ID;
    }

    // If no metabox id is set, yell about it
    if ( empty( $atts['id'] ) ) {
        return 'Please add an "id" attribute to specify the CMB2 form to display.';
    }

    $metabox_id = esc_attr( $atts['id'] );
    $object_id = absint( $atts['post_id'] );
    // Get our form
    $form = cmb2_get_metabox_form( $metabox_id, $object_id );

    return $form;
}
add_shortcode( 'cmb-form', 'jt_cmb2_do_frontend_form_shortcode' );
