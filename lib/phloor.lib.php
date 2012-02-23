<?php
/*****************************************************************************
 * Phloor                                                                    *
 *                                                                           *
 * Copyright (C) 2011, 2012 Alois Leitner                                    *
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
 * get the current phloor version
 */
function phloor_get_version($humanreadable = false) {
    static $phloor_version, $phloor_release;

    $elgg_version = get_version();
    if ($elgg_version === false) {
        return false;
    }

    $path = elgg_get_plugins_path() . 'phloor/';
    if (!isset($phloor_version) || !isset($phloor_release)) {
        if (!include($path . "version.php")) {
            return false;
        }
    }
    return (!$humanreadable) ? $phloor_version : $phloor_release;
}

/**
 * loads and sets up the environment
 * defining and loading libraries
 * 
 * @access private
 */
function phloor_boot() {
	/**
	 * Classes
	 * load classes
	 * @todo: auto-loading?
	 */
    $classes_path = elgg_get_plugins_path() . 'phloor/classes/';
    elgg_register_classes($classes_path);
    
    
    $vendor_path = elgg_get_plugins_path() . 'phloor/vendor/';
    elgg_register_classes($vendor_path . 'class.upload_0.31');
    

	/**
	 * LIBRARY
	 * register all phloor libraries
	 */
	$lib_path = elgg_get_plugins_path() . 'phloor/lib/phloor/';
	
	// library of helper functions
	elgg_register_library('phloor-misc-lib',
	                      $lib_path . 'misc.lib.php');

	// library for string related stuff
	elgg_register_library('phloor-string-lib',
	                      $lib_path . 'strings.lib.php');

	elgg_register_library('phloor-views-lib',
	                      $lib_path . 'views.lib.php');

	// library for phloors page handler (phloor, phloor/object, etc.)
	elgg_register_library('phloor-page-handler-lib',
	                      $lib_path . 'page_handler.lib.php');

	// library for handling/managing phloor objects
	elgg_register_library('phloor-entity-object-lib',
	                      $lib_path . 'entity_object.lib.php');
	
	// library for output related stuff (icons, etc.)
	elgg_register_library('phloor-output-lib',
	                      $lib_path . 'output.lib.php');
	
	// library for handling images
	elgg_register_library('phloor-image-lib',
	                      $lib_path . 'image.lib.php');

	// library for handling thumbnails
	elgg_register_library('phloor-thumbnails-lib',
	                      $lib_path . 'thumbnails.lib.php');

	/**
	 * load libraries
	 */
	elgg_load_library('phloor-string-lib');
	elgg_load_library('phloor-misc-lib');
	elgg_load_library('phloor-views-lib');
	elgg_load_library('phloor-page-handler-lib');

	elgg_load_library('phloor-entity-object-lib');

	elgg_load_library('phloor-output-lib');

	elgg_load_library('phloor-image-lib');
	elgg_load_library('phloor-thumbnails-lib');

	// boot views
	\phloor\views\phloor_views_boot();
	// boot page handlers
	\phloor\page_handler\phloor_page_handler_boot();
	\phloor\entity\object\phloor_entity_object_boot();

	return true;
}

