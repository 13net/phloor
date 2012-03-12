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

$input_name        =  elgg_extract('name',        $vars, '');
$input_value       =  elgg_extract('value',       $vars, '');
$input_label       =  elgg_extract('label',       $vars, '');
$input_description =  elgg_extract('description', $vars, '');
// at least 'input' or 'input_view' must me given
$input_view        =  elgg_extract('view',        $vars, NULL);
$input             =  elgg_extract('input',       $vars, NULL);

// if no input is given.. we we try to create it with 'view' param
if (empty($input)) {
    // check if view exists
    if (!elgg_view_exists($input_view)) {
        register_error(elgg_echo('phloor:error:form:view_not_found', array($input_view)));
        return;
    }
    
    $input = elgg_view($input_view, array(
    	'name'  => $input_name,
        'value' => $input_value,
    ));
}

$output = $input;

if (strcmp('input/hidden', $input_view) != 0) {
    $inner_content = <<<HTML
        <label for="$input_name" class="control-label">$input_label</label>
        <div class="controls">
            $input
        	<p class="help-block">$input_description</p>
        </div>
HTML;
        
    $output = elgg_view('phloor/output/div', array(
    	'name'  => "$input_name-container",
        'class' => "control-group phloor-form-item phloor-form-$subtype-$input_name",
    	'content' => $inner_content,
    ));
}

echo $output;

