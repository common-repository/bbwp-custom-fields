// JavaScript Document

function setSessionBB(key, value){
  if(typeof(Storage) !== "undefined") {
    sessionStorage.setItem(key, value);
  }
}
function getSessionBB(key){
  if(typeof(Storage) !== "undefined")
    return sessionStorage.getItem(key);
  else
    return null;
}

(function( $ ){
  $.fn.BBWPTabbedMenu = function(options) {
	  var settings = $.extend( {
		  active : 0,
      active_session : null,
		  active_link_class : "current-menu-item",
		  active_container_class : "bbwp_current_content_cantainer"
		}, options);

    current_tab = false;
    hash = window.location.hash;
    active_session = getSessionBB("current-nav-item");

    this.find("a").each(function(){
      if($(this).hasClass(settings.active_link_class) && current_tab === false)
        current_tab = $(this).attr("href");
      if($(this).attr("href") == hash){
        current_tab = hash;
        return false;
      }
      if($(this).attr("href") === active_session)
        current_tab = active_session;
    });

    if(!current_tab)
      current_tab = this.find("a").slice(settings.active, settings.active+1).attr("href");

    this.find('a').removeClass(settings.active_link_class);
    this.find('a[href="'+current_tab+'"]').addClass(settings.active_link_class);
    $("."+settings.divclass).removeClass(settings.active_container_class).hide();
		$(current_tab).addClass(settings.active_container_class).show();

	  this.find("a").click(function(e){
		  $(this).parent().find("a").removeClass(settings.active_link_class);
		  $(this).addClass(settings.active_link_class);
		  $("."+settings.divclass).removeClass(settings.active_container_class).hide();
		  current_tab = $(this).attr("href");
		  $(current_tab).addClass(settings.active_container_class).show();
      //window.location.hash=current_tab;
      setSessionBB("current-nav-item", current_tab);
	   	e.stopPropagation(); e.preventDefault(); return false;
	  });
  };
})( jQuery );

jQuery(document).ready(function($){
   $(".bbwp_nav_wrapper").BBWPTabbedMenu({
		 divclass: "bbwp_tab_nav_content",
		 active_link_class:"nav-tab-active"
	 });
	
	if($('input[name="field_select_list_type"]:checked').length >= 1){
		var field_select_list_type_id = $('input[name="field_select_list_type"]:checked').attr('id');
		$(".field_select_list_type").hide();
		$(".field_select_list_type."+field_select_list_type_id).show();
	}
	
	$("input[name='field_select_list_type']").change(function(){
		var field_select_list_type_id = $('input[name="field_select_list_type"]:checked').attr('id');
		$(".field_select_list_type").hide();
		$(".field_select_list_type."+field_select_list_type_id).show();
	});
	
});



jQuery(document).ready(function($) {
	$("select.submit_on_change").change(function(){
		$(this).parents("form").submit();
		return false;
	});
	/*$("#bb-select-all-checkbox").click(function(){
		if($(this))
			$("input[name='"+$(this).attr("data-name")+"[]']")
	});*/

	// jquery for field types class start from here
	$( '.bytebunch-wp-color-picker' ).wpColorPicker();
	$( '.bytebunch-wp-date-picker' ).datepicker({
		changeYear: true
	});
	$( '.bytebunch-wp-sortable' ).sortable({
      placeholder: "ui-state-highlight"
    });

	$(".bb_tagadd").click(function(){
		new_tag_value = $(this).parent().find("input.bb_new_tag").val();
		if(new_tag_value && new_tag_value != "" && new_tag_value != " "){
			new_tag = '<span><input class="regular-text" type="text" value="'+new_tag_value+'" name="'+$(this).parent().find("input.bb_new_tag").attr("data-name")+'[]" /><a href="#" class="bb_dismiss_icon bb_delete_it">&nbsp;</a>';
			new_tag += '</span>';
			$(this).parent().find(".bbtagchecklist").append(new_tag);
			$(this).parent().find("input.bb_new_tag").val('');
		}
		return false;
	});

	$("body").on("click", ".bb_single_image_preview a", function(){
		$(this).parents(".bb_single_image_preview").parent().find("input[type='text']").val("");
		$(this).parent().remove();
		return false;
	});

	$("body").on("click", ".bb_delete_it", function(){
		$(this).parent().remove();
		return false;
	});
// jquery for field types class end here

});

/* *********************** */
/* wordpress image uploader */
/* *********************** */

jQuery(document).ready(function($) {
   var bytebunch_wp_uploader;
	 var bb_multiple_wp_uploader;

	 // single file uploader
    $(".bytebunch_file_upload_button").on("click",function(e){
			inputobject = $(this).parent().find("input[type='text']");
			e.preventDefault();

			// If the media frame already exists, reopen it.
			if (bytebunch_wp_uploader) {
				bytebunch_wp_uploader.open();
				return;
			}

			 // Create a new media frame
			bytebunch_wp_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose File',
				button: {
					text: 'Choose File'
				},
				multiple: false
			});

			//When a file is selected, grab the URL and set it as the text field's value
			bytebunch_wp_uploader.on('select', function() {
				attachment = bytebunch_wp_uploader.state().get('selection').first().toJSON();
				if(inputobject.parent().find(".bb_single_image_preview").length >= 1){
					inputobject.parent().find(".bb_single_image_preview").html('<span><img src="'+attachment.url+'" /><a href="#" class="bb_dismiss_icon">&nbsp;</a></span>');
				}
				inputobject.val(attachment.url);
			});

			//Open the uploader dialog
			bytebunch_wp_uploader.open();
			return false;
	});

	// multiple file uploader
	$(".bytebunch_multiple_upload_button").on("click",function(e){
		inputobject = $(this);
		e.preventDefault();

		if (bb_multiple_wp_uploader) {
			bb_multiple_wp_uploader.open();
			return;
		}

		bb_multiple_wp_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Files',
			button: {
				text: 'Choose Files'
			},
			multiple: true
		});

		bb_multiple_wp_uploader.on('select', function() {
			attachments = bb_multiple_wp_uploader.state().get('selection').toJSON();
			if(attachments.constructor === Array){
				if(inputobject.parent().find(".bb_multiple_images_preview").length >= 1){
					$.each(attachments, function( index, value ) {
						output_html = '<span><img src="'+value.url+'" /><a href="#" class="bb_delete_it bb_dismiss_icon">&nbsp;</a><input type="hidden" name="'+inputobject.attr("data-name")+'[]" value="'+value.url+'" /></span>';
						//console.log(value.id);
						inputobject.parent().find(".bb_multiple_images_preview").prepend(output_html);
					});
				}
			}
		});

		bb_multiple_wp_uploader.open();
		return false;
	});

  if($(".bytebunch_admin_page_container").length >= 1){
    postboxes.save_state = function(){
        return;
    };
    postboxes.save_order = function(){
        return;
    };
    postboxes.add_postbox_toggles();
  }


});
