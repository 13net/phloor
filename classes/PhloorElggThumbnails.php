<?php

/**
 * @deprecated stupid idea
 */
class PhloorElggThumbnails extends AbstractPhloorElggThumbnails {
    public function __construct($guid = null) {
        parent::__construct($guid);
    }

    protected function initializeAttributes() {
        parent::initializeAttributes();

        $this->attributes['subtype'] = "phloor_elgg_thumbnails";
    }

    public function deleteImage() {
        if($this->canEdit()) {
            return parent::deleteImage();
        }
    }

}