<?php 

elgg_load_css('jquery-colorpicker-css');
elgg_load_js ('jquery-colorpicker-js');


$id    = elgg_extract('id',    $vars, '');
$name  = elgg_extract('name',  $vars, 'color');
$value = elgg_extract('value', $vars, '');
$class = elgg_extract('class', $vars, '');

$class = "phloor-colorpicker $class";

if (empty($id)) {
    $id = phloor_uniqid();
}

echo elgg_view('input/text', array(
    'id'    => $id,
	'name'  => $name,
	'value' => $value,
    'class' => $class,
));

