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
	"phloor:success:entity:delete"      => 'Entity successfully deleted. ',
	"phloor:message:entity:deleted"     => 'Entity successfully deleted. ',
	"phloor:error:entity:delete"        => 'Entity can not be deleted. ',
	"phloor:error:entity:cannot_delete" => 'Entity can not be deleted. ',

	"phloor:success:entity:edit"      => 'Entity successfully edited. ',
	"phloor:error:entity:edit"        => 'Entity cannot be edited. ',
	"phloor:error:entity:cannot_edit" => 'Entity cannot be edited. ',

	"phloor:success:entity:create" => 'Entity successfully created. ',
	"phloor:error:entity:create"   => 'Entity cannot be created. ',

	"phloor:message:entity:saved"       => 'Entity successfully saved. ',
	"phloor:error:entity:cannot_save"   => 'Entity cannot be saved. ',
    "phloor:error:entity:save:attribute_error" => "Attribute konnte nicht übernommen werden %s.",

	"phloor:error:entity:not_found"  => 'Entity not found. ',
	"phloor:error:class_not_found"   => 'Entity class not found. ',
	"phloor:error:subtype_not_found" => 'Entity subtype not found. ',

	"phloor:error:singleton:create" => 'Es gibt bereits ein Objekt dieses Typs - solche Objekte können systemweit nur ein einziges Mal existieren. ',

	"phloor:error:view:not_found" => 'Invalide View %s. ',
	
	"phloor:error:check_vars:return:false" => 'Validierungsprozess fehlgeschlagen. ',


	"phloor:error:unknown_subtype" => 'Unbekannter Subtype.',


/*** general entity messages ************************************************/

/*** internal plugins settings **********************************************/
	"phloor:settings:elgg:title"   => 'Elgg Einstellungen',
	"phloor:settings:phloor:title" => 'phloor Einstellungen',
	"phloor:settings:metadata:title" => 'Metadaten Einstellungen',

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