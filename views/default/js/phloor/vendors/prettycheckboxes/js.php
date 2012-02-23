<?php

?>
elgg.provide('phloor.prettycheckboxes');

elgg.require('phloor');

phloor.prettycheckboxes.boot = function() {
	var loadcss = phloor.load_css_file(elgg.get_site_url() + "mod/phloor/vendors/prettycheckboxes/prettycheckboxes.css");
	
	return true;
}

/**
 * dynamically load a css file
 * @param filename filename
 * @returns
 */
phloor.prettycheckboxes.init = function() {
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
 
elgg.register_hook_handler('boot', 'system', phloor.prettycheckboxes.boot);
elgg.register_hook_handler('init', 'system', phloor.prettycheckboxes.init);
