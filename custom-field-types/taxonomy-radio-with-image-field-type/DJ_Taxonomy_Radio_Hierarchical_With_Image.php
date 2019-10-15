<?php

/**
 * Handles 'taxonomy_radio_with_image' custom field type.
 */
class DJ_Taxonomy_Radio_Hierarchical_With_Image extends CMB2_Type_Taxonomy_Radio_Hierarchical {
	protected $term;
	protected $saved_term;

	/**
	 * Build children hierarchy.
	 *
	 * @param  object       $parent_term The parent term object.
	 * @param  array|string $saved       Array of terms set to the object, or single term slug.
	 *
	 * @return string                    List of terms.
	 */
	protected function build_children( $parent_term, $saved ) {
		if ( empty( $parent_term->term_id ) ) {
			return '';
		}

		$this->parent = $parent_term->term_id;

		$terms   = $this->get_terms();
		$options = '';

		if ( ! empty( $terms ) && is_array( $terms ) ) {
			// DJ - BEGIN
			$options = '<ul class="cmb2-indented-hierarchy">';
			$options .= $this->loop_terms( $terms, $saved );
			$options .= '</ul>';
			// DJ - END
		}

		return $options;
	}

	protected function list_term_input( $term, $saved_term ) {
		$this->term = $term;
		$this->saved_term = $saved_term;
		return parent::list_term_input( $term, $saved_term );
	}

	public function list_input( $args = array(), $i ) {
		if ( empty( $this->term ) ) {
			return parent::list_input( $args, $i );
		}

		$a = $this->parse_args( 'list_input', array(
			'type'  => 'radio',
			'class' => 'cmb2-option',
			'name'  => $this->_name(),
			'id'    => $this->_id( $i ),
			'value' => $this->field->escaped_value(),
			'label' => '',
		), $args );

		$taxonomy  = $this->field->args( 'taxonomy' );
		$image     = '';
		$is_parent = '';

		$image_url = isset( $this->term->term_id ) ? get_term_meta( $this->term->term_id, 'yourprefix_category_avatar', true ) : '';

		if ( ! empty( $image_url ) && $this->term->parent == 0 ) {
			$image = '<img style="max-width: 30px; height: auto; vertical-align: middle; margin-right: 5px;" src="'.$image_url.'" alt="'.$a['label'].'" />';
		} else {
			$image = '';
		}

		$atts = $this->concat_attrs( $a, array( 'label' ) );
		if ( isset( $this->term->term_id ) && get_term_children( $this->term->term_id, $taxonomy ) ) {
			$is_parent = 'class="parent"';
			return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span>%s</span></label></span>' . "\n", $is_parent, $atts, $a['id'], $image, $a['label'] );
		} else {
			return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span>%s</span></label></span></li>' . "\n", $is_parent, $atts, $a['id'], $image, $a['label'] );
		}

	}

}
