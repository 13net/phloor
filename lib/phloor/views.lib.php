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

namespace phloor\views;

/**
 * Checks if a view had been extended with specific view.
 *
 * @param string $view           The view that was extended.
 * @param string $view_extension This view that was added to $view
 *
 * @return bool
 * @since 1.8-12.02.01b
 */
function is_view_extended($view, $view_extension) {
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
 * Puts the phloor metadata in front directly after
 * the <head>-tag by hooking into the 'view' plugin
 * hook of 'page/elements/head'
 *
 * @param string $hook   'view'
 * @param string $type   'page/elements/head'
 * @param string $return the content to append to [head] tag
 * @param array  $params
 *  
 * @access private
 */
function page_elements_head_hook($hook, $type, $return, $params) {
    if (strcmp("view", $hook) != 0) {
        return $return;
    }
    if (strcmp("page/elements/head", $type) != 0) {
        return $return;
    }

    // show phloor version and release metadata
    $hide_phloor_metadata = elgg_get_plugin_setting('hide_phloor_metadata', 'phloor');
    if (!phloor_str_is_true($hide_phloor_metadata)) {
    	$phloor_version = phloor_get_version();
    	$phloor_release = phloor_get_version(true);

    	$content = <<<HTML
        <meta name="PhloorRelease" content="{$phloor_version}" />
        <meta name="PhloorVersion" content="{$phloor_release}" />
HTML;

    	// but it in front of the current content
    	$return = $content . $return;
    }

    return $return;
}

/**
* loads and sets up view environment
* 
* - load/setup basic css/js (masonry, infinitescroll, ..)
* - extend views (e.g. [head] tag)
* - setup icon configuration
*
* @access private
*/
function phloor_views_boot() {
    global $CONFIG;

    /**
     * phloor JS
     */
    elgg_register_simplecache_view('js/phloor/lib/phloorlib');
    $url = elgg_get_simplecache_url('js', 'phloor/lib/phloorlib');
    elgg_register_js('phloor-js-lib', $url, 'head');
    elgg_load_js('phloor-js-lib');

    /**
     * External JS
     */
    $js_url = 'mod/phloor/vendors/';
    elgg_register_js('jquery-masonry',        $js_url.'masonry/jquery.masonry.min.js',               'head');
    elgg_register_js('jquery-infinitescroll', $js_url.'infinitescroll/jquery.infinitescroll.min.js', 'footer');
    elgg_register_js('jquery-qtip-js',        $js_url.'qtip2/jquery.qtip.min.js',                    'footer');
    elgg_register_js('jquery-colorpicker-js', $js_url.'colorpicker/js/colorpicker.js',               'footer');

    
    // Prettyboxes js 
    elgg_register_simplecache_view('js/phloor/vendors/prettycheckboxes/js');
    $url = elgg_get_simplecache_url('js', 'phloor/vendors/prettycheckboxes/js');
    elgg_register_js('phloor-prettycheckboxes-js', $url, 'head');
    
    /**
     * External CSS
     */
    $css_url = 'mod/phloor/vendors/';
    elgg_register_css('jquery-qtip-css',              $css_url.'qtip2/jquery.qtip.min.css');
    elgg_register_css('jquery-fluid960gs-layout-css', $css_url.'fluid960gs/css/layout.css');
    elgg_register_css('jquery-colorpicker-css',       $css_url.'colorpicker/css/colorpicker.css');

    /**
     * CSS
     */
    elgg_extend_view('css/elgg',  'phloor/css/elgg');
    elgg_extend_view('css/admin', 'phloor/css/admin');

    elgg_extend_view('css/elgg',  'phloor/css/elements/icons');
    elgg_extend_view('css/admin', 'phloor/css/elements/icons');

    /**
     * Plugin hooks
     */
    // add meta data to head
	elgg_register_plugin_hook_handler('view', 'page/elements/head', __NAMESPACE__ . '\page_elements_head_hook');

    // add options to default icon sizes config
    $icon_sizes = elgg_get_config('icon_sizes');
    $phloor_icon_sizes = array(
    //'pixel' => array('w' => 1, 'h' => 1,   'square' => TRUE, 'upscale' => TRUE),
	//'teeny-weeny' => array('w' => 8, 'h' => 8, 'square' => TRUE, 'upscale' => TRUE),
		'thumb'       => array('w' => 60, 'h' => 60, 'square' => TRUE, 'upscale' => TRUE),
    );
    elgg_set_config('icon_sizes', array_merge($phloor_icon_sizes, $icon_sizes));

    return true;
}

//elgg_register_event_handler('init', 'system', 'phloor_views_boot', 2); <-- called in phloor_boot
