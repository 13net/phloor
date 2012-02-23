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
 * Check whether a string starts with a given prefix
 * 
 * @param string $haystack
 * @param string $needle
 */
function phloor_str_starts_with($haystack, $needle){
    return strpos($haystack, $needle) === 0;
}

/**
 * Check whether a string end with a given postfix.
 * 
 * @param string $haystack
 * @param string $needle
 */
function phloor_str_ends_with($haystack, $needle){
    return strrpos($haystack, $needle) === strlen($haystack) - strlen($needle);
}

/**
 * Syntactic sugar for strcmp("true", $string) == 0
 * 
 * @param string $string
 */
function phloor_str_is_true($string) {
    return strcmp("true", $string) == 0;
}

