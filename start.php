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

//elgg_register_event_handler('plugins_boot',  'system', 'phloor_plugins_boot', 1);
elgg_register_event_handler('init',  'system', 'phloor_bootloader_boot', 0);

/**
 * phloor entry point
 * registers the main library
 * and invokes setting up the framework
 * 
 * @access private
 */
function phloor_bootloader_boot() {
	/**
	 * LIBRARY
	 * find phloor on mbr :)
	 */
	$lib_path = elgg_get_plugins_path() . 'phloor/lib/';
	elgg_register_library('phloor-lib', $lib_path . 'phloor.lib.php');
	// load library
	elgg_load_library('phloor-lib');
	
	// load deprecated functions
	elgg_register_library('phloor-deprecated-lib', $lib_path . 'phloor-deprecated.lib.php');
	elgg_load_library('phloor-deprecated-lib');

	// invoke phloor boot
	phloor_boot();

	return true;
}







