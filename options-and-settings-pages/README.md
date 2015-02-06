Options and Settings Pages
==========

This class is an example of how you can use CMB2 to create an Admin Theme Options Page. `$this->fields` can be modified to hold your fields array and extended the same way you would in the [`cmb_meta_boxes` filter](https://github.com/WebDevStudios/CMB2/blob/master/example-functions.php). If you want to retrieve an option, use `myprefix_get_option( 'test_text' )`. 

Obviously replace all instances of `myprefix` with a unique project-specific prefix.

[Check these snippets out](https://github.com/WebDevStudios/CMB2-Snippet-Library/tree/master/helper-functions) if you're looking to modify the form output of the `cmb2_metabox_form` function.
