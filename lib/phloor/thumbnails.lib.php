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

namespace phloor\thumbnails;

/**
 * check if entity is instance of AbstractPhloorElggThumbnails
 * 
 * @param AbstractPhloorElggThumbnails $entity
 */
function instance_of($entity) {
    // must be entity with subtype object
    if (!elgg_instanceof($entity, 'object')) {
        return false;
    }
    
    return ($entity instanceof \AbstractPhloorElggThumbnails);
}

/**
 * Creates thumbnails for an AbstractPhloorElggThumbnails
 * instance. The object must have a valid attribute named
 * "image" which contains the location of an existing
 * image file on the file system.
 *  
 * @param AbstractPhloorElggThumbnails $object
 * 
 * @return true/false
 */
function create_thumbnails(&$object) {
    if (!namespace\instance_of($object)) {
        return false;
    }
    if (!$object->hasImage()) {
        return false;
    }

    $icon_sizes = elgg_get_config('icon_sizes');

    $guid  = $object->guid;
    $image = $object->image;
    $mime  = $object->getMimeType();

    $file = new \ElggFile();
    $file->owner_guid = elgg_get_logged_in_user_guid();
    $file->setMimeType($mime);

    $prefix = "{$object->getSubtype()}/images/thumbnails/$guid/";

    $files = array();
    foreach ($icon_sizes as $size => $size_info) {
        $resized = get_resized_image_from_existing_file($image, $size_info['w'], $size_info['h'], $size_info['square']);

        if ($resized) {
            //$clean_title = ereg_replace("[^A-Za-z0-9]", "", $title);

            $file->setFilename("{$prefix}{$size}.jpeg");
            $file->open('write');
            $file->write($resized);
            $file->close();

            // @todo: delete this after everything is upgraded
            $object->set("thumb$size", $file->getFilenameOnFilestore());

            $files[] = $file;
        } else {
            // cleanup on fail
            foreach ($files as $file) {
                $file->delete();
            }

            register_error(elgg_echo('phloor:resize:fail'));
            return false;
        }
    }

    return true;
}

/**
 * Recreates the thumbnails for an entity with an image
 * 
 * @param string                  $phloor_event 'phloor_object:save:after'
 * @param string                  $subtype      'all'
 * @param AbstractPhloorElggImage $object
 * 
 * @return boolean true
 */
function save_after_event_handler($phloor_event, $subtype, $object) {
    if (!namespace\instance_of($object)) {
        return true;
    }
    if (!$object->hasImage()) {
        return true;
    }

    // recreate thumbnails
    $object->recreateThumbnails();

    return true;
}

// create event handler for thumbnail entities
elgg_register_event_handler('phloor_object:save:after', 'all', __NAMESPACE__ . '\save_after_event_handler');

