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
 
elgg.register_hook_handler('init', 'system', phloor.init);
