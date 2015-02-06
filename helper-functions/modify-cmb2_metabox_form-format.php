<?php

/**
 * To change the formatting of the form,
 * again, pass the third parameter (the arguments array),
 * and specify the 'form_format' param
 *
 * In our case, we're modifying the save button text,
 * and we're giving the save button a secondary button class
 */
cmb2_metabox_form( $meta_box, $object_id, array(
    'form_format' => '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'your-textdomain' ) . '" class="button-secondary"></div></form>',
) );
