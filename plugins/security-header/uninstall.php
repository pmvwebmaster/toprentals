<?php
// If uninstall not called from WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete the saved options
delete_option('inspiredmonks_security_header_options');
?>
