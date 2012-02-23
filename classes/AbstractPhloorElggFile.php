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
 */
abstract class AbstractPhloorElggFile extends ElggFile {

    protected function initializeAttributes() {
        parent::initializeAttributes();

        $this->attributes['subtype'] = "phloor_elgg_file";
    }

    /**
     * Forces downloading the file
     *
     * Credit: Alessio Delmonti http://www.tecnocrazia.com/
     *
     * @return boolean
     */
    function download(){
        if (headers_sent()) {
            return false;
        }

        if (!$this->hasFile()) {
            forward('404');
        }

        $file_name = $this->getFilenameOnFilestore();
        //$file_name = $this->getFilenameOnFilestore();
        $mime = 'application/force-download';
        header('Pragma: public'); 	// required
        header('Expires: 0');		// no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private',false);
        header('Content-Type: '.$mime);
        header("Content-Length: " . filesize($file_name));
        header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($file_name);		// push it out
        exit();
    }
}