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
?>
<?php
// get the plugin entity
$entity = elgg_extract('entity', $vars, NULL);

// Elgg
$hide_elgg_metadata = $entity->hide_elgg_metadata;

if (!phloor_str_is_true($hide_elgg_metadata)) {
    $hide_elgg_metadata = 'false';
}

// Phloor
$hide_phloor_metadata = $entity->hide_phloor_metadata;

if (!phloor_str_is_true('true', $hide_phloor_metadata)) {
    $hide_phloor_metadata = 'false';
}


?>
<?php

echo elgg_view_title(elgg_echo('phloor:settings:metadata:title'));

// hide phloor/elgg release and version metadata
echo elgg_view('phloor/input/vendors/prettycheckboxes/checklist', array(
	'options' => array(
    	'hide_elgg_metadata'  => array(
        	'name'  => 'params[hide_elgg_metadata]',
        	'value' => $hide_elgg_metadata,
            'label' => elgg_echo('phloor:hide_elgg_metadata'),
        ),
    	'hide_phloor_metadata' => array(
        	'name'  => 'params[hide_phloor_metadata]',
        	'value' => $hide_phloor_metadata,
            'label' => elgg_echo('phloor:hide_phloor_metadata'),
        ),
    ),
));


?>
