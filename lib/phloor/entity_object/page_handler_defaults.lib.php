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
 * Default page handler implementations
 * 
 * These functions are invoked when a plugin author 
 * decides to let entity pages be handled by phloor.
 * 
 * Everything in here is private and should
 * not be called by other plugin authors.
 * 
 * @author void
 * @access private
 */
namespace phloor\entity\object\page_handler\defaults;

/**
 * 
 * @param array $params
 * 
 * @return options array to view a page
 * 
 * @access private
 */
function page_owner($params) {
    $owner = elgg_extract("user", $params, NULL);
	if (!elgg_instanceof($owner, 'user')) {
	    return false;
	}

    $subtype = elgg_extract("subtype", $params, NULL);
    $options = namespace\page_content_list($subtype, $owner->guid);

	$options['layout'] = 'content';

	return $options;
}

/**
 *
 * @param array $params
 *
 * @return options array to view a page
 *
 * @access private
 */
function page_group($params) {
    $group = elgg_extract("group", $params, NULL);
	if (!elgg_instanceof($group, 'group')) {
	    return false;
	}

    $subtype = elgg_extract("subtype", $params, NULL);
    $options = namespace\page_content_list($subtype, $group->guid);

	$options['layout'] = 'content';

	return $options;
}

/**
 *
 * @param array $params
 *
 * @return options array to view a page
 *
 * @access private
 */
function page_friends($params) {
    $user = elgg_extract("user", $params, NULL);
	if (!elgg_instanceof($user, 'user')) {
	    return false;
	}

    $subtype = elgg_extract("subtype", $params, NULL);
    $options = namespace\page_content_friends($subtype, $user->guid);

	$options['layout'] = 'content';

	return $options;
}

/**
 *
 * @param array $params
 *
 * @return options array to view a page
 *
 * @access private
 */
function page_view($params) {
    $subtype = elgg_extract("subtype", $params, NULL);
    $entity  = elgg_extract("entity",  $params, NULL);
	if (!elgg_instanceof($entity, 'object', $subtype)) {
	    return false;
	}

    $options = namespace\page_content_read($subtype, $entity->guid);

	$options['layout'] = 'content';

	return $options;
}

/**
 *
 * @param array $params
 *
 * @return options array to view a page
 *
 * @access private
 */
function page_add($params) {
    $container_guid = elgg_get_logged_in_user_guid();
    
    $container = elgg_extract("container", $params, NULL);
    if(elgg_instanceof($container)) {
        $container_guid = $container->guid;
    }  
    
    $subtype = elgg_extract("subtype", $params, NULL);
    $options = namespace\page_content_add($subtype, $container_guid);

	$options['layout'] = 'content';

	return $options;
}

/**
 *
 * @param array $params
 *
 * @return options array to view a page
 *
 * @access private
 */
function page_edit($params) {
    $subtype = elgg_extract("subtype", $params, NULL);
    $entity  = elgg_extract("entity",  $params, NULL);
	if (!elgg_instanceof($entity, 'object', $subtype)) {
	    return false;
	}

    $options = namespace\page_content_edit($subtype, $entity->guid);

	$options['layout'] = 'content';

	return $options;
}

/**
 *
 * @param array $params
 *
 * @return options array to view a page
 *
 * @access private
 */
function page_all($params) {
    $subtype = elgg_extract("subtype", $params, NULL);
    $options = namespace\page_content_list($subtype);

	$options['layout'] = 'content';

	return $options;
}

/****************************************************************************/

/**
 *
 * @param int $guid GUID of the entity.
 * @return array
 */
function page_content_read($subtype, $guid = NULL) {
    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }
    
	$entity = get_entity($guid);
    if (!elgg_instanceof($entity, 'object', $subtype)) {
        return false;
    }

    $class = get_subtype_class('object', $subtype);
    // check if its an instance of the class corresponding to the subtype
    if (!elgg_instanceof($entity, 'object', $subtype, $class)) {
        return false;
    }

    /** breadcrumbs */
    $container = $entity->getContainerEntity();
	$crumbs_title = $container->name;

	if (elgg_instanceof($container, 'group')) {
		elgg_push_breadcrumb($crumbs_title, "phloor/object/$subtype/group/$container->guid/all");
	}
	else {
		elgg_push_breadcrumb($crumbs_title, "phloor/object/$subtype/owner/$container->username");
	}

	elgg_push_breadcrumb($entity->title);
    /** breadcrumbs - end */

	$return = array(
	    'filter'  => '', // no header or tabs for viewing an individual object
	    'title'   => htmlspecialchars($entity->title),
	    'content' => elgg_view_entity($entity, array(
	    	'full_view' => true,
	    )),
	);

	return $return;
}

