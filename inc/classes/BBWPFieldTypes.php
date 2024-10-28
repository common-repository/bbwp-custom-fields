<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWPFieldTypes{

  private $prefix = "";
  private $saveType = "option";
  private $dataID = '';
  private $displaytype = array("wrapper_open" => '<table class="form-table">', 'wrapper_close' => '</table>', 'container_open' => '<tr>', 'container_close' => '</tr>', 'label_open' => '<th scope="row">', 'label_close' => '</th>', 'input_open' => '<td>', 'input_close' => '</td>');

  public function __construct($prefix = ""){
    if(isset($prefix) && $prefix && is_string($prefix))
      $this->prefix = $prefix;
    /*$this->displaytype = array(
      "wrapper_open" => '<div class="form-wrap">',
      'wrapper_close' => '</div>',
      'container_open' => '<div class="form-field">',
      'container_close' => '</div>',
      'label_open' => '',
      'label_close' => '',
      'input_open' => '',
      'input_close' => ''
    );*/
  }// construct function end here

  /******************************************/
  /***** AddNewFields function start from here *********/
  /******************************************/
  public function AddNewFields($edit_field = false){
    $input_values = array();
    if($edit_field){
      $existing_values = SerializeStringToArray(get_option($this->prefix));
      if($existing_values && is_array($existing_values) && array_key_exists($edit_field, $existing_values)){
        $input_values = $existing_values[$edit_field];
        echo '<input type="hidden" name="update_field" value="'.esc_attr($edit_field).'">';
      }else{
        update_option("bbwp_update_message", __("Meta Key has been updated or doesn't exist.", 'bbwp-custom-fields'));
        echo '<script>window.location.replace("'.admin_url('admin.php?page='.$_GET['page']).'");</script>';
      }
    }else
      echo '<input type="hidden" name="update_field" value="new">';
    ?>
    <input type="hidden" name="bb_field_types_save" value="<?php echo esc_attr($this->prefix("bb_field_types_save")); ?>">
    <div style="float:left;" class="form-wrap" id="col-left">
      <div class="form-field">
        <label for="field_title"><?php _e('Field Title', 'bbwp-custom-fields'); ?> <span class="require_star">*</span></label>
        <?php $selected_value = ""; if(isset($input_values['field_title'])){ $selected_value = $input_values['field_title']; } ?>
        <input type="text" name="field_title" id="field_title" class="regular-text" value="<?php echo esc_attr($selected_value); ?>" required="required">
      </div>
      <div class="form-field">
        <label for="meta_key"><?php _e('Meta Key', 'bbwp-custom-fields'); ?> <span class="require_star">*</span></label>
        <?php $selected_value = ""; if(isset($input_values['meta_key'])){ $selected_value = $input_values['meta_key']; } ?>
        <input type="text" name="meta_key" id="meta_key" class="regular-text" value="<?php echo esc_attr($selected_value); ?>" required="required">
      </div>
      <div class="form-field">
        <label for="field_type"><?php _e('Field Type', 'bbwp-custom-fields'); ?> <span class="require_star">*</span></label>
        <select name="field_type" id="field_type" class="<?php echo $this->prefix("field_type"); ?>" required="required">
          <?php
          $selected_value = ""; if(isset($input_values['field_type'])){ $selected_value = $input_values['field_type']; }
          $types = array(
						'text' => 'Text',
						'number' => 'Number',
            'editor' => 'Editor',
            'image' => 'Image',
            'file' => 'Files',
            'textarea' => 'Text Area',
            'color' => 'Color Picker',
            'date' => 'Date Picker',
            'checkbox_list' => 'Check Box List',
            'checkbox' => 'Check Box',
            'select' => 'Select List',
            'password' => 'Password',
						'radio' => 'Radio Buttons',
						'hidden' => 'Hidden'
          );
          echo ArraytoSelectList($types, $selected_value);
          ?>

        </select>
      </div>
      <div class="form-field">
        <label for="field_description"><?php _e('Help Text', 'bbwp-custom-fields'); ?></label>
        <?php $selected_value = ""; if(isset($input_values['field_description'])){ $selected_value = $input_values['field_description']; } ?>
        <textarea name="field_description" id="field_description" cols="30" rows="5" class="regular-text"><?php echo $selected_value; ?></textarea>
        <p class="description"><?php _e('Tell to the user about what is the field', 'bbwp-custom-fields'); ?></p>
      </div>
      <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'bbwp-custom-fields'); ?>"></p>
  	</div> <!-- style="width:50%; float:left;"  -->
    <div class="form-wrap" id="col-right" style="float:right;">
        <div class="options_of_fields" style="padding:20px; background-color:#fff;">
          <h3 style="margin:0 0 20px 0px;"><?php _e('Options of field', 'bbwp-custom-fields'); ?></h3>
					<p><?php _e('By default on this box will be displayed a information about custom fields, after the custom field be selected, this box will be displayed some extra options of the field (if required) or a information about the selected field', 'bbwp-custom-fields'); ?></p>
          
					<!-- Custom Post types -->
					<div class="hidden_fields select form-field" style="display:none;">
						<?php 
						$selected_value = 'field_is_custom_select_list';
            	if(isset($input_values['field_select_list_type']) && $input_values['field_select_list_type']){
              	$selected_value = $input_values['field_select_list_type'];
            }
						$field_is_custom_select_list = array(
							array('id' => 'field_is_custom_select_list', 'value' => 'field_is_custom_select_list', 'label' => __('Custom List', 'bbwp-custom-fields')), 
							array('id' => 'field_is_post_types', 'value' => 'field_is_post_types', 'label' => __('Post Types', 'bbwp-custom-fields')),
							array('id' => 'field_is_taxonomies', 'value' => 'field_is_taxonomies', 'label' => __('Taxonomies', 'bbwp-custom-fields'))
						);
						echo '<div class="bb_checkboxes_container">';
						echo ArraytoRadioList($field_is_custom_select_list, 'field_select_list_type', $selected_value);
						echo '</div>';
						?>
          </div>
					
					<!-- Choices -->
					<div class="hidden_fields checkbox_list select radio form-field" style="display:none;">
						<div class="d-none field_select_list_type field_is_custom_select_list">
							<label for="field_type_values"><?php _e('Choices', 'bbwp-custom-fields'); ?>: </label>
							<?php $selected_value = ""; if(isset($input_values['field_type_values'])){ $selected_value = implode("\n", $input_values['field_type_values']); } ?>
							<textarea name="field_type_values" id="field_type_values" cols="30" rows="5" class="regular-text"><?php echo $selected_value; ?></textarea>
							<p class="description"><?php _e('Enter each choice on a new line.', 'bbwp-custom-fields'); ?></p>
						</div>
						<div class="d-none field_select_list_type field_is_post_types">
						<?php	
						$post_types_list = array();
						$selected_value = array();
            if(isset($input_values['field_post_types']) && is_array($input_values['field_post_types'])){
              $selected_value = $input_values['field_post_types'];
            }
						
						$args = array('public' => true);
            $post_types = get_post_types( $args, 'names' );
            foreach ( $post_types as $post_type ) {
              if($post_type == 'attachment')
                continue;
							$post_types_list[$post_type] = array('id' => $post_type, 'value' => $post_type, 'label' => ucfirst(str_ireplace(array("-","_"), array(" ", " "), $post_type)));
            }
						
						echo '<div class="bb_checkboxes_container">';
						echo ArraytoCheckBoxList($post_types_list, 'field_post_types', $selected_value);
						echo '</div>';
						?>
						</div>
						<div class="d-none field_select_list_type field_is_taxonomies">
						<?php	
						$taxonomies_list = array();
						$selected_value = array();
            if(isset($input_values['field_taxonomies']) && is_array($input_values['field_taxonomies'])){
              $selected_value = $input_values['field_taxonomies'];
            }
						
            $taxonomies = get_taxonomies($args);
            foreach ( $taxonomies as $taxonomy ) {
              if($taxonomy == 'post_format')
                continue;
							$taxonomies_list[$taxonomy] = array('id' => $taxonomy, 'value' => $taxonomy, 'label' => ucfirst(str_ireplace(array("-","_"), array(" ", " "), $taxonomy)));
            }
						
						echo '<div class="bb_checkboxes_container">';
						echo ArraytoCheckBoxList($taxonomies_list, 'field_taxonomies', $selected_value);
						echo '</div>';
						?>
						</div>
          </div><!-- hidden_fields-->
					
					<!-- Default Value -->
          <div class="hidden_fields text color select radio form-field number hidden">
            <label for="default_value"><?php _e('Default Value', 'bbwp-custom-fields'); ?>: </label>
            <?php $selected_value = ""; if(isset($input_values['default_value'])){ $selected_value = $input_values['default_value']; } ?>
            <input type="text" name="default_value" id="default_value" class="regular-text" value="<?php echo esc_attr($selected_value); ?>" />
          </div>
					
					<!-- Can be duplicated -->
          <div class="hidden_fields text image form-field">
            <label for="field_duplicate" style="display:inline-block;"><?php _e('Can be duplicated', 'bbwp-custom-fields'); ?>: </label>
            <?php $selected_value = ""; if(isset($input_values['field_duplicate'])){ $selected_value = $input_values['field_duplicate']; } ?>
            <input type="checkbox" name="field_duplicate" id="field_duplicate" <?php if($selected_value === 'on'){ echo 'checked="checked"'; } ?> />
          </div>
					
					
					<div class="hidden_fields textarea editor form-field">
            <label for="field_allow_all_code" style="display:inline-block;"><?php _e('Allow all types of code', 'bbwp-custom-fields'); ?>: </label>
            <?php $selected_value = ""; if(isset($input_values['field_allow_all_code'])){ $selected_value = $input_values['field_allow_all_code']; } ?>
            <input type="checkbox" name="field_allow_all_code" id="field_allow_all_code" <?php if($selected_value === 'on'){ echo 'checked="checked"'; } ?> />
          </div>
					
					<!-- Disable wpautop -->
					<div class="hidden_fields textarea editor form-field">
            <label for="field_disable_autop" style="display:inline-block;"><?php _e('Disable wpautop', 'bbwp-custom-fields'); ?>: </label>
            <?php $selected_value = ""; if(isset($input_values['field_disable_autop'])){ $selected_value = $input_values['field_disable_autop']; } ?>
            <input type="checkbox" name="field_disable_autop" id="field_disable_autop" <?php if($selected_value === 'on'){ echo 'checked="checked"'; } ?> />
          </div>

        </div>
    </div>
    <div class="clearboth"></div>
    <script>
    jQuery(document).ready(function($) {
      $(".options_of_fields .hidden_fields").hide();
      var bb_field_type_value = $("select.<?php echo $this->prefix('field_type'); ?>").val();
      $(".options_of_fields ."+bb_field_type_value).show();
      $("select.<?php echo $this->prefix('field_type'); ?>").change(function(){
        bb_field_type_value = $(this).val();
        $(".options_of_fields .hidden_fields").hide();
        $(".options_of_fields ."+bb_field_type_value).show();
      });
    });
    </script>
  <?php }

  /******************************************/
  /***** DeleteField function start from here *********/
  /******************************************/
  static function DeleteFields($meta_key, $db_key){
    $existing_values = SerializeStringToArray(get_option($db_key));
    if($existing_values && is_array($existing_values) && count($existing_values) >= 1){
      if(isset($meta_key) && is_array($meta_key) && count($meta_key) >= 1){
        foreach($meta_key as $value){
          if($value && array_key_exists($value, $existing_values))
            unset($existing_values[$value]);
        }
      }
      elseif(isset($meta_key) && $meta_key && array_key_exists($meta_key, $existing_values)){
        unset($existing_values[$meta_key]);
      }
      update_option($db_key, ArrayToSerializeString($existing_values));
      update_option("bbwp_update_message", __('Your setting have been updated.', 'bbwp-custom-fields'));
    }
  }

  /******************************************/
  /***** SortFields function start from here *********/
  /******************************************/
  static function SortFields($newValues, $db_key){
    $existing_values = SerializeStringToArray(get_option($db_key));
    if(is_array($existing_values) && count($existing_values) >= 1 && isset($newValues) && is_array($newValues) && count($newValues) >= 1 ){
      $new_values = array();
      foreach($newValues as $value){
        if($value && array_key_exists($value, $existing_values)){
          $new_values[$value] = $existing_values[$value]; }
      }
      if(count($existing_values) == count($new_values)){
        update_option($db_key, ArrayToSerializeString($new_values));
        update_option("bbwp_update_message", __('Your setting have been updated.', 'bbwp-custom-fields'));
      }
    }
  }

  /******************************************/
  /***** UpdateFields function start from here *********/
  /******************************************/
  public function UpdateFields(){

    if(isset($_POST['bb_field_types_save']) && $_POST['bb_field_types_save'] === $this->prefix("bb_field_types_save")){
      if(isset($_POST['field_title']) && $_POST['field_title'] && isset($_POST['meta_key']) && $_POST['meta_key'] && isset($_POST['field_type']) && $_POST['field_type'])
      {
        $value = BBWPSanitization::Textfield($_POST['field_title']);
        $key = sanitize_key($_POST['meta_key']);
        $type = sanitize_key($_POST['field_type']);
        $existing_values = SerializeStringToArray(get_option($this->prefix));

        if(isset($_POST["update_field"]) && $_POST["update_field"] && array_key_exists($_POST["update_field"], $existing_values)){
          unset($existing_values[$_POST["update_field"]]);
        }

        if(array_key_exists($key, $existing_values)){
          update_option("bbwp_error_message",  'This meta key is already exist. Please choose new meta key or delete the old one first.');
          return;
        }

        if($value && $key && $type){

          $update_message = __('Your setting have been updated.', 'bbwp-custom-fields');

          if(isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET['page']) && isset($_GET['meta_key']) && array_key_exists($key, $existing_values)){
            $update_message = '<p>'.__('Your setting have been updated.', 'bbwp-custom-fields').'</p>';
          }


          $new_field_values = array();
          $new_field_values['meta_key'] = $key;
          $new_field_values['field_title'] = $value;
          $new_field_values['field_type'] = $type;

          if(isset($_POST["default_value"])){
            $default_value = BBWPSanitization::Textfield($_POST["default_value"]);
            if($default_value)
              $new_field_values['default_value'] = $default_value;
          }
          else
            $new_field_values['default_value'] = "";

          if(isset($_POST['field_description'])){
            $field_description = BBWPSanitization::Textfield($_POST["field_description"]);
            if($field_description)
              $new_field_values['field_description'] = $field_description;
          }

          $new_field_values['field_duplicate'] = '';
          if(isset($_POST['field_duplicate'])){
            $new_field_values['field_duplicate'] = 'on';
          }

					$new_field_values['field_allow_all_code'] = '';
          if(isset($_POST['field_allow_all_code'])){
            $new_field_values['field_allow_all_code'] = 'on';
          }

					$new_field_values['field_disable_autop'] = '';
          if(isset($_POST['field_disable_autop'])){
            $new_field_values['field_disable_autop'] = 'on';
          }



          if(($type == "checkbox_list" || $type == "select" || $type == "radio") && isset($_POST["field_type_values"]) && $_POST["field_type_values"])
          {
            $field_type_values = BBWPSanitization::Textarea($_POST["field_type_values"]);
            if($field_type_values)
              $new_field_values['field_type_values'] = array_values(array_filter(explode("\n", str_replace("\r", "", $field_type_values))));
            else{
              update_option("bbwp_error_message",  'There was some problem with '.$type.' values. Please try again.');
              return;
            }
          }

          $existing_values[$key] = $new_field_values;
          update_option($this->prefix, ArrayToSerializeString($existing_values));
          update_option("bbwp_update_message", $update_message);
          if(isset($new_field_values['default_value']) && $this->saveType === "option")
            update_option($new_field_values['meta_key'], $new_field_values['default_value']);
        }
        else
          update_option("bbwp_error_message", 'There was some problem. Please try again with different meta key name.');
      }
    }
  }

  /******************************************/
  /***** DisplayOptions function start from here *********/
  /******************************************/
  public function DisplayOptions(){
    $existing_values = SerializeStringToArray(get_option($this->prefix));
    if(isset($existing_values) && $existing_values && count($existing_values) >= 1){
      //db($existing_values);
      echo '<input type="hidden" name="'.$this->prefix('update_options').'" value="'.$this->prefix('update_options').'" />';
      echo $this->displaytype['wrapper_open'];

      foreach($existing_values as $value){

				if($value['field_type'] != 'hidden')
        	echo $this->displaytype['container_open'];

        $field_description = '';
        if(isset($value['field_description']))
          $field_description = '<p class="description">'.$value['field_description'].'</p>';

				if($value['field_type'] != 'hidden')
        	echo $this->displaytype['label_open'].'<label for="'.$value['meta_key'].'">'.$value['field_title'].'</label>'.$field_description.$this->displaytype['label_close'].$this->displaytype['input_open'];

				$default_value = "";
				$selected_value = "";
        if(isset($value['default_value']) && $value['default_value'])
          $default_value = $value['default_value'];

        if($this->saveType === "option")
          $selected_value = get_option($value['meta_key']);
        elseif($this->saveType === "user" && is_numeric($this->dataID) && $this->dataID >= 1)
          $selected_value = get_user_meta($this->dataID, $value['meta_key'], true);
        elseif($this->saveType === "post" && is_numeric($this->dataID) && $this->dataID >= 1)
          $selected_value = get_post_meta($this->dataID, $value['meta_key'], true);
        elseif($this->saveType === "term" && is_numeric($this->dataID) && $this->dataID >= 1)
          $selected_value = get_term_meta($this->dataID, $value['meta_key'], true);
        elseif($this->saveType === "comment" && is_numeric($this->dataID) && $this->dataID >= 1)
          $selected_value = get_comment_meta($this->dataID, $value['meta_key'], true);

        if(!(isset($selected_value) && $selected_value))
          $selected_value = $default_value;
        if(isset($value['field_duplicate']) && $value['field_duplicate'] == 'on'){
          $selected_value = SerializeStringToArray($selected_value);
        }

        if($value['field_type'] == 'text' || $value['field_type'] == 'password' || $value['field_type'] == 'number' || $value['field_type'] == 'hidden'){
          if(isset($value['field_duplicate']) && $value['field_duplicate'] == 'on'){
            echo '<div><input type="text" class="field_duplicate regular-text bb_new_tag" data-name="'.$value['meta_key'].'" />
            <input type="button" class="button tagadd bb_tagadd" value="Add"><div class="bbtagchecklist input_bbtagchecklist">';
            if($selected_value && is_array($selected_value) && count($selected_value) >= 1){
              foreach ($selected_value as $field_type_value) {
                echo '<span><input type="text" value="'.esc_attr($field_type_value).'" name="'.$value['meta_key'].'[]" class="regular-text" /><a href="#" class="bb_delete_it bb_dismiss_icon">&nbsp;</a></span>';
              }
            }
            echo '</div></div>';
          }
          else
            echo '<input type="'.$value['field_type'].'" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" value="'.esc_attr($selected_value).'" class="regular-text">';
				}
        elseif($value['field_type'] == 'image'){
          if(isset($value['field_duplicate']) && $value['field_duplicate'] == 'on'){
            //<p class="description">You can use Ctrl+Click to select multiple images from media library.</p>
            echo '<input type="button" id="" class="bytebunch_multiple_upload_button button" value="Select Images" data-name="'.$value['meta_key'].'">';
            echo '<div class="bb_multiple_images_preview bb_image_preview">';
            if($selected_value && is_array($selected_value) && count($selected_value) >= 1){
              foreach ($selected_value as $field_type_value) {
                echo '<span><img src="'.$field_type_value.'"><a href="#" class="bb_dismiss_icon bb_delete_it">&nbsp;</a><input type="hidden" name="'.$value['meta_key'].'[]" value="'.esc_attr($field_type_value).'" /></span>';
              }
            }
            echo '<div class="clearboth"></div></div>';
          }else{
            echo '<input type="text" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" value="'.esc_attr($selected_value).'" class="regular-text">
            <input type="button" id="" class="bytebunch_file_upload_button button" value="Select Image">';
            echo '<div class="bb_single_image_preview bb_image_preview">';
            if($selected_value){
              echo '<span><img src="'.$selected_value.'"><a href="#" class="bb_dismiss_icon">&nbsp;</a></span>';
            }
            echo '<div class="clearboth"></div></div>';
          }

        }
        elseif($value['field_type'] == 'file'){
          echo '<input type="text" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" value="'.esc_attr($selected_value).'" class="regular-text">
              <input type="button" id="" class="bytebunch_file_upload_button button" value="'.__('Upload File', 'bbwp-custom-fields').'">';
        }
        elseif($value['field_type'] == 'editor'){
          $setting = array('textarea_rows' => 10, 'textarea_name' => $value['meta_key'], 'teeny' => false, 'tinymce' => true, 'quicktags' => true);
          wp_editor($selected_value, $value['meta_key'], $setting);
        }
        elseif($value['field_type'] == 'textarea'){
          echo '<textarea name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" rows="5">'.$selected_value.'</textarea>';
        }
        elseif($value['field_type'] == 'color'){
          echo '<input type="text" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" value="'.esc_attr($selected_value).'" class="bytebunch-wp-color-picker regular-text">';
        }
        elseif($value['field_type'] == 'date'){
          echo '<input type="text" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" value="'.esc_attr($selected_value).'" class="bytebunch-wp-date-picker regular-text">';
        }
        elseif($value['field_type'] == 'select'){
          echo '<select name="'.$value['meta_key'].'" id="'.$value['meta_key'].'">';
          foreach($value['field_type_values'] as $field_type_value){
            if($field_type_value == $selected_value)
              echo '<option value="'.esc_attr($field_type_value).'" selected="selected">'.esc_html($field_type_value).'</option>';
            else
              echo '<option value="'.esc_attr($field_type_value).'">'.esc_html($field_type_value).'</option>';
          }
          echo '</select>';
        }
        elseif($value['field_type'] == 'radio'){
          foreach($value['field_type_values'] as $key=>$field_type_value){
            if($field_type_value == $selected_value)
              echo ' <input type="radio" id="'.$value['meta_key'].$key.'" value="'.esc_attr($field_type_value).'" name="'.$value['meta_key'].'" checked="checked" /> <label for="'.$value['meta_key'].$key.'">'.esc_html($field_type_value).'</label> ';
            else
              echo ' <input type="radio" id="'.$value['meta_key'].$key.'" value="'.esc_attr($field_type_value).'" name="'.$value['meta_key'].'" /> <label for="'.$value['meta_key'].$key.'">'.esc_html($field_type_value).'</label> ';
            echo '&nbsp;&nbsp;';
          }
        }
        elseif($value['field_type'] == 'checkbox'){
          if($selected_value)
            echo '<input type="'.$value['field_type'].'" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'" checked="checked">';
          else
            echo '<input type="'.$value['field_type'].'" name="'.$value['meta_key'].'" id="'.$value['meta_key'].'">';
        }
        elseif($value['field_type'] == 'checkbox_list'){
          $selected_value = SerializeStringToArray($selected_value);
          if(!($selected_value && is_array($selected_value)))
            $selected_value = array();
          foreach($value['field_type_values'] as $key=>$field_type_value){
            if(in_array($field_type_value, $selected_value))
              echo ' <input type="checkbox" id="'.$value['meta_key'].$key.'" value="'.esc_attr($field_type_value).'" name="'.$value['meta_key'].'[]" checked="checked" /> <label for="'.$value['meta_key'].$key.'">'.esc_html($field_type_value).'</label> ';
            else
              echo ' <input type="checkbox" id="'.$value['meta_key'].$key.'" value="'.esc_attr($field_type_value).'" name="'.$value['meta_key'].'[]" /> <label for="'.$value['meta_key'].$key.'">'.esc_html($field_type_value).'</label> ';
            echo '&nbsp;&nbsp;';
          }
				}
				if($value['field_type'] != 'hidden'){
					echo $this->displaytype['input_close'];
					echo $this->displaytype['container_close'];
				}
      }
      echo $this->displaytype['wrapper_close'];
    }
  }

  /******************************************/
  /***** SaveOptions function start from here *********/
  /******************************************/
  public function SaveOptions(){
    $existing_values = SerializeStringToArray(get_option($this->prefix));
    if(isset($existing_values) && $existing_values && count($existing_values) >= 1){
      if(isset($_POST[$this->prefix("update_options")]) && $_POST[$this->prefix("update_options")] === $this->prefix("update_options"))
      {
        foreach($existing_values as $value){
          $dbvalue = "";
          if(isset($_POST[$value['meta_key']]) && $_POST[$value['meta_key']]){
            if(is_array($_POST[$value['meta_key']]) && count($_POST[$value['meta_key']]) >= 1){
              $dbvalue = array();
              foreach($_POST[$value['meta_key']] as $selected_value){
                $selected_value = BBWPSanitization::Textfield($selected_value);
                if($selected_value)
                  $dbvalue[] = $selected_value;
              }
            }
            else{
                if($value['field_type'] == 'textarea' || $value['field_type'] == 'editor'){
									if(isset($value['field_allow_all_code']) && $value['field_allow_all_code'] && $value['field_allow_all_code'] == 'on'){
										if(isset($value['field_disable_autop']) && $value['field_disable_autop'] && $value['field_disable_autop'] == 'on')
											$dbvalue = wptexturize(BBWPSanitization::Textarea($_POST[$value['meta_key']], true));
										else
											$dbvalue = wptexturize(wpautop(BBWPSanitization::Textarea($_POST[$value['meta_key']], true)));
									}else{
										if(isset($value['field_disable_autop']) && $value['field_disable_autop'] && $value['field_disable_autop'] == 'on')
											$dbvalue = wptexturize(BBWPSanitization::Textarea($_POST[$value['meta_key']]));
										else
											$dbvalue = wptexturize(wpautop(BBWPSanitization::Textarea($_POST[$value['meta_key']])));
									}

								}
                else{
                  $dbvalue = BBWPSanitization::Textfield($_POST[$value['meta_key']]); }
            }
          }
          else{
            if(isset($value['default_value']))
              $dbvalue = $value['default_value'];
          }

          if(is_array($dbvalue))
            $dbvalue = ArrayToSerializeString($dbvalue);

          if($this->saveType === "option")
              update_option($value['meta_key'], $dbvalue);
          elseif($this->saveType === "user" && is_numeric($this->dataID) && $this->dataID >= 1)
              update_user_meta($this->dataID, $value['meta_key'], $dbvalue);
          elseif($this->saveType === "post" && is_numeric($this->dataID) && $this->dataID >= 1)
            update_post_meta($this->dataID, $value['meta_key'], $dbvalue);
          elseif($this->saveType === "term" && is_numeric($this->dataID) && $this->dataID >= 1)
              update_term_meta($this->dataID, $value['meta_key'], $dbvalue);
          elseif($this->saveType === "comment" && is_numeric($this->dataID) && $this->dataID >= 1)
              update_comment_meta($this->dataID, $value['meta_key'], $dbvalue);
        }

        if($this->saveType == "option")
          update_option("bbwp_update_message", __('Your setting have been updated.', 'bbwp-custom-fields'));
      }
    }
  }

  /******************************************/
  /***** Set function start from here *********/
  /******************************************/
  public function Set($property, $value = NULL){
    if(isset($property) && $property){
      if(isset(self::$$property))
        self::$$property = $value;
      else
        $this->$property = $value;
    }
  }

  /******************************************/
  /***** prefix function start from here *********/
  /******************************************/
  public function prefix($string = '', $underscore = "_"){
    return $this->prefix.$underscore.$string;
  }

}
