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

$id       = elgg_extract('id',      $vars, '');
$class    = elgg_extract('class',   $vars, '');
$name     = elgg_extract('name',    $vars, '');
$content  = elgg_extract('content', $vars, '');

$options = array();
if (!empty($id)) {
    $options['id'] = $id;
}
if (!empty($name)) {
    $options['name'] = $name;
}
if (!empty($class)) {
    $options['class'] = $class;
}

$attributes = elgg_format_attributes($options);

$content = <<<HTML
<div $attributes>$content</div>
HTML;

echo $content;