/**
 * Get page components to list a user's or all objects.
 *
 * @return array
 * 
 * @access private
 */
function page_content_list($subtype, $container_guid = NULL, $params = array()) {
    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }

	$default_options = array(
		'type'             => 'object',
		'subtype'          => $subtype,
		'full_view'        => false,
		'pagination'       => true,
		'list_type_toggle' => false,
		'offset'           => (int) max(get_input('offset', 0), 0),
		'limit'            => (int) max(get_input('limit', 10), 0),
		'list_class'       => "elgg-list-entity phloor-list-$subtype",
	);
	$options = array_merge($default_options, $params);

    $container = get_entity($container_guid);
    $user = elgg_get_logged_in_user_entity();

	$return = array();
	if (elgg_instanceof($container)) {
		// access check for closed groups
		group_gatekeeper();

		elgg_push_breadcrumb($container->name);

		$options['container_guid'] = $container_guid;

		$title = elgg_echo("phloor/object/$subtype:page:content:list:container:title", array(
		    $container->name,
		));

        $return['title'] = $title;
        
        $return['filter_context'] = 'none';
        // set filter context to mine if owner viewing own objects
		if ($container_guid == $user->guid) {
		    $return['filter_context'] = 'mine';
		}
		// dont display filter when watching someone else's objects
		// or its from a group
		if (elgg_instanceof($container, 'group')) {
		    $return['filter'] = ''; // turn off filter
		    //$return['filter_context'] = 'none';
		}
	} else {
	    $title = elgg_echo("phloor/object/$subtype:page:content:list:all:title");

		$return['title'] = $title;
		$return['filter_context'] = 'all';
	}

    namespace\register_title_button($subtype, "add");
	
	$content = elgg_list_entities_from_metadata($options);
	if (!$content) {
	    $content = '<p>' . elgg_echo("phloor/object/$subtype:page:content:list:none") . '</p>';
	}
	
	$return['content'] = $content;

	return $return;
}

/**
 * Get page components to edit an object
 *
 * @return array
 * 
 * @access private
 */
function page_content_edit($subtype, $guid = NULL) {
    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }
    
    $entity = get_entity((int)$guid);
    if (!elgg_instanceof($entity, 'object', $subtype)) {
        //register_error(elgg_echo('phloor:error:entity:object:page:content:edit'));
        return false;
    }
    // check if user can edit this entity
    if (!$entity->canEdit()) {
        return false;
    }
       
    elgg_push_breadcrumb($entity->title, $entity->getURL());
    elgg_push_breadcrumb(elgg_echo('edit'));
	
    // get form variables
	$form_variables = \phloor\entity\object\form_vars($subtype, $guid);
	
	// create form
	$form = elgg_view('input/form',array(
    	'action' => elgg_normalize_url("action/phloor/object/save/?subtype=$subtype"),
    	'body' => elgg_view('phloor/entity_object/forms/save', array(
		    'guid'    => $entity->guid,
		    'container_guid' => get_input('container_guid', $entity->getContainerGUID()),
		    'subtype' => $subtype,
	        'form_vars' => $form_variables,
		)),
	    'id'               => "phloor-{$subtype}-edit",
	    'name'             => "phloor-{$subtype}",
    	'method'           => 'post',
    	'enctype'          => 'multipart/form-data',
    	'disable_security' => false,
    	'class'            => 'elgg-form-alt',
    ));

    $return = array(
		'title'   => elgg_echo("phloor/object/$subtype:page:content:edit:title", array(
			$entity->title
		)),
		'content' => $form,
		'sidebar' => '',
		'filter'  => '',
	);

	return $return;
}

/**
 * Get page components to create an object
 * 
 * 
 * @param string $subtype        
 * @param int    $container_guid container_guid is necessary to save the entity
 * 
 * @return boolean|array
 * 
 * @access private
 */
function page_content_add($subtype, $container_guid = 0) {
    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }
    
    //elgg_push_breadcrumb(elgg_echo("item:object:$subtype"));
	elgg_push_breadcrumb(elgg_echo('add'));
	
	// get form variables
	$form_variables = \phloor\entity\object\form_vars($subtype, NULL);	
	
	// create form
	$form = elgg_view('input/form',array(
    	'action' => elgg_normalize_url("action/phloor/object/save/?subtype=$subtype"),
    	'body' => elgg_view('phloor/entity_object/forms/save', array(
		    'guid'           => NULL,
		    'container_guid' => get_input('container_guid', $container_guid),
	        'subtype'        => $subtype,
	        'form_vars'      => $form_variables,
		)),
	    'id'               => "phloor-{$subtype}-add",
	    'name'             => "phloor-{$subtype}",
    	'method'           => 'post',
    	'enctype'          => 'multipart/form-data',
    	'disable_security' => false,
    	'class'            => 'elgg-form-alt',

    ));

    $return = array(
		'title'   => elgg_echo("phloor/object/$subtype:page:content:add:title"),
		'content' => $form,
		'sidebar' => '',
		'filter'  => '',
	);

	return $return;
}

/**
 * Get page components to list of the user's friends' objects.
 *
 * @return array
 * 
 * @access private
 */
function page_content_friends($subtype, $user_guid) {
    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }

    $user = get_user($user_guid);
    if (!elgg_instanceof($user, 'user')) {
        return false;
    }

    $crumbs_title = $user->name;
    elgg_push_breadcrumb($crumbs_title, "phloor/object/$subtype/owner/{$user->username}");
    elgg_push_breadcrumb(elgg_echo('friends'));

    // register 'add' button
    namespace\register_title_button($subtype, "add");

    $return = array();

    $return['filter_context'] = 'friends';
    $return['title'] = elgg_echo("phloor/object/$subtype:page:content:friends:title", array($subtype));

    // check if user got friends
    if (!$friends = get_user_friends($user_guid, ELGG_ENTITIES_ANY_VALUE, 0)) {
        $return['content'] = elgg_echo('friends:none:you');
        return $return;
    }

    $options = array();
    // add users friends guid to query
    foreach ($friends as $friend) {
        $options['container_guids'][] = $friend->getGUID();
    }

    // get "$subtype" entities from friends
    $list = \phloor\entity\object\get_entities($subtype, $options);
    if (!$list) {
        $return['content'] = elgg_echo("phloor/object/$subtype/list:none");
    } else {
        $return['content'] = $list;
    }

    return $return;
}

function register_title_button($subtype, $name = 'add') {
    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }

    elgg_register_title_button("phloor/object/$subtype", $name);

    /*
	if (elgg_is_logged_in()) {
		$owner = elgg_get_page_owner_entity();
		if (!$owner) {
			$owner = $user;
		}
		if ($owner && $owner->canWriteToContainer()) {
			$guid = $owner->getGUID();
			elgg_register_menu_item('title', array(
				'name' => 'add',
				'href' => "phloor/object/$subtype/add/$guid",
				'text' => elgg_echo("phloor:entity:object:menu:title:add"),
				'link_class' => 'elgg-button elgg-button-action',
			));
		}
	}*/

	return true;
}

/**
 * Default entity page hook handler.
 * 
 * This hook comes into play when an plugin based on phloor
 * does not want/need to implement a custom page handler.
 * 
 * Handles:
 * - all, owner, group, friends, edit, add, view
 * 
 * @param string $hook         'phloor_object_page_handler'
 * @param string $type         
 * @param unknown_type $return
 * @param array $params
 * 
 */
function phloor_object_default_page_hook_handler($hook, $type, $return, $params) {
    // check if another function already served the request
    if ($return !== false) {
        return $return;
    }
    // check for the right 'hook'
    if (strcmp('phloor_object:page_handler', $hook) != 0) {
        return $return;
    }

    $subtype   = elgg_extract("subtype",   $params, NULL);
    $page      = elgg_extract("page",      $params, NULL);
    $page_type = elgg_extract("page_type", $params, NULL);

    // check if the subtype is controlled by phloor
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return false;
    }
    
    // push "all" breadcrumb
    $crumbs_title = elgg_echo("phloor/object/$subtype:breadcrumb:all");
    elgg_push_breadcrumb($crumbs_title, "phloor/object/$subtype/all");

    $options = array();

	switch ($page_type) {
		case 'owner':
			$options = namespace\page_owner($params);
			break;
		case 'group':
            $options = namespace\page_group($params);
			break;
		case 'friends':
            $options = namespace\page_friends($params);
			break;
		case 'view':
            $options = namespace\page_view($params);
			break;
		case 'add':
		    gatekeeper();
            $options = namespace\page_add($params);
			break;
		case 'edit':
		    gatekeeper();
            $options = namespace\page_edit($params);
			break;
		case 'all':
            $options = namespace\page_all($params);
			break;
		default:
			return false;
	}

	// set default values for the page options
	$default_options = array(
	    'title'   => '',
	    'content' => '',
	    'sidebar' => '',
	    'layout'  => 'content',
	    //'filter'    => '',
	);
	// merge default values with options from hook
	$options = array_merge($default_options, $options);

	return $options;
}

/**
 * setups phloor default page handler system
 * 
 * @access private
 */
function phloor_entity_object_page_handler_defaults_boot() {

    // register the default page handler for all phloor objects
    elgg_register_plugin_hook_handler('phloor_object:page_handler', 'all', __NAMESPACE__ . "\phloor_object_default_page_hook_handler", 500);

    return true;
}


