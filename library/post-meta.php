<?php
/**
 * Post meta box to add multimedia when writing posts and pages.
 *
 *
 * Copyright (c) 2012 The Regents of the University of California
 * Released under the GPL Version 2 license
 * http://www.opensource.org/licenses/gpl-2.0.php
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package WordPress
 * @subpackage CalPress2
 * @since CalPress 0.9.7
 */

add_action( 'add_meta_boxes', 'calpress_add_post_meta_box' );
add_action( 'save_post', 'calpress_save_postdata' );

/**
 * Adds meta box to the posts section
 *
 * @uses add_meta_box()
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_add_post_meta_box() {
	global $post;
	
    add_meta_box( 
        'calpress_multimedia_options',
        __( 'Multimedia', 'calpress' ),
        'calpress_custom_meta_box',
        'post',
		'normal',
		'high'
    );

    add_meta_box( 
        'calpress_multimedia_options',
        __( 'Multimedia', 'calpress' ),
        'calpress_custom_meta_box',
        'page',
		'normal',
		'high'
    );
}

/**
 * Prints the form fields for meta box on posts page
 *
 * @uses wp_nonce_field()
 * @global obj $calpress_supported_multimedia 
 * @param obj $post The current post object
 * @param obj $postmeta Passed in post metadata to fill field values
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_custom_meta_box( $post ) {
	global $calpress_supported_multimedia;
	
	$post_custom = get_post_custom($post->ID, true);
	
	$maximum_lead_art_items = 10;
	
	//set defaults if previous values aren't set
	foreach($calpress_supported_multimedia as $key => $value):
		$args[$key] = array($value['default_value']);
		$args['lead_art_caption_'.$key] = array('');
	endforeach;
	
	
	//default settings for inline and lead art
	$args['lead_art'] = array('image');
	$args['inline_art'] = array('none', 'none', 'none', 'none', 'none', 'none', 'none', 'none', 'none', 'none');
	$args['inline_art_title'] = array('', '', '', '', '', '', '', '', '', '');
	$args['inline_art_caption'] = array('', '', '', '', '', '', '', '', '', '');
	$args['inline_art_media'] = array('', '', '', '', '', '', '', '', '', '');

	
	//if legacy support is activated, peform a few functions to support older posts, and replace above as needed.
	if(calpress_legacy_support()){
		if(!isset($post_custom['lead_art'])){
			$args = calpress_legacy_set_lead_art($post_custom, $args, $post->post_date);
		}
	}
	
	//Have to serialize the data here because add_post_meta serializes arrays
	$args['inline_art'] = array(serialize($args['inline_art']));
	$args['inline_art_title'] = array(serialize($args['inline_art_title']));
	$args['inline_art_caption'] = array(serialize($args['inline_art_caption']));
	$args['inline_art_media'] = array(serialize($args['inline_art_media']));
	
	//sets defaults if previous values arent set
	$options = wp_parse_args($post_custom, $args); 
	
	//wordpress serializes arrays in post meta
	$options['inline_art'] = unserialize($options['inline_art'][0]);
	$options['inline_art_title'] = unserialize($options['inline_art_title'][0]);
	$options['inline_art_caption'] = unserialize($options['inline_art_caption'][0]);
	$options['inline_art_media'] = unserialize($options['inline_art_media'][0]);

	?>
	
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function($){
			
			$('#lead_art_picker').change(function(){
				$('.lead_media').hide();
				$('.lead_art_caption').css('display', 'none');
				$('#' + $(this).val() + '_lead_art_caption').css('display', 'table-row');
				$('.' + $(this).val()).show();
			});
			
			$('.calpress_inline_art').each(function(index){
				$(this).change(function(){
					if($(this).val() != 'none' && $(this).val() != 'image'){
						$('.inline_fields_'+index).show();
						$('.inline_fields_'+index+' .description').remove();
						$('.inline_fields_'+index+':last td > *').after(descriptions[$(this).val()]);
					} else {
						$('.inline_fields_'+index).hide();
					}
				});
			});
			
		});
		
		var descriptions = new Array();
		<?php foreach($calpress_supported_multimedia as $key => $value): ?>
		descriptions['<?php echo $key; ?>'] = '<p class="description"><?php echo addslashes(stripslashes($value['description'])); ?></p>';
		<?php endforeach; ?>

	</script>
	<div id="lead_art_section" style="border:1px solid #dfdfdf; background:#eee; margin-bottom:20px;">
 	<table class="form-table">
		<tbody>
			<!-- LEAD ART PICKER -->
			<tr valign="top">
				<th scope="row"><label for="lead_art_picker">Pick a media type to use for the lead artwork to appear on this post</label></th>
				<td>
					<select name="lead_art" id="lead_art_picker">
						<?php foreach($calpress_supported_multimedia as $key => $value): ?>
							<?php if($value['lead']): ?>
							<option value="<?php echo $key ?>" <?php selected($options['lead_art'][0], $key); ?>><?php echo $value['field_label']; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<span id="multimedia_lead_message"></span>
				</td>
			</tr>
			<!-- LEAD ART MEDIA FORMS -->
			<?php foreach($calpress_supported_multimedia as $key => $value): ?>
				<?php if($value['render_form'] && $value['lead']): ?>
					<?php $caption = isset($options['lead_art_caption_'.$key][0]) ? esc_attr($options['lead_art_caption_'.$key][0]) : ''; ?>
				<tr valign="top" class="<?php echo $key . " "; ?>lead_media" <?php if($options[$key][0] == '') echo 'style="display:none;" '; ?>>
					<th scope="row"><label for="<?php echo $key; ?>"><?php echo $value['field_label']; ?>:</label></th>
					<td>
						<?php call_user_func_array($value['render_form'], array(implode(' ', $options[$key]), $key, $key )); ?>
						<p class="description"><?php if(isset($value['description'])) echo $value['description']; ?></p>
					</td>
				</tr>
				<?php if($value['caption_label'] != ''): ?>
				<tr valign="top" id="<?php echo $key . '_lead_art_caption'; ?>" <?php if($options[$key][0] == '') echo ' style="display:none;" '; ?>class="lead_art_caption">
					<th scope="row"><label for="<?php echo $key; ?>_caption"><?php echo $value['caption_label']; ?></label></th>
					<td>
						<input type="text" id="<?php echo $key; ?>_caption" name="lead_art_caption_<?php echo $key; ?>" value="<?php echo $caption; ?>" size="60">
						<p class="description">Enter a caption that will appear below this media.</p>
					</td>
				</tr>
				<?php endif; //has caption label ?>
				<?php endif; //has render form ?>
			<?php endforeach; //end calpress_supported_multimedia ?>
		</tbody>
	</table>	
	</div>
	
	<div id="inline_art_section" style="border:1px solid #dfdfdf; background:#eee; margin-bottom:20px;">
	<table class="form-table" id="inline_form">
			<!-- INLINE ART MEDIA FORMS -->
			<?php for($i=0; $i<$maximum_lead_art_items; $i++): ?>
			<tbody<?php if($i > 0 && $options['inline_art'][$i] == 'none') echo ' style="display:none;"'; ?> id="inline_art_media_<?php echo $i; ?>">
				<tr valign="top">
					<th scope="row">Choose inline artwork <?php echo $i+1; ?></th>
					<td>
						<select name="inline_art[<?php echo $i; ?>]" class="calpress_inline_art" id="inline_picker_<?php echo $i; ?>">
							<?php foreach($calpress_supported_multimedia as $key => $value): ?>
								<?php if($value['inline']): ?>
									<option value="<?php echo $key; ?>" <?php selected($options['inline_art'][$i], $key); ?>><?php echo $value['field_label']; ?></option><br />
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="inline_fields_<?php echo $i; ?>"<?php if($options['inline_art'][$i] == 'none' || $options['inline_art'][$i] == 'image') echo ' style="display:none;" '; ?>>
					<th scope="row"><label for="inline_art_title_<?php echo $i; ?>">Title: </label></th>
					<td><input type="text" size="60" name="inline_art_title[<?php echo $i; ?>]" id="inline_art_title_<?php echo $i; ?>" value="<?php echo $options['inline_art_title'][$i] ?>" /></td>
				</tr>
				<tr valign="top" class="inline_fields_<?php echo $i; ?>"<?php if($options['inline_art'][$i] == 'none' || $options['inline_art'][$i] == 'image') echo ' style="display:none;" '; ?>>
					<th scope="row"><label for="inline_art_caption_<?php echo $i; ?>">Caption: </label></th>
					<td><input type="text" size="60" name="inline_art_caption[<?php echo $i; ?>]" id="inline_art_caption_<?php echo $i; ?>" value="<?php echo $options['inline_art_caption'][$i] ?>" /></td>
				</tr>
				<tr valign="top" class="inline_fields_<?php echo $i; ?>"<?php if($options['inline_art'][$i] == 'none' || $options['inline_art'][$i] == 'image') echo ' style="display:none;" '; ?>>
					<th scope="row"><label for="inline_art_media_<?php echo $i; ?>">Media: </label></th>
					<td><textarea size="60" class="large-text code" rows="2" name="inline_art_media[<?php echo $i; ?>]" id="inline_art_media_<?php echo $i; ?>"><?php echo esc_textarea($options['inline_art_media'][$i]); ?></textarea></td>
				</tr>
				<?php if($i < ($maximum_lead_art_items-1)): ?>
				<tr valign="top" colspan="2">
					<th scope="row"><a href="#" onclick="jQuery('#inline_art_media_<?php echo $i + 1; ?>').toggle(); return false;">Add another inline element</a></th>
				</tr>
				<?php endif; ?>
			</tbody>
			<?php endfor; ?>
	</table>
	</div>
	
	<!-- ADVANCED OPTIONS FORMS -->
	<h4 style="background:none; border:none;"><a href="#" onclick="jQuery('#advanced_options_section').toggle(); return false;">Advanced Options</a></h4>
	<div id="advanced_options_section" style="border:1px solid #dfdfdf; background:#eee; margin-bottom:20px; display:none;">
 	<table class="form-table">
		<tbody>
		<?php foreach($calpress_supported_multimedia as $key => $value): ?>
			<?php if($value['render_form'] && ($value['advanced'])): ?>
			<tr valign="top">
				<th scope="row"><label for="<?php echo $key; ?>"><?php echo $value['field_label']; ?></label></th>
				<td>
					<?php call_user_func_array($value['render_form'], array(implode(' ', $options[$key]), $key, $key )); ?>
					<p class="description"><?php if(isset($value['description'])) echo $value['description']; ?></p>
				</td>
			</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
			
<?php
}

/**
 * Saves posts meta data into the database
 *
 * @uses add_post_meta()
 * @global obj $calpress_supported_multimedia 
 * @since CalPress 0.9.7
 * @return void
 */
