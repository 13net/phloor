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

namespace phloor\page_handler;

/**
 * page handler for "/phloor/..."
 * 
 * reacts on certain "prefixes":
 * - "object": handle phloor objects
 * - "site":   not used atm
 * - "user":   not used atm
 * - "group":  not used atm
 *
 * @param array $page page query array (['object', ...])
 * 
 * @access private
 */
function phloor_page_handler($page) {
    // phloor page handler needs an argument
    if (!isset($page[0])) {
        return false;
    }

    elgg_push_context($page[0]);

    $return = false;
    switch ($page[0]) {
        case 'object':
            $return = namespace\phloor_object_page_handler($page);
            break;
        case 'site':
        case 'user':
        case 'group':
        default:
            return false;
    }

    elgg_pop_context();

	return $return;
}

/**
 * page handler for "/phloor/object/..."
 * 
 * needs the subtype as argument phloor/object/SUBTYPE/
 * 
 * reacts on certain "prefixes":
 * - "owner"
 * - "friends"
 * - "group"
 * - "view"
 * - "edit"
 * - "add"
 * - "all"
 * 
 * triggers the plugin hook 'phloor_object:page_handler', "$subtype:$page_type"
 * with 
 * - $subtype   => the subtype of the given object
 * - $page_type => "owner", "group", "friends", etc. or custom
 * 
 * and automatically sets up the $params array on 
 * - "owner" user entity ($param['user'])
 * - "group" group entity ($param['group'])
 * - "edit"  elgg entity with subtype $subtype ($param['entity'])
 * - "add"   container entity ($param['entity'] | ($param['container']))
 * 
 * @param array $page page query array (['phloor','object', ...])
 * 
 * @access private
 */
function phloor_object_page_handler($page) {
    $type      = elgg_extract(0, $page, NULL);
	$subtype   = elgg_extract(1, $page, NULL); // page query: phloor/object/SUBTYPE/...
	$page_type = elgg_extract(2, $page, 'all'); // 'all' | 'owner' | 'friends' | 'edit' | ...

	// phloor page handler needs an argument
	if (!isset($type) || !isset($subtype)) {
	    return false;
	}
	
	if (!\phloor\entity\object\is_object_subtype($subtype)) {
	    register_error('phloor:error:unknown_subtype');
	    return false;
	}
	
	elgg_push_context($subtype); // push subtype as context (like Elgg'd do)

	$params = array(
	    'type'      => 'object',
	    'subtype'   => $subtype,
	    'page_type' => $page_type,
	    'page'      => $page,
	);

	switch ($page_type) {
	    // 'owner' and 'friends' need a username (phloor/object/SUBTYPE/owner/USERNAME/)
		case 'owner':
		case 'friends':
		    //$user_name = $page[3];
		    $user_name = elgg_extract(3, $page, NULL); // extract username 			
			$user = get_user_by_username($user_name);
			if(!elgg_instanceof($user, 'user')) {
				return false;
			}
			
			$params['user']   = $user;
			break;

	    // 'group' needs a group guid  (phloor/object/SUBTYPE/group/GROUP_GUID/)
		case 'group':
		    //$group_guid = $page[3]; 
		    $group_guid = elgg_extract(3, $page, NULL); // extract group guid    	
			$group = get_entity($group_guid);
			if(!elgg_instanceof($group, 'group')) {
				return false;
			}

			$params['group']  = $group;
			break;

		// 'view' and 'edit' need a guid  (phloor/object/SUBTYPE/edit/GUID/)
		case 'view':
		case 'edit':
		    //$guid   = $page[3];
			$guid = elgg_extract(3, $page, NULL); // extract guid    	
			$entity = get_entity($guid);
        	 // check if its an instance of the class corresponding to the subtype
			if(!elgg_instanceof($entity, 'object', $subtype/*, $class*/)) {
				return false;
			}

			$params['entity'] = $entity;
			break;

		// 'add' needs an container guid  (phloor/object/SUBTYPE/add/CONTAINER_GUID/)
		// @todo: container?
		case 'add' :
			//$container_guid   = $page[3];
		    $container_guid = elgg_extract(3, $page, NULL); // container guid    				
			$container = get_entity($container_guid);
			
			// check at least for entity (can be user/group/anything else)
			if(!elgg_instanceof($container)) {
				return false;
			}

			$params['container'] = $container;
			break;
			
		case 'all' :
		default : 
		    break;
	}

	// invoke "phloor_object:page_handler" hook
	$options = elgg_trigger_plugin_hook('phloor_object:page_handler', "$subtype:$page_type", $params, false);
	
	// break up if hook handled the request by itself
	if ($options === true || headers_sent()) {
	    return true;
	}
	// or break up if no hook registered or hook returned false (or not an array)
	if ($options === false || !is_array($options)) {
	    return false;
	}

	// override Elggs default filter
    /*if(!isset($options['filter'])) {
        $filter = elgg_view('phloor/entity_object/page/layouts/content/filter', $options + $params);
        system_message("!isset(ptions['filter'] $filter");
        $options['filter'] = $filter;
    }*/

	// IMPORTANT: set context (for "add" title button etc!)
    $options['context'] = "phloor/object/$subtype";

	$title  = elgg_extract("title",  $options, "");
	//unset($options['context']);

	$layout = elgg_extract("layout", $options, "content");
	unset($options['layout']); // layout is phloor specific (not needed below)

	// create the output
    $body = elgg_view_layout($layout, $options);
    // view the output
	echo elgg_view_page($title, $body);

	elgg_pop_context(); // pop context again (pushed it at beginning of function)

	return true; // request was properly handled
}


/**
 * page handler setup
 * 
 * registers the "phloor" pagehandler
 * 
 * @access private
 */
function phloor_page_handler_boot() {

    elgg_register_page_handler('phloor', '\phloor\page_handler\phloor_page_handler');

    return true;
}

