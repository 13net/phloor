<?php
/*****************************************************************************
 * Phloor                                                                    *
 *                                                                           *
 * Copyright (C) 2011 Alois Leitner                                          *
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

/**
 * 
 * @author void
 *
 */
abstract class AbstractPhloorElggThumbnails extends AbstractPhloorElggImage {

    public function delete() {
        $return = $this->deleteThumbnails();

        // delete the object even when there was an
        // error deleting the thumbnails
        //$return = parent::delete() && $return;
        // DONT DELETE object even when there was an
        // error deleting the thumbnails
        $return = $return && parent::delete();
        
        // ElggFile may not return anything atm
        return $return;
    }


    /**
     * get the url for the image
     */
    public function getImageURL($size = 'small') {
        if(!$this->hasImage()) {
            return false;
        }

        return $this->getThumbnailURL($size);
    }

    public function getThumbnailURL($size = 'small') {
        if(!$this->hasImage()) {
            return false;
        }

        $icon_sizes = elgg_get_config('icon_sizes');
        if (!array_key_exists($size, $icon_sizes)) {
            $size = 'small';
        }

        $thumb_url = "mod/phloor/pages/phloor/thumbnail.php?guid={$this->guid}&size={$size}";

        return elgg_normalize_url($thumb_url);
    }

    public function deleteImage() {
        $return = $this->canEdit();
        // delete thumbnails too
        $return = $return && $this->deleteThumbnails();
        // delete orginal image
        $return = $return && parent::deleteImage();

        return $return;
    }

    public function createThumbnails() {
        if (!$this->canEdit()) {
            return false;
        }
        
        if(!$this->hasImage()) {
            return false;
        }

        if(!\phloor\thumbnails\create_thumbnails($this)) {
            return false;
        }

        return true;
    }

    public function recreateThumbnails() {
        if (!$this->hasImage()) {
            return false;
        }

        if (!$this->deleteThumbnails()) {
            return false;
        };

        if (!$this->createThumbnails()) {
            return false;
        }

        return true;
    }

    public function getThumbnail($size = 'small') {
        $thumbnails = $this->getThumbnails();
        if (!array_key_exists($size, $thumbnails)) {
            return false;
        }

        return $thumbnails[$size];
    }

    public function getThumbnails() {
        $files = array();
        $icon_sizes = elgg_get_config('icon_sizes');

        
        $file = new ElggFile();
        $file->owner_guid = $this->owner_guid;       
        $prefix = "{$this->getSubtype()}/images/thumbnails/{$this->guid}/";
        
        $files = array();
        foreach ($icon_sizes as $size => $_) {
            // try to look for the thumbnail through file handler
            $file->setFilename("{$prefix}{$size}.jpeg");
            if($file->exists()) {
                $files[$size] = $file->getFilenameOnFilestore();
                continue;
            }        
            
            // @todo: delete this part when everything is upgraded to 1.8.3
            $thumbnail = $this->get("thumb$size");
            if (file_exists($thumbnail) && is_file($thumbnail)) {
                $files[$size] = $thumbnail;
            }
        }

        return $files;
    }

    protected function deleteThumbnails() {
        if (!$this->canEdit()) {
            return false;
        }
        
        $return = true;

        $file = new ElggFile();
        $file->owner_guid = $this->owner_guid;
        // delete thumbnails
        $thumbnails = $this->getThumbnails();
        foreach ($thumbnails as $thumbnail) {
            if ($thumbnail && file_exists($thumbnail) && is_file($thumbnail)) {
                $return = $return && @unlink($thumbnail);
                
                // @todo: delete this part when everythign is upgraded
                $this->set("thumb$size", '');
            }
        }

        return $return;
    }
}