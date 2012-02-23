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

namespace phloor\entity\object\actions\defaults;

/**
 * 
 * 
 * @param string $hook   'action'
 * @param string $type   'phloor/object/save'
 * @param unknown_type $return
 * @param array $params
 */
function action_object_save($hook, $type, $return, $params) {
    if (strcmp("action", $hook) != 0 || 
        strcmp("phloor/object/save", $type) != 0) {
        return $return;
    }

    action_gatekeeper();    

    // store errors to pass along
    $error = FALSE;
    $error_forward_url = REFERER;

    $guid    = get_input('guid',    NULL);
    $subtype = get_input('subtype', NULL);
    
    $entity     = NULL;
    $new_entity = TRUE; 
    
    // check if subtype is controlled by phloor
    $entity_class = \phloor\entity\object\get_subtype_class($subtype);
    if(!class_exists($entity_class)) {
        $error = elgg_echo('phloor:error:class_not_found');
    }
    else {
        elgg_make_sticky_form($subtype);
        
        // edit or create a new entity
        if (!$guid) {
            $new_entity = TRUE;
            $entity = new $entity_class();
        } else {
            $new_entity = FALSE;
            $entity = get_entity($guid);
            
            if (!elgg_instanceof($entity, 'object', $subtype, $entity_class)) {
                $error = elgg_echo('phloor:error:entity:not_found');
            }
            else if (!$entity->canEdit()) {
                $error = elgg_echo('phloor:error:entity:cannot_edit');
            }
        }
    }
    
    // if no error happend till now => 
    // get input vars and save the object
    if(!$error) {
        // get form inputs from POST var
        $input_vars = \phloor\entity\object\get_input_vars($subtype);
    
        // save settings and display success message
        if (!\phloor\entity\object\save_vars($entity, $input_vars)) {
            $error = elgg_echo('phloor:error:entity:cannot_save');
        }
    }
    
    // show any error that occured during the saving process
    if ($error) {
        register_error($error);
        forward($error_forward_url);
        return false;
    }
    
    // clear sticky form save was successful
    if (elgg_is_sticky_form($subtype)) {
        elgg_clear_sticky_form($subtype);
    }
    
    system_message(elgg_echo('phloor:message:entity:saved'));
    forward($entity->getURL());

    return true;
}

/**
 *
 *
 * @param string $hook   'action'
 * @param string $type   'phloor/object/delete'
 * @param unknown_type $return
 * @param array $params
 */
function action_object_delete($hook, $type, $return, $params) {
    if (strcmp("action", $hook) != 0 || 
        strcmp("phloor/object/delete", $type) != 0) {
        return $return;
    }

    action_gatekeeper();

    // store errors to pass along
    $error = FALSE;
    $error_forward_url = REFERER;
    
    $guid    = get_input('guid', NULL);
    $entity  = get_entity($guid);
    $subtype = NULL;
    
    if (!elgg_instanceof($entity, 'object')) {
        $error = elgg_echo('phloor:error:entity:not_found');
    }
    else if(!$entity->canEdit()) {
        $error = elgg_echo('phloor:error:entity:cannot_edit');
    }
    
    if (!$error) {
        $subtype = $entity->getSubtype();
        $entity_class = \phloor\entity\object\get_subtype_class($subtype);
        if(!class_exists($entity_class)) {
            $error = elgg_echo('phloor:error:class_not_found');
        }
    }

    // if no error occured => delete the entity
    if (!$error && !$entity->delete()) {
        $error = elgg_echo('phloor:error:entity:cannot_delete');
    }
    
    // show any error that occured during the deletion process
    if ($error) {
        register_error($error);
        forward($error_forward_url);
        return false;
    }
    
    // display delete success message
    system_message(elgg_echo('phloor:message:entity:deleted'));
    
    // by default refer user to "all" page after successful deletion
    $success_foward_url = elgg_normalize_url("phloor/object/$subtype/all");  
    // if entity is deleted via "all" overview page
    // refer him back with all the parameters (limit, offset, etc..)
    if (elgg_http_url_is_identical($_SERVER['HTTP_REFERER'], $success_foward_url)) {
        $success_foward_url = REFERER;
    }
    
    forward($success_foward_url);
       
    return true; 
}


/**
 * setup phloor action handling
 * 
 * register default save and delete action.
 * 
 * @access private
 */
function phloor_entity_object_action_defaults_boot() {

    /**
     * ACTIONS
     */
    elgg_register_action('phloor/object/save');
    elgg_register_action('phloor/object/delete');

    elgg_register_plugin_hook_handler('action', 'phloor/object/save',   __NAMESPACE__ . '\action_object_save');
    elgg_register_plugin_hook_handler('action', 'phloor/object/delete', __NAMESPACE__ . '\action_object_delete');

    return true;
}



