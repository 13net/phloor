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
?>

.elgg-menu-filter {
    border-bottom: 2px solid #CCC;

    display: table;
    margin-bottom: 5px;
    width: 100%;
}
.elgg-menu-filter > li {
    background-color: #EEEEEE;

    border: 2px solid #CCC;
    border-bottom: none;

    border-top-left-radius: 5px;
    border-top-right-radius: 5px;

    float: left;
    margin: 0 0 0 10px;
}
.elgg-menu-filter > li:hover {
    background-color: #DEDEDE;
}
.elgg-menu-filter > li > a {
    color: #999999;
    display: block;
    height: 21px;
    padding: 3px 10px 0 10px;
    text-align: center;
    text-decoration: none;
}
.elgg-menu-filter > li > a:hover {
    background-color: #DEDEDE;

    color: #333333;
}
.elgg-menu-filter > .elgg-state-selected {
    background-color: white;
     
    border-color: #CCCCCC;
}
.elgg-menu-filter > .elgg-state-selected > a {
    background-color: white;

    position: relative;
    top: 2px;
}
