<?php

$min_required_php_version = "5.3.0";

// add admin notice if php version is lower than required
if (version_compare(PHP_VERSION, $min_required_php_version, '<')) {
    $message = elgg_echo('phloor:admin_notice:min_required_php_version', array(
        $min_required_php_version, $current_php_version,
    ));
    
    // display admin message
    elgg_add_admin_notice('phloor_min_required_php_vesion', $message);
}