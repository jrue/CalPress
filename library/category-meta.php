<?php
/** 
 * Functions to add additional metadata to post categories as well as other 
 * taxonomies. 
 *
 * Currently we support adding an image to categories, and
 * specifying whether this category is a "photo" category, which we can use
 * in templates to display a special layout. 
 * 
 * This file may be updated since this isn't really 
 * sanctioned by WordPress, and there are lots of different ways 
 * to do this. We took the safest route by using the options table
 * and hooking into a few taxonomy hooks in the WP admin. We're using the
 * newest hook {taxonomy}_edit_form_fields to increase longevity and prevent
 * deprecation. 
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package WordPress
 * @subpackage CalPress
 * @since CalPress 2.0
 */

add_action( 'category_edit_form_fields', 'calpress_edit_category_meta' );
add_action( 'edited_terms', 'calpress_save_category_meta');
add_filter( 'deleted_term_taxonomy', 'calpress_remove_category_meta');

/**
 * Renders the a field for entering the image URL on the EDIT category pages.
 *
 * @since CalPress 2.0
 * @param obj $tag The WP category object passed in. Use term_id key to get the ID.
 * @return void
 */
function calpress_edit_category_meta($tag=NULL){
	$photocategory = $photoURL = false;//initialize variables
	if($tag){//safety feature in case WordPress changes hook 

		if(!$cat_meta = get_option('calpress_category_meta'))
			add_option('calpress_category_meta', array(), '', 'yes');

		if(isset($cat_meta[$tag->term_id]['categorytype']))
			$photocategory = $cat_meta[$tag->term_id]['categorytype'];
			
		if(isset($cat_meta[$tag->term_id]['image']))
			$photoURL = $cat_meta[$tag->term_id]['image'];
			
		//unused. Possibly to make an option to remove sidebar from category pages
		if(isset($cat_meta[$tag->term_id]['show_sidebar']))
			$showsidebar = $cat_meta[$tag->term_id]['show_sidebar'];
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="calpress_category_image"><?php _e('Category Image URL:', 'CalPress'); ?></label></th>
		<td><input name="calpress_category_image" id="calpress_category_image" type="text" value="<?php echo $photoURL ? esc_url($photoURL) : ''; ?>" size="40" aria-required="true" /><br />
		<span class="description"><?php _e('Add the URL of image to associate with this category. You can upload using the Media section.', 'CalPress'); ?></span></td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="parent"><?php _e('Is this a photo category?', 'CalPress'); ?></label></th>
		<td>
			<select name='calpress_category_type' id='calpress_category_type' class='postform' >
				<option class="level-0" value="no" <?php if($photocategory == 'no' || $photocategory == false) echo 'selected="selected"'; ?>><?php _e('Not a photo category', 'CalPress'); ?></option>
				<option class="level-0" value="yes" <?php if($photocategory == 'yes') echo 'selected="selected"'; ?>><?php _e('Display as a photo category', 'CalPress'); ?></option>
			</select>
			<p class="description"><?php _e('We offer a special "look" to categories that are photo-centric. If activated, make sure posts in this category have images.', 'CalPress'); ?></p>
		</td>
	</tr>
<?php 
	}//$tag is set
}

/**
 * Saves category meta data to the options table. Uses category ID as the key pair.
 *
 * @since CalPress 2.0
 * @param int $term_id The numerical id of the category we're saving.
 * @param string $taxonomy The taxonomy of the post. In this case it should be 'category'.
 * @return void
 */
function calpress_save_category_meta($term_id=NULL, $taxonomy=NULL){
	if ( isset( $_POST['calpress_category_image'] ) && isset( $_POST['calpress_category_type']) && $term_id ) {
        $cat_meta = get_option('calpress_category_meta');

		//verify the url is working
		$headers = calpress_get_headers_curl(esc_url($_POST['calpress_category_image']), 5);
		if (strpos($headers[0], '200') || empty($headers[0]))
			$cat_meta[$term_id]['image'] = (string) esc_url($_POST['calpress_category_image']);
			
		$cat_meta[$term_id]['categorytype'] = (string) strip_tags($_POST['calpress_category_type']);
        update_option('calpress_category_meta', $cat_meta );
    }
}

/**
 * If user deletes a category, this will delete the associated meta data with that category.
 *
 * @since CalPress 2.0
 * @param int $term_id The numerical id of the category we're deleting from the options table.
 * @return void
 */
function calpress_remove_category_meta($term_id=NULL) {
  if( isset($_POST['calpress_category_image'])):
    $cat_meta = get_option('calpress_category_meta');
    unset($cat_meta[$term_id]);
    update_option('calpress_category_meta', $cat_meta);
  endif;
}
