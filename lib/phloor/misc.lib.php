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


function phloor_get_data_uri($file, $mime) {
    if (!file_exists($file) || !is_file($file)) {
        return false;
    }
    
    $contents = file_get_contents($file);
    $base64   = base64_encode($contents);
    
    return "data:$mime;base64,$base64";
}

/**
 * get a unique identification string
 * 
 * @param unknown_type $seed
 * @return string the unique id
 */
function phloor_uniqid($seed) {
    if (empty($seed)) {
        $seed = rand();
    }

    return uniqid($seed, true);
}

