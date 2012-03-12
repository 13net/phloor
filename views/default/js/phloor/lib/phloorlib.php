<?php 
?>

elgg.provide('phloor');

var phloor = phloor || {};

phloor.global = this;

/**
 * dynamically load a css file
 * @param filename filename
 * @returns
 */
phloor.load_css_file = function(filename) {
  var link = document.createElement("link");
  link.setAttribute("href", filename);
  link.setAttribute("type", "text/css");
  link.setAttribute("rel",  "stylesheet") ;
  
  return document.getElementsByTagName("head")[0].appendChild(link);
};

phloor.init = function() { 
    if ($(".phloor-image-lightbox").length) {
    	$(".phloor-image-lightbox").fancybox({'type': 'image'});
    } 
    
 return true;
}
 
/**
 * loads the css files needed for prettyboxes
 * @param filename filename
 * @returns
 */
phloor.bootPrettycheckboxes = function() {
	var loadcss = phloor.load_css_file(elgg.get_site_url() + "mod/phloor/vendors/prettycheckboxes/prettycheckboxes.css");
	
	return true;
}

/**
 * applies the styles for pretty checkboxes
 * @returns
 */
phloor.initPrettycheckboxes = function() {
	/* see if anything is previously checked and reflect that in the view*/
	$(".checklist input.elgg-input-checkbox:checked").parent().addClass("selected");

	/* handle the user selections */
	$(".checklist a.checkbox-select").click(
		function(event) {
			event.preventDefault();
			$(this).parent().addClass("selected");
			$(this).parent().find(":checkbox").attr("checked","checked");
		}
	);

	$(".checklist a.checkbox-deselect").click(
		function(event) {
			event.preventDefault();
			$(this).parent().removeClass("selected");
			$(this).parent().find(":checkbox").removeAttr("checked");
		}
	);

	return true;
}
 
phloor.initColorPicker = function() {
	if ($(".phloor-colorpicker").length) {		
 		$(".phloor-colorpicker").ColorPicker({
			onSubmit: function(hsb, hex, rgb) {
				$(".phloor-colorpicker").val('#' + hex);
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});
	}
}

elgg.register_hook_handler('boot', 'system', phloor.bootPrettycheckboxes);
elgg.register_hook_handler('init', 'system', phloor.initPrettycheckboxes);

elgg.register_hook_handler('init', 'system', phloor.initColorPicker);
 
 
elgg.register_hook_handler('init', 'system', phloor.init);
