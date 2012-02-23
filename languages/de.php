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
/**
 * Phloor language file
 * German
 */

$german = array(
    'phloor' => 'phloorFramework',
	"admin:plugins:category:PHLOOR" => "PHLOOR Plugins",

/*** general messages *******************************************************/
	"phloor:enable"  => 'Aktivieren',
	"phloor:disable" => 'Deaktivieren',
	"phloor:apply"   => 'Anwenden',

/*** general messages *******************************************************/

/*** general entity messages ************************************************/
	"phloor:success:entity:delete" => 'Objekt wurde erfolgreich gelöscht. ',
	"phloor:error:entity:delete"   => 'Objekt kann nicht gelöscht werden. ',

	"phloor:success:entity:edit" => 'Objekt erfolgreich editiert. ',
	"phloor:error:entity:edit"   => 'Objekt konnte nicht editiert werden. ',


	"phloor:error:entity:not_found" => 'Objekt nicht gefunden ',

	"phloor:error:singleton:create"   => 'Es gibt bereits ein Objekt dieses Typs - solche Objekte können systemweit nur ein einziges Mal existieren. ',

/*** general entity messages ************************************************/

/*** internal plugins settings **********************************************/
	"phloor:settings:elgg:title"   => 'Elgg Einstellungen',
	"phloor:settings:phloor:title" => 'phloor Einstellungen',

	"phloor:hide_elgg_metadata"   => 'Elgg Metadata in &lt;head /&gt; ausblenden',
	"phloor:hide_phloor_metadata" => 'Hide phloor Metadata in &lt;head /&gt; ausblenden',
/****************************************************************************/

/*** thumbnails and images **************************************************/
	'phloor:image_mime_type_not_supported' => "Der Mimetype der Datei ('%s') wird nicht unterstützt. Bitte benützen Sie 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg' oder 'image/png'. ",
	'phloor:couldnotmoveuploadedfile' => "Die Datei konnte nicht in das Daten-Directory verschoben werden.",
	'phloor:upload_error' => "Upload Fehler: %s ",
	'phloor:resize:fail' => 'Anpassen der Auflösung des Bilds fehlgeschlagen',
/****************************************************************************/
);

add_translation("de", $german);