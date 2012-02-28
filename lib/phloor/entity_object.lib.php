<?php
/*****************************************************************************
 * Phloor                                                                    *
 *                                                                           *
 * Copyright (C) 2012 Alois Leitner                                          *
 *                                                                           *
 * This program is free software: you can redistribute it and/or modify      *
 * it under the terms of the GNU General Public License as published by      *
 * the Free Software Foundation, either version 2 of the License, or         *
 * (at your option) any later version.                                       *
 *                                                                           *
 * This program is distributed in the hope that it will be useful,           *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 * GNU General Public License for more details.                              *
 *                                                                           *
 * You should have received a copy of the GNU General Public License         *
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.     *
 *                                                                           *
 * "When code and comments disagree both are probably wrong." (Norm Schryer) *
 *****************************************************************************/

/**
 * Library for entity handling
 *
 * plugin hooks:
 *  - "phloor_object_default_vars", $subtype
 *    this hook fetches all default vars of an object
 *  - phloor_object_check_vars
 *	  this hook checks the variables of an object for validness
 *
 * @author void
 */
namespace phloor\entity\object;

/**
* Puts a subtype under the control of phloor
*
* @param string $subtype
*/
function phloor_my_subtype($subtype) {
    // check if the subtype exists
    $subtype_id = get_subtype_id('object', $subtype);
    if ($subtype_id === false) {
        return false;
    }

    // check the class
    $entity_class = get_subtype_class_from_id($subtype_id);
    if (!class_exists($entity_class)) {
        return false;
    }

    if(!namespace\add_object_subtype($subtype)) {
        return false;
    }

    // an entitiy handler to reroute to the phloor view the page "/phloor/object/$subtype/view/$guid"
    elgg_register_entity_url_handler('object', $subtype, __NAMESPACE__ . '\default_object_url_handler');

    return true;
}

/**
 * Array with subtypes handles and managed by phloor
 * 
 * @global bool $PHLOOR_ENTITY_OBJECT_SUBTYPES
 * @access private
 */
$PHLOOR_ENTITY_OBJECT_SUBTYPES = NULL;

/**
 * Adds an object subtype to a list of subtypes
 * known and handled by phloor
 * 
 * @param string $subtype
 */
function add_object_subtype($subtype) {
    // check if the subtype exists
    $subtype_id = get_subtype_id('object', $subtype);
    if ($subtype_id === false) {
        return false;
    }
    
    // check the class
    $entity_class = get_subtype_class_from_id($subtype_id);
    if (!class_exists($entity_class)) {
        return false;
    }
     
    global $PHLOOR_ENTITY_OBJECT_SUBTYPES;
    if (!is_array($PHLOOR_ENTITY_OBJECT_SUBTYPES)) {
        $PHLOOR_ENTITY_OBJECT_SUBTYPES = array();
    }

    $PHLOOR_ENTITY_OBJECT_SUBTYPES[$subtype] = $entity_class;
    
    return true;
}

/**
 * Check if objects with the given subtype
 * are under the control of phloor
 * 
 * @param string $subtype
 * 
 * @return bool
 */
function is_object_subtype($subtype) {     
    global $PHLOOR_ENTITY_OBJECT_SUBTYPES;
    if (!is_array($PHLOOR_ENTITY_OBJECT_SUBTYPES)) {
        return false;
    }

    //return array_key_exists($subtype, $PHLOOR_ENTITY_OBJECT_SUBTYPES);
    $class_name = elgg_extract($subtype, $PHLOOR_ENTITY_OBJECT_SUBTYPES, NULL);
    
    if (!class_exists($class_name)) {
        return false;
    }
    
    return true;
}


/**
 * Get the class of an entity that
 * is controlled by phloor.
 *
 * @param string $subtype
 *
 * @return bool
 */
function get_subtype_class($subtype) {
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }
     
    global $PHLOOR_ENTITY_OBJECT_SUBTYPES;
    $class_name = elgg_extract($subtype, $PHLOOR_ENTITY_OBJECT_SUBTYPES, NULL);
    if (!class_exists($class_name)) {
        return false;
    }
    
    return $class_name;
}

/**
 * Format and return the URL for phloor objects.
 *
 * @param ElggObject $entity 
 * @return string URL of entity object
 */
function default_object_url_handler($entity) {
    if (!$entity->getOwnerEntity()) {
        return FALSE;
    }
    
    $subtype = $entity->getSubtype();
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }

    $friendly_title = elgg_get_friendly_title($entity->title);

    return "phloor/object/$subtype/view/{$entity->guid}/$friendly_title";
}

/**
 * Returns the default attributes of an subtype controlled
 * by phloor. Triggers the plugin hook
 * 'phloor_object_default_vars' for that specific subtype.
 *
 * @param string $subtype
 * 
 * @return array with default values
 */
function default_vars($subtype, $guid = NULL) {
    // check if the subtype exists
    /*$subtype_id = get_subtype_id('object', $subtype);
    if ($subtype_id === false) {
        return false;
    }*/
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }

	$defaults = array(
	    'guid'           => NULL,
	    'access_id'      => ACCESS_PUBLIC,
		'owner_guid'     => elgg_get_logged_in_user_guid(),
		'container_guid' => elgg_get_logged_in_user_guid(),
	    'comments_on'    => 'Off',
	);

	$params = array(
	    'user'    => elgg_get_logged_in_user_entity(),
	    'type'    => 'object',
	    'subtype' => $subtype,
	    'guid'    => $guid, // may be NULL
	);

	// invoke plugin hook to fetch custom default values for an entity of a specific subtype
	$options = elgg_trigger_plugin_hook('phloor_object:default_vars', $subtype, $params, $defaults);
	// merge options with default values
	$return = array_merge($defaults, $options);
	
	return $return;
}



/**
 * Returns the input vars of an subtype controlled
 * by phloor. 
 * This function can currently only set the attributes
 * that are returned in the "default_vars" hook.
 *
 * @param string $subtype
 * 
 * @return array with input values
 */
function get_input_vars($subtype) {
    // check if the subtype exists
    /*$subtype_id = get_subtype_id('object', $subtype);
    if ($subtype_id === false) {
        return false;
    }*/
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }

    // get default values
	$defaults = namespace\default_vars($subtype);

	$user = elgg_get_logged_in_user_entity();

	$params = array();
	foreach ($defaults as $key => $default_value) {
	    $value = get_input($key, $default_value);
		switch ($key) {
			// get the image from $_FILES array
			/*case 'image':
				//$params['image'] = $_FILES['image'];
				$params['image'] = elgg_extract("image", $_FILES, array());
				break;*/
			case 'container_guid':
				// this can't be empty or saving the base entity fails
				if (!empty($value)) {
					if (can_write_to_container($user->getGUID(), $value)) {
						$params['container_guid'] = $value;
					}
				}
				break;
			// don't try to set the guid
			case 'guid':
				unset($params['guid']);
				break;
			default:
				$params[$key] = $value;
				break;
		}
	}

	return $params;
}

/**
 * Stores the values of the $params array
 * as attributes of the given $entity.
 * 
 * Calls 'check_vars' which invokes
 * a plugin hook to manipulate the
 * saving process (break up on return value
 * FALSE)
 * 
 * triggers events
 * - 'phloor_object:save:before'
 * - 'phloor_object:save:after'
 * 
 * @return bool true on successful save
 */
function save_vars($entity, $params = array()) {    
    // store errors to pass along
    $error = FALSE;
    $error_forward_url = REFERER;
    
    if (!elgg_instanceof($entity, 'object')) {
        //register_error('phloor:entity:object:save_vars2');
        return false;
    }

    $subtype = $entity->getSubtype(); // extract the subtype
    // check is subtype is controlled by phloor
    if (!namespace\is_object_subtype($subtype)) {
        return false; // BREAK UP OTHERWISE
        // this function is not allowed
        // to save other types of entities!
    }
    
    // save the "outdated" values of the entity in $old_entity
    $old_entity = clone $entity;
    $old_entity->guid = $entity->guid; // clone omits guid
    
	// get default values
	$defaults = namespace\default_vars($subtype);

    // check the variables 
    $vars = namespace\check_vars($entity, array_merge($defaults, $params));
    
    if ($vars === false) {
        $error = elgg_echo('phloor:error:check_vars:return:false');
    }
    else {
        // adopt variables to ENTITY
        foreach ($vars as $key => $value) {
            if (FALSE === ($entity->$key = $value)) {
                $error = elgg_echo('phloor:error:entity:save:attribute_error' . "{$key} = {$value}");
                break;
            }
        }      
    }

    if ($error) {
        register_error($error);
        forward($error_forward_url);
        return false;
    }
    
    
    $options = array(
    	'entity' => $entity,
    );
    
    // trigger save:before event
    elgg_trigger_event('phloor_object:save:before', $subtype, $entity);
    
    if (!$entity->save()) {
        return false;
    }
    
    // pass old values via volatile data of the entity
    // because one cannot provide additional parameter to an event
    $entity->setVolatileData('old_entity', $old_entity);
    
    // trigger save:before event
    elgg_trigger_event('phloor_object:save:after', $subtype, $entity);

	// save site and return status
	return true;
}

/**
 * Checks if the given parameters
 * are valid attributes values
 * for a given entity.
 * 
 * The $param array is a reference to
 * optionally manipulate invalid errors.
 * 
 * @param unknown_type $entity
 * @param array $params
 * 
 * @return true if values are applicable
 */
function check_vars($entity, &$params) {
    if (!elgg_instanceof($entity, 'object')) {
        return false;
    }

    // extract the subtype
    $subtype = $entity->getSubtype();
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }
    
    $hook_params = array(
        'entity'  => $entity,
        'type'    => 'object',
        'subtype' => $subtype,
		'params'  => $params, // save orginal params
    );

    // trigger plugin hook to check variables of an object with a given subtype
    $return = elgg_trigger_plugin_hook('phloor_object:check_vars', $subtype, $hook_params, $params);
    if (!is_array($return)) {
        return false;
    }

	return $return;
}

/**
 * Return variables used in save form.
 * $return will be filled with the standard values
 *
 * Calls the hooks 'phloor_object_form_vars'
 * and 'phloor_object_prepare_form_vars'.
 * 
 * In 'phloor_object_form_vars' one can define volatile data which
 * is not stored directy in the entity but used otherwise
 *  or
 * set attributes from another source than the default variables!
 * (e.g. fetch something from the GET params , etc.)
 * 
 * @param string $subtype
 * @param int    $guid
 */
function form_vars($subtype, $guid = NULL) {
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }
          
    $new_entity = TRUE;
    $entity = get_entity($guid);
    if (elgg_instanceof($entity, 'object', $subtype)) {
        $new_entity = FALSE;
    }
    
    // get default values
    $default_variables = namespace\default_vars($subtype);
    
    // options for the phloor_object_form_vars hook
    $options = array(
		'guid'         => $guid,
		'entity'       => $entity,
		'type'         => 'object',
		'subtype'      => $subtype,
        'default_vars' => $default_variables,
    );
  
    // trigger plugin hook for fetching the form variables
    $form_variables = elgg_trigger_plugin_hook('phloor_object:form_vars', $subtype, $options, array());
    
    $sticky_values = array();
    if (elgg_is_sticky_form($subtype)) {
        $sticky_values = elgg_get_sticky_values($subtype);
    }
    
    $return = array();
    foreach ($form_variables as $name => $form_params) {
        // if $form_params is not an array, it is an the $input_view
        if (!is_array($form_params)) {
            $form_params = array('view' => $form_params);
        }      
        
        $input_view =  elgg_extract('view', $form_params, NULL);
        // check fi the input view exists and raise an
        // error but CONTINUE if it does not.
        if (!elgg_view_exists($input_view)) {
            register_error(elgg_echo('phloor:error:view:not_found', array($input_view)));
            continue;
        }       
        
        // take the value from the entity if it exists
        if (!$new_entity) {
            $input_value = $entity->get($name);
        }
        else {
            $input_value =  elgg_extract('value', $form_params, '');
            
            // override the value with the sticky form value
            $sticky_value = elgg_extract($name, $sticky_values, NULL);
            if (isset($sticky_value)) {
                $input_value = $sticky_value;
            }
            
            // IF NO VALUE IS SET.. TAKE THE DEFAULT VALUE
            if(empty($input_value)) {
                $input_value = elgg_extract($name, $default_variables, NULL);
            }
        
        }
         
        // get label and set default label is not defined otherwise
        $input_label =  elgg_extract('label', $form_params, '');
        if (empty($input_label)) {
            $input_label = elgg_echo("phloor/object/$subtype:form:$name");
        }
         
        // display the desciption of the attribute (optional string, often empty)
        $input_description =  elgg_extract('description', $form_params, '');
        if (empty($input_description)) {
            $input_description = elgg_echo("phloor/object/$subtype:form:$name:description");
            // inset the description if no language key was found
            if (strcmp("phloor/object/$subtype:form:$name:description", $input_description) == 0) {
                $input_description = ' ';
            }
        }
        
        // add to return array with key $name
        $return[$name] = array(
            'view'        => $input_view,
            'value'       => $input_value,
            'description' => $input_description,
            'label'       => $input_label,
        );
             
    }
    
    /**
    * Prepare form vars.
    *
    */
    $options['form_vars'] = $return; // original params
    
    // trigger plugin hook for fetching the form variables
    $return = elgg_trigger_plugin_hook('phloor_object:prepare_form_vars', $subtype, $options, $return);
    
    foreach ($return as $name => $form_params) {
        // by now the $form_params should really be an array
        if (!is_array($form_params)) {
            register_error(elgg_echo('phloor:error:form:form_params:!is_array'));
            unset($return[$name]);
            continue;
        }       
    }
    
    return $return;
}

