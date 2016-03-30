<?php
/**
 * This allows you to specify one or more taxonomies, and for each taxonomy one or more terms.
 * If a post is tagged one of those terms, this metabox shows up on its Edit screen.
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters#example-taxonomy-show_on-filter wiki
 */

$cmb = new_cmb2_box( array(
	'id'         => 'show_for_taxonomy_terms',
	'title'      => 'Demo',
	'show_on_cb' => 'be_taxonomy_show_on_filter', // function should return a bool value
	'show_on_terms' => array(
		'category' => array( 'featured' ),
		'post_tag' => array( 'best-of' ),
	),
) );

/**
 * Taxonomy show_on filter
 * @author Bill Erickson
 * @param  object $cmb CMB2 object
 * @return bool        True/false whether to show the metabox
 */
function be_taxonomy_show_on_filter( $cmb ) {
	$tax_terms_to_show_on = $cmb->prop( 'show_on_terms', array() );
	if ( empty( $tax_terms_to_show_on ) || ! $cmb->object_id() ) {
		return false;
	}

	$post_id = $cmb->object_id();
	$post = get_post( $post_id );

	foreach( (array) $tax_terms_to_show_on as $taxonomy => $slugs ) {
		if ( ! is_array( $slugs ) ) {
			$slugs = array( $slugs );
		}

		$terms = $post
			? get_the_terms( $post, $taxonomy )
			: wp_get_object_terms( $post_id, $taxonomy );

		if ( ! empty( $terms ) ) {
			foreach( $terms as $term ) {
				if ( in_array( $term->slug, $slugs, true ) ) {
					wp_die( '<xmp>: '. print_r( 'show it', true ) .'</xmp>' );
					// Ok, show this metabox
					return true;
				}
			}
		}
	}

	return false;
}