function calpress_save_postdata( $post_id ) {
	global $calpress_supported_multimedia;
	
	//count error messages
	$error_messages = array();
	
	//tags allowed for captions
	$allowed_tags = array(
		'a' => array(
			'href' => array(), 
			'title'=> array(), 
			'class'=>array(),
			'style'=>array()
			),
		'em' => array(),
		'strong' => array(),
		'b' => array(),
		'i' => array(), 
		'span' => array(
			'style' => array(),
			'class' => array()
		)
	);
 
  //don't autosave
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // Check permissions
  if ( !current_user_can( 'edit_post', $post_id ) )
       return;

  if ( !wp_is_post_revision( $post_id ) && isset($_POST['lead_art'])):

		//clear out previous values from DB if the user removed them from the fields
		foreach($calpress_supported_multimedia as $key => $value):
			if($value['render_form'] && $value['lead'] && isset($_POST[$key])):
				if(($_POST[$key] == '' || $_POST['lead_art'] == 'none' || $_POST['lead_art'] != $key) && $value['lead']){
					delete_post_meta($post_id, $key);
					delete_post_meta($post_id, 'lead_art_caption_' . $key);
				}
			endif;
		endforeach;
		
		/*========= LEAD ART PROCESSING =========== */
		
		//get values of the media type based on what the user picked for lead_art
		$lead_art['media_object'] = $calpress_supported_multimedia->$_POST['lead_art'];
		
		if(!$lead_art['media_object']['render_form'])://if no user input was required
			
			$lead_art['value'] = call_user_func_array($lead_art['media_object']['sanitize_output'], array($_POST['lead_art'], $post_id));
			
			if($lead_art['value']){
				add_post_meta($post_id, 'lead_art', $_POST['lead_art'], true) or 
				update_post_meta( $post_id, 'lead_art', $_POST['lead_art'] );
			} else {
				add_post_meta($post_id, 'lead_art', 'none', true) or 
				update_post_meta( $post_id, 'lead_art', 'none' );
			}
			
		else: //has a form for user input
			
			if($_POST[$_POST['lead_art']] == ''){
				
				add_post_meta($post_id, 'lead_art', 'none', true) or 
				update_post_meta( $post_id, 'lead_art', 'none' );
				
				delete_post_meta($post_id, 'lead_art_caption_' . $_POST['lead_art']);
				
			} else {
				
				if(isset($_POST['override']) && $_POST['override'] == "true"){
					$lead_art['value'] = $_POST[$_POST['lead_art']];
				} else {
					$lead_art['value'] = call_user_func_array($lead_art['media_object']['sanitize_output'], array($_POST[$_POST['lead_art']] , $post_id));
				}
		
				if($lead_art['value']){
			
					//set the lead art picker
					add_post_meta($post_id, 'lead_art', $_POST['lead_art'], true) or 
					update_post_meta( $post_id, 'lead_art', $_POST['lead_art'] );
			
					//add actual user input data for the media
					add_post_meta($post_id, $_POST['lead_art'], $lead_art['value'], true) or
					update_post_meta($post_id, $_POST['lead_art'], $lead_art['value']);
					
					//add caption
					if($_POST['lead_art_caption_' . $_POST['lead_art']] != ''){
						add_post_meta($post_id, 'lead_art_caption_' . $_POST['lead_art'], wp_kses($_POST['lead_art_caption_' . $_POST['lead_art']], $allowed_tags), true) or 
						update_post_meta( $post_id, 'lead_art_caption_' . $_POST['lead_art'], wp_kses($_POST['lead_art_caption_' . $_POST['lead_art']], $allowed_tags) );
					} else {
						delete_post_meta($post_id, 'lead_art_caption_' . $_POST['lead_art']);
					}
			
				} else {
					add_post_meta($post_id, 'lead_art', 'none', true) or 
					update_post_meta( $post_id, 'lead_art', 'none' );
					$error_messages[] = $_POST['lead_art'];
				}
			}
			
		endif;
		
		/*========= INLINE ART PROCESSING =========== */
		
		//setup arrays for storing $_POST values.
		$inline_art_setting = array();
		$inline_art_title = array();
		$inline_art_caption = array();
		$inline_art_media = array();
		
		//only process items that have inline art option set
		foreach($_POST['inline_art'] as $key => $inline_art):
			
			//get the settings for this inline art item (multimedia.php)
			$inline_object = $calpress_supported_multimedia->$inline_art;
			
			if($inline_art != 'none'):
				
				if(isset($_POST['override']) && $_POST['override'] == "true"){ //allow user to override checks
					$inline_art_media[$key] = $_POST['inline_art_media'][$key];
				} else {
					if($inline_object['sanitize_output'] != false){
						$inline_art_media[$key] = call_user_func_array($inline_object['sanitize_output'], array($_POST['inline_art_media'][$key], $post_id));
					} else {
						$inline_art_media[$key] = $_POST['inline_art_media'][$key];
					}
				}
				
				//if sanitize function returned false generate error message
				if(!$inline_art_media[$key]){
					$error_messages[] = $inline_art;
					$inline_art_media[$key] = '';
					$inline_art_setting[$key] = 'none';
				} else {
					$inline_art_setting[$key] = (string) $_POST['inline_art'][$key];
				}
				
				$inline_art_caption[$key] = wp_kses($_POST['inline_art_caption'][$key], $allowed_tags);
				$inline_art_title[$key] = strip_tags($_POST['inline_art_title'][$key]);
				

			else: //inline art is set to none
				
				//zero out these fields if set to none
				$inline_art_setting[$key] = 'none';
				$inline_art_title[$key] = ''; 
				$inline_art_caption[$key] = '';
				$inline_art_media[$key] = '';
				
			endif;
		endforeach;

		add_post_meta($post_id, 'inline_art', $inline_art_setting, true) or 
		update_post_meta( $post_id, 'inline_art', $inline_art_setting );
		
		add_post_meta($post_id, 'inline_art_title', $inline_art_title, true) or 
		update_post_meta($post_id, 'inline_art_title', $inline_art_title);
		
		add_post_meta($post_id, 'inline_art_caption', $inline_art_caption, true) or 
		update_post_meta($post_id, 'inline_art_caption', $inline_art_caption);
		
		add_post_meta($post_id, 'inline_art_media', $inline_art_media, true) or 
		update_post_meta($post_id, 'inline_art_media', $inline_art_media);
		
		/*========= ADVANCED PROCESSING =========== */
		foreach($calpress_supported_multimedia as $key => $value):
			if($value['render_form'] && $value['advanced']):
				if(!isset($_POST[$key]) || $_POST[$key] == ''){
					delete_post_meta($post_id, $key);
				} else {
				
					if(isset($_POST['override']) && $_POST['override'] == "true"){
						$advanced = $_POST[$key];
					}elseif($value['sanitize_output']){
						$advanced = call_user_func_array($value['sanitize_output'], array($_POST[$key], $post_id));
					} else {
						$advanced = $_POST[$key];
					}
				
					if($advanced){
						add_post_meta($post_id, $key, $advanced, true) or 
						update_post_meta($post_id, $key, $advanced);
					} else {
						delete_post_meta($post_id, $key);
						$error_messages[] = $key;
					}
				}
				
			endif;
		endforeach;
		
		//show error messages if they exists
		if(count($error_messages) > 0)
			add_filter('redirect_post_location', create_function('$loc', 'return add_query_arg("media_error", "'. implode(',', $error_messages) .'", $loc);'));
	
  endif;

}

/**
 * Displays error messages if the query string has them.
 *
 * @since CalPress 0.9.7
 * @global obj $calpress_supported_multimedia 
 * @param array $messages The core messages from WordPress
 * @return array Send the new array back to WordPress core
 */
function calpress_show_error_messages($messages){
	global $calpress_supported_multimedia;
	//display error messages from a previous save
	if(isset($_GET['media_error'])){
		
		$errors = explode(",", $_GET['media_error']);
		$error_message = '';
		
		foreach($errors as $error):
			if(isset($calpress_supported_multimedia->$error)){
				$msg = $calpress_supported_multimedia->$error;
				$error_message .= '<div class=\"error\"><p>' . $msg['error_message'] . '</p></div>';
			}
		endforeach;
		
		add_action('admin_notices', create_function('', 'echo "' . $error_message .'";'));
		
	}
	
	return $messages;
}
add_action('post_updated_messages', 'calpress_show_error_messages');


?>