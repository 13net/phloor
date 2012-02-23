<?php

/**
* get the current page url
* @deprecated use current_page_url or full_url instead
* @see current_page_url and full_url
*/
function phloor_get_current_page_url() {
    $url = "http";
    if (strcmp("on", $_SERVER["HTTPS"]) == 0) {
        $url .= "s";
    }
    $url .= "://";

    if (strcmp("80", $_SERVER["SERVER_PORT"]) != 0) {
        $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }

    return $url;
}



/**
 * Checks if a view had been extended with specific view.
 *
 * @param string $view           The view that was extended.
 * @param string $view_extension This view that was added to $view
 *
 * @return bool
 * @since 1.8-12.01.15b
 * @deprecated use \phloor\views\is_view_extended() instead
 */
function phloor_is_view_extended($view, $view_extension) {
    global $CONFIG;

    if (!isset($CONFIG->views)) {
        return FALSE;
    }

    if (!isset($CONFIG->views->extensions)) {
        return FALSE;
    }

    if (!isset($CONFIG->views->extensions[$view])) {
        return FALSE;
    }

    $priority = array_search($view_extension, $CONFIG->views->extensions[$view]);
    if ($priority === FALSE) {
        return FALSE;
    }

    return TRUE;
}


/**
* Checks if an PhloorElggImages attributes
* can be applied with certain parameters.
*
* Takes the array in the 'image' attribute
* and creates a file on the system. 'image' attribute
* will be applied with the filename of the image.
*
* @param $object the image entity
* @param $params the attributes to check
*
* @deprecated now vie event
* @see phloor_elgg_image_check_vars_event_handler
*/
function phloor_elgg_image_check_vars($object, &$params) {
    if (!phloor_elgg_image_instanceof($object)) {
        return false;
    }

    // delete image if checkbox was set
    if (phloor_str_is_true($params['delete_image']) &&
    $object->hasImage()) {
        $object->deleteImage();
        // reset the delete_image var
        unset($params['delete_image']);
    }

    // check if upload failed
    /*if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] != 0) {
    register_error(elgg_echo('phloor_menuitem:error:cannotloadimage'));
    forward(REFERER);
    }*/
    // see if an image has been set.. if not.. explicitly reassign the current one!
    if (!isset($params['image']) || empty($params['image']) || $params['image']['error'] == 4) {
        $params['image'] = $object->hasImage() ? $object->image : '';
    } else {
        $mime = array(
    		'image/jpeg'  => 'jpeg',
    		'image/pjpeg' => 'jpeg',
    		'image/png'   => 'png',
    		'image/x-png' => 'png',
    		'image/gif'   => 'gif',
        );

        if (!array_key_exists($params['image']['type'], $mime)) {
            register_error(elgg_echo('phloor:image_mime_type_not_supported', array(
            $params['image']['type'],
            )));
            return false;
        }
        if ($params['image']['error'] != 0) {
            register_error(elgg_echo('phloor:upload_error', array(
            $params['image']['error'],
            )));
            return false;
        }

        $tmp_filename = $params['image']['tmp_name'];
        $params['mime'] = $params['image']['type'];

        // determine filename (clean title)
        $clean_title = ereg_replace("[^A-Za-z0-9]", "", $params['title']); // just numbers and letters
        $filename = $clean_title . '.' . time() . '.' . $mime[$params['mime']];
        $prefix = "{$object->getSubtype()}/images/";

        /*$image = new ElggFile();
         $image->setMimeType($params['mime']);
        $image->setFilename($prefix . $filename);
        $image->open("write");
        $image->close();*/

        $object->setMimeType($params['mime']); //@see detecteMimeType
        $object->setFilename($prefix . $filename);
        $object->open("write");
        $object->close();

        // move the file to the data directory
        //$move = move_uploaded_file($_FILES['image']['tmp_name'], $image->getFilenameOnFilestore());
        $move = move_uploaded_file($_FILES['image']['tmp_name'], $object->getFilenameOnFilestore());
        // report errors if that did not succeed
        if (!$move) {
            register_error(elgg_echo('phloor:couldnotmoveuploadedfile'));
            return false;
        }

        //$params['image'] = $image->getFilenameOnFilestore();
        $params['image'] = $object->getFilenameOnFilestore();
    }

    return true;
}


/**
 * check if entity is instance of AbstractPhloorElggImage
 *
 * @param AbstractPhloorElggImage $entity
 * @deprecated
 * 
 * @see \phloor\image\instance_of()
 */
function phloor_elgg_image_instanceof($entity) {
    // must be entity with subtype object
    if (!elgg_instanceof($entity, 'object')) {
        return false;
    }

    return ($entity instanceof AbstractPhloorElggImage);
}

/**
* check if entity is instance of AbstractPhloorElggThumbnails
*
* @param AbstractPhloorElggThumbnails $entity
* 
* 
 * @deprecated see \phloor\thumbnails\instance_of
*/
function phloor_elgg_thumbnails_instanceof($entity) {
    // must be entity with subtype object
    if (!elgg_instanceof($entity, 'object')) {
        return false;
    }

    return ($entity instanceof AbstractPhloorElggThumbnails);
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
 * 
 * @deprecated see \phloor\thumbnails\create_thumbnails()
 */
function phloor_thumbnails_create_thumbnails(&$object) {
    if (!phloor_elgg_thumbnails_instanceof($object)) {
        return false;
    }
    if (!$object->hasImage()) {
        return false;
    }

    $icon_sizes = elgg_get_config('icon_sizes');

    $guid  = $object->guid;
    $image = $object->image;
    $mime  = $object->getMimeType();

    $file = new ElggFile();
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

    //return $object->save();
    return true;
}