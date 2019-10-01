<?php

/**
 * CMB2 Multicheck by Post Type
 *
 * @package CMB2 Default Tags field/metabox
 * @author Daniele Mte90 Scasciafratte
 */

if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CMB2_Field_Textarea_With_Checkbox' ) ) {
  /**
   * Class CMB2_Field_Textarea_With_Checkbox
   */
  class CMB2_Field_Textarea_With_Checkbox  {

    /**
     * Current version number
     */
    const VERSION = '1.0.0';
    /**
     * Initialize the plugin
     */
    public function __construct() {
       add_action( 'cmb2_render_textarea_with_checkbox', [$this, 'render_textarea_with_checkbox'], 10, 5 );
       add_filter( 'cmb2_sanitize_textarea_with_checkbox', [$this, 'sanitize_textarea_with_checkbox'], 10, 5 );
       add_filter( 'cmb2_types_esc_textarea_with_checkbox', [$this, 'escape_textarea_with_checkbox'], 10, 4 );
    }

    public function render_textarea_with_checkbox( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
      // the properties of the fields.
      $field_escaped_value = wp_parse_args( $field_escaped_value, [
        'text'	=> '',
        'status'	=> '',
      ] );
      $checked = false;
      if ( ! empty( $field_escaped_value['status'] ) ) {
        $checked = true;
      }
      ?>
      <div style="overflow: hidden;">
        <?= $field_type_object->textarea( [
          'name' => $field_type_object->_name( '[text]' ),
          'id' => $field_type_object->_id( '_text' ),
          'value' => $field_escaped_value['text'],
        ] ); ?>
      </div>
      <div style="overflow: hidden">
        <p><label for="<?= $field_type_object->_id( '_status' ); ?>"><?= $field_type_object->field->args('title_checkbox'); ?></label></p>
        <?= $field_type_object->checkbox( [
          'type' => 'checkbox',
          'name' => $field_type_object->_name( '[status]' ),
          'id' => $field_type_object->_id( '_status' ),
        ], $checked ); ?>
      </div>
      <?php
      echo $field_type_object->_desc( true );
    }

    /**
     * Sanitize Field.
     */
    public static function sanitize_textarea_with_checkbox( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
      if ( !is_array( $meta_value ) || !( array_key_exists('repeatable', $field_args ) && $field_args['repeatable'] == TRUE ) ) {
        return $check;
      }
      
      $new_values = array();
      foreach ( $meta_value as $key => $val ) {
        if( !empty( $meta_value[$key]['text'] ) ) {
            $new_values[$key] = array_filter( array_map( 'sanitize_text_field', $val ) );
        }
      }
      
      return array_filter( array_values( $new_values ) );
    }

    /**
     * Escape Field.
     */
    public static function escape_textarea_with_checkbox( $check, $meta_value, $field_args, $field_object ) {
      if ( !is_array( $meta_value ) || ! $field_args['repeatable'] ) {
        return $check;
      }

      $new_values = array();
      foreach ( $meta_value as $key => $val ) {
        if( !empty( $meta_value[$key]['text'] ) ) {
            $new_values[$key] = array_filter( array_map( 'esc_attr', $val ) );
        }
      }
      
      return array_filter( array_values( $new_values ) );
    }
  }

  $cmb2_field_textarea_with_checkbox = new CMB2_Field_Textarea_With_Checkbox();
}
