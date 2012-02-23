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

namespace phloor\entity\object\entity_menu\defaults;

/**
 * Add/remove particular links/info to an entity menu
 *
 * @param string $hook 'register'
 * @param string $type 'menu:entity'
 * @param unknown_type $return
 * @param array $params
 * 
 * @access private
 */
function register_entity_menu_setup($hook, $type, $return, $params) {
    $entity  = elgg_extract('entity',  $params, false);
    $handler = elgg_extract('handler', $params, false);

    $subtype = $entity->getSubtype();

    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return $return;
    }
    
    /*
    if ($entity->canEdit()) {
        /*$options = array(
    		'name' => 'stuff',
    		'text' => elgg_echo('stuff'),
    		'href' => "phloor/object/$subtype/stuff/{$entity->guid}",
        );
        $return[] = \ElggMenuItem::factory($options);
    }*/

    return $return;
}

/**
 * Changes the href of the standard "edit" 
 * and "delete" buttons in the entity menu
 * for entities that are handled by phloor.
 *
 * @param string $hook 'prepare'
 * @param string $type 'menu:entity'
 * @param unknown_type $return
 * @param array $params
 * 
 * @access private
 */
function prepare_entity_menu_setup($hook, $type, $return, $params) {
    $entity  = elgg_extract('entity', $params, false);
    $handler = elgg_extract('handler', $params, false);
    
    $subtype = $entity->getSubtype();
    if (!\phloor\entity\object\is_object_subtype($subtype)) {
        return $return;
    }
    
    foreach ($return as $index => $section) {
        if (is_array($section)) {
            foreach ($section as $key => $item) {
                
                switch ($item->getName()) {
                    case 'edit':
                        // change edit href to the "phloor/object/..."
                        $item->setHref("phloor/object/$subtype/edit/{$entity->guid}");
                        break;
                    case 'delete':
                        // change delete href to the "phloor/object/..."
                        $item->setHref("action/phloor/object/delete?guid={$entity->guid}");
                        break;
                    default:
                        break;
                }
            }
        }
    }

    return $return;
}

/**
 * setup for phloors default menu implementation
 * 
 * @access private
 */
function phloor_entity_object_menu_defaults_boot() {
    /**
     * Entity menu
     */
    elgg_register_plugin_hook_handler('register', 'menu:entity', __NAMESPACE__ . '\register_entity_menu_setup');
    elgg_register_plugin_hook_handler('prepare',  'menu:entity', __NAMESPACE__ . '\prepare_entity_menu_setup');  

    return true;
}


