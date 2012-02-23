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
 * English
 */

$english = array(
	'phloor' => 'phloorFramework',
	"admin:plugins:category:PHLOOR" => "PHLOOR Plugins",

	"phloor:admin_notice:min_required_php_version" => "phloor needs at least PHP version %s to run properly. Your current version of PHP is %s. Please upgrade! ",

/*** general messages *******************************************************/
	"phloor:enable"  => 'Enable',
	"phloor:disable" => 'Disable',
	"phloor:apply"   => 'Apply',

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
    "phloor:error:entity:save:attribute_error" => "Attribute cannot be adopted %s.",

	"phloor:error:entity:not_found"  => 'Entity not found. ',
	"phloor:error:class_not_found"   => 'Entity class not found. ',
	"phloor:error:subtype_not_found" => 'Entity subtype not found. ',

	"phloor:error:singleton:create" => 'There already exists an entity of this type - such entities can only exists once in the system. ',

	"phloor:error:view:not_found" => 'Could not find input view %s. ',
	
	"phloor:error:check_vars:return:false" => 'Entity validation process reports an error.',


/*** general entity messages ************************************************/

/*** interal plugin settings ************************************************/
	"phloor:settings:elgg:title"   => 'Elgg Settings',
	"phloor:settings:phloor:title" => 'phloor Settings',
	"phloor:settings:metadata:title" => 'Metadata Settings',

	"phloor:hide_elgg_metadata"   => 'Hide Elgg Metadata in &lt;head /&gt;',
	"phloor:hide_phloor_metadata" => 'Hide phloor Metadata in &lt;head /&gt;',
/****************************************************************************/


/*** thumbnails and images **************************************************/
	'phloor:image_mime_type_not_supported' => "Unsupported Mimetype (file '%s'). Please use 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg' or 'image/png'. ",
	'phloor:couldnotmoveuploadedfile' => "Could not move the uploaded file into the data directory. ",
	'phloor:upload_error' => "Upload error: %s ",
	'phloor:resize:fail' => 'Resize of the image failed',
/****************************************************************************/

);

add_translation("en", $english);