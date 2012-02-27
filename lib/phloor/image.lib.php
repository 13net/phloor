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

namespace phloor\image;

/**
 * check if entity is instance of AbstractPhloorElggImage
 *
 * @param AbstractPhloorElggImage $entity
 */
function instance_of($entity) {
    // must be entity with subtype object
    if (!elgg_instanceof($entity, 'object')) {
        return false;
    }
    
    return ($entity instanceof \AbstractPhloorElggImage);
}



/**
 * @todo: use class.upload here!
 * 
 * @param string $hook phloor_object
 * @param unknown_type $type
 * @param unknown_type $return
 * @param unknown_type $params
 */
function check_vars($hook, $type, $return, $params) {
    if ($return === false || !is_array($return)) {
        return $return;
    }
    
    $object = elgg_extract("entity", $params, NULL);
    if (!namespace\instance_of($object)) {
        return $return;
    }

    // delete image if checkbox was set
    if (phloor_str_is_true($return['delete_image']) && $object->hasImage()) {
        $object->deleteImage();
    }
    unset($return['delete_image']); // reset the delete_image var
    
    // get image from $_FILES post
    $image_input = elgg_extract("image", $_FILES, array()); //@todo: dont use $return for that.
    


    // check if upload failed
    /*if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] != 0) {
    register_error(elgg_echo('phloor_menuitem:error:cannotloadimage'));
    forward(REFERER);
    }*/
    // see if an image has been set.. if not.. explicitly reassign the current one!
    $image = '';
    if (empty($image_input) || $image_input['error'] == 4) {
        $image = $object->hasImage() ? $object->image : '';
    } else {      
        $tmp_filename = elgg_extract('tmp_name', $image_input,  '');
        $mime         = elgg_extract('type',     $image_input,  '');
        $error        = elgg_extract('error',    $image_input, -13);
        
        if ($error != 0) {
            register_error(elgg_echo('phloor:upload_error', array(
            $error,
            )));
            unset($return['image']);
            return false;
        }
        
        $file_types = array(
    		'image/jpeg'  => 'jpeg',
    		'image/pjpeg' => 'jpeg',
    		'image/png'   => 'png',
    		'image/x-png' => 'png',
    		'image/gif'   => 'gif',
        );
        
        if (!array_key_exists($mime, $file_types)) {
            register_error(elgg_echo('phloor:image_mime_type_not_supported', array(
                $mime,
            )));
            unset($return['image']);
            return false;
        } 

        // determine filename (clean title)
        $clean_title = ereg_replace("[^A-Za-z0-9]", "", $return['title']); // just numbers and letters
        $filename = $object->guid .'.'. $clean_title .'.'. time() .'.'. $file_types[$mime];
        $prefix = "{$object->getSubtype()}/images/";

        $object->setMimeType($mime); //@see detecteMimeType
        $object->setFilename($prefix . $filename);
        $object->open("write");
        $object->close();

        // move the file to the data directory
        //$move = move_uploaded_file($_FILES['image']['tmp_name'], $image->getFilenameOnFilestore());
        $move = move_uploaded_file($return['image']['tmp_name'], $object->getFilenameOnFilestore());
        // report errors if that did not succeed
        if (!$move) {
            register_error(elgg_echo('phloor:couldnotmoveuploadedfile'));
            unset($return['image']);
            return $return;
        }

        $return['mime'] = $mime; // <-- @todo: delete
        $image = $object->getFilenameOnFilestore();
    }
    
    $return['image'] = $image;

    return $return;
}

/**
 * 
 *
 */
function default_vars($hook, $type, $return, $params) {
    if ($return === false || !is_array($return)) {
        return $return;
    }
    
    $subtype = elgg_extract("subtype", $params, NULL);
    $class = \phloor\entity\object\get_subtype_class($subtype);
    
    if (!$class || !class_exists($class)) {
        return $return;
    }
    
    // search AbstractPhloorElggImage in the class hierarchy
    if (array_search('AbstractPhloorElggImage', class_parents($class)) === false) {
        return $return;
    }
//     if (!(new $class() instanceof AbstractPhloorElggImage)) {
//         return $return;
//     }
    
    $return['image']        = '';
    $return['delete_image'] = 'false';

    return $return;
}


/**
 * adds:
 * - 'image' => 'input/file'
 * - 'delete_image' => 'phloor/input/enable'
 */
function form_vars($hook, $type, $return, $params) {
    if ($return === false || !is_array($return)) {
        return $return;
    }
    
    $subtype = elgg_extract("subtype", $params, NULL);
    $class = \phloor\entity\object\get_subtype_class($subtype);
    
    if (!$class || !class_exists($class)) {
        return $return;
    }
    
    // search AbstractPhloorElggImage in the class hierarchy
    if (array_search('AbstractPhloorElggImage', class_parents($class)) === false) {
        return $return;
    }
//     if (!(new $class() instanceof AbstractPhloorElggImage)) {
//         return $return;
//     }
       
    $return['image'] = 'input/file';
    
    $entity = elgg_extract("entity", $params, NULL);
    // add "delete_image" if entity has image
    if (namespace\instance_of($entity) && $entity->hasImage()) {  
        $return['delete_image'] = 'phloor/input/enable';
    }

    return $return;
}

// populate your function for the DEFAULT VALUES of your entity
elgg_register_plugin_hook_handler('phloor_object:default_vars', 'all', __NAMESPACE__ . '\default_vars', 900);

// populate your function for the FROM ATTRIBUTES of your entity
elgg_register_plugin_hook_handler('phloor_object:form_vars',    'all', __NAMESPACE__ . '\form_vars', 900);

// populate your function for VALIDATING the attributes of your entity
elgg_register_plugin_hook_handler('phloor_object:check_vars',   'all', __NAMESPACE__ . '\check_vars', 900);

