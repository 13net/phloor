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

namespace phloor\entity\object\events\defaults;

/**
 * Event handler for 'create', 'object'.
 * Checks for the right event and triggers the event 
 * 'phloor_object_create' with the $subtype as param 
 * when the object is handled by phloor.
 * 
 * @param string     $event        'create'
 * @param string     $object_type  'object'
 * @param ElggObject $object       the entity object
 */
function create_object_event_handler($event, $object_type, $object) {
    // check for the right event
    if (strcmp('object', $object_type) != 0 || !elgg_instanceof($object, 'object')) {
        return true;
    }
    
    $subtype = $object->getSubtype(); // get subtype and...
    //... check if phloor handles it
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return true;
    }
    
    $return = elgg_trigger_event('phloor_object:create', $subtype, $object);
    
    
    return $return === false ? false : true;
}

/**
* Event handler for 'update', 'object'.
* Checks for the right event and triggers the event
* 'phloor_object_update' with the $subtype as param
* when the object is handled by phloor.
*
* @param string     $event        'update'
* @param string     $object_type  'object'
* @param ElggObject $object       the entity object
*/
function update_object_event_handler($event, $object_type, $object) {
    // check for the right event
    if (strcmp('object', $object_type) != 0 || !elgg_instanceof($object, 'object')) {
        return true;
    }
    
    $subtype = $object->getSubtype(); // get subtype and...
    //... check if phloor handles it
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return true;
    }
    
    $return = elgg_trigger_event('phloor_object:update', $subtype, $object);
    
    return $return === false ? false : true;
}

/**
* Event handler for 'delete', 'object'.
* Checks for the right event and triggers the event
* 'phloor_object:delete' with the $subtype as param
* when the object is handled by phloor.
*
* @param string     $event        'delete'
* @param string     $object_type  'object'
* @param ElggObject $object       the entity object
*/
function delete_object_event_handler($event, $object_type, $object) {
    // check for the right event
    if (strcmp('object', $object_type) != 0 || !elgg_instanceof($object, 'object')) {
        return true;
    }

    $subtype = $object->getSubtype(); // get subtype and...
    //... check if phloor handles it
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return true;
    }
    
    $return = elgg_trigger_event('phloor_object:delete', $subtype, $object);

    return $return === false ? false : true;
}

/**
 * setup phloor event handling for objects controlled by phloor
 * 
 * hooks into the some events and
 * triggers own phloor_object_* events for objects
 * whichs subtype is managed by phloor
 * 
 * @access private
 */
function phloor_entity_object_event_defaults_boot() {
    
    elgg_register_event_handler('create', 'object', __NAMESPACE__ . '\create_object_event_handler');
    elgg_register_event_handler('update', 'object', __NAMESPACE__ . '\update_object_event_handler');
    elgg_register_event_handler('delete', 'object', __NAMESPACE__ . '\delete_object_event_handler');

    return true;
}



