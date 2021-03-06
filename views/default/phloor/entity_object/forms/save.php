<?php
/**
 * Edit form for phloor objects
 */

$guid           = elgg_extract("guid",           $vars, NULL);
$subtype        = elgg_extract("subtype",        $vars, "");
$container_guid = elgg_extract("container_guid", $vars, NULL);

// extract form variables ($name => $input_view)
$form_variables = elgg_extract("form_vars",    $vars, array());

$container = get_entity($container_guid);
$entity    = get_entity($guid);

$new_entity = true;
if ($guid && elgg_instanceof($entity, 'object', $subtype)) {
    $new_entity = false;
    if(!elgg_instanceof($container)) {
        $container_guid = $entity->getContainerGUID();
    }
}
if(!elgg_instanceof($container)) {
    $container_guid = elgg_get_logged_in_user_guid();
}

/** action buttons */
$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));

$delete_link = '';
if (!$new_entity) {
    // add a delete button if editing
    $delete_url = "action/phloor/object/delete?guid={$guid}";
    $delete_link = elgg_view('output/confirmlink', array(
    	'href' => $delete_url,
    	'text' => elgg_echo('delete'),
    	'class' => 'elgg-button elgg-button-delete elgg-state-disabled float-alt'
	));
}

$action_buttons = $save_button . $delete_link;
/** action buttons - end */

$form_content = '';
foreach ($form_variables as $name => $form_params) {
    $input_view        =  elgg_extract('view',        $form_params, NULL);
    $input_value       =  elgg_extract('value',       $form_params, '');
    $input_label       =  elgg_extract('label',       $form_params, '');
    $input_description =  elgg_extract('description', $form_params, '');
    
    // append to form content
    $form_item = elgg_view('phloor/output/form-item', array(
        'view'        => $input_view,
        'value'       => $input_value,
        'label'       => $input_label,
        'description' => $input_description,
        'name'        => $name,
    ));
    
    $form_content .= $form_item;
}

$categories_input = elgg_view('input/categories', array('entity' => $entity));

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array(
	'name'  => 'container_guid',
	'value' => $container_guid,
));
$guid_input = elgg_view('input/hidden', array(
	'name'  => 'guid',
	'value' => $guid,
));

$content = <<<HTML
    $form_content
    $categories_input

    <div class="elgg-foot">
    $guid_input
    $container_guid_input

    $action_buttons
    </div>
HTML;

// output form content
echo $content;

