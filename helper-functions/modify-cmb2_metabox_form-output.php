<?php
/**
 * You can use the 'cmb2_get_metabox_form_format' filter to modify the form format,
 * but keep in mind it will affect the form format for all uses of
 * the cmb2_metabox_form, cmb2_print_metabox_form, and cmb2_get_metabox_form functions,
 * so you should make sure you're looking at the right form.
 *
 * If you're using the options-page example,
 * (https://github.com/WebDevStudios/CMB2-Snippet-Library/tree/master/options-and-settings-pages)
 * You would do it like this:
 */

/**
 * Modify CMB2 Default Form Output
 *
 * @param  string  $form_format Form output format
 * @param  string  $object_id   In the case of an options page, this will be the option key
 * @param  object  $cmb         CMB2 object. Can use $cmb->cmb_id to retrieve the metabox ID
 *
 * @return string               Possibly modified form output
 */
function myprefix_options_modify_cmb2_metabox_form_format( $form_format, $object_id, $cmb ) {

    if ( 'myprefix_options' == $object_id && 'option_metabox' == $cmb->cmb_id ) {

        $form_format = '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'myprefix' ) . '" class="button-primary"></div></form>';
    }

    return $form_format;
}
add_filter( 'cmb2_get_metabox_form_format', 'myprefix_options_modify_cmb2_metabox_form_format', 10, 3 );