/**
 * Retrieve entities of a given subtype.
 * 
 * @param string $subtype subtype of the entity (type is "object")
 * @param array $params override or add additional query params
 * 
 * @return array of entities
 */
function get_entities($subtype, $params = array()) {
	// check if subtype exists
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }

    $defaults = array(
		'type'    => 'object',
		'subtype' => $subtype,
		'offset'  => (int) max(get_input('offset', 0), 0),
		'limit'   => (int) max(get_input('limit', 10), 0),
    );

    $options = array_merge($defaults, $params);

    return elgg_get_entities_from_metadata($options);
}

function get_entities_by_container($subtype, $container_guid = 0, $params = array()) {
	// check if subtype exists
    if (!namespace\is_object_subtype($subtype)) {
        return false;
    }

    if (elgg_entity_exists($container_guid)) {
        $params['container_guid'] = $container_guid;
    }

    $entities = namespace\get_entities($subtype, $params);

    return $entities;
}


/**
 * setup phloors entity handling system
 * loads and boots all necessary functions
 * 
 * @access private
 */
function phloor_entity_object_boot() {

	$lib_path = elgg_get_plugins_path() . 'phloor/lib/phloor/';

	elgg_register_library('phloor-entity-object-page-handler-defaults-lib',
	                      $lib_path . 'entity_object/page_handler_defaults.lib.php');
	
	elgg_register_library('phloor-entity-object-action-defaults-lib',
	                      $lib_path . 'entity_object/action_defaults.lib.php');
	
	elgg_register_library('phloor-entity-object-menu-defaults-lib',
	                      $lib_path . 'entity_object/menu_defaults.lib.php');
	
	elgg_register_library('phloor-entity-object-event-defaults-lib',
	                      $lib_path . 'entity_object/event_defaults.lib.php');

	elgg_load_library('phloor-entity-object-page-handler-defaults-lib');
	elgg_load_library('phloor-entity-object-action-defaults-lib');
	elgg_load_library('phloor-entity-object-menu-defaults-lib');
	elgg_load_library('phloor-entity-object-event-defaults-lib');

	// invoke the boot process the libraries
	\phloor\entity\object\page_handler\defaults\phloor_entity_object_page_handler_defaults_boot();
	\phloor\entity\object\actions\defaults\phloor_entity_object_action_defaults_boot();
    \phloor\entity\object\entity_menu\defaults\phloor_entity_object_menu_defaults_boot();
    \phloor\entity\object\events\defaults\phloor_entity_object_event_defaults_boot();
    
    return true;
}

