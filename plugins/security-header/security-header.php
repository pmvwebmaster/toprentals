<?php
/*
Plugin Name: HTTP Security Header
Description: Secure your WordPress site with essential HTTP headers. Easy dashboard management.
Version: 3.1
Author: Inspired Monks
Author URI: https://inspiredmonks.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: security-header
*/

if (!defined('ABSPATH')) exit;

// Load Translations
add_action('plugins_loaded', function() {
    load_plugin_textdomain('security-header', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Include Admin Dashboard
require_once plugin_dir_path(__FILE__) . 'inspiredmonks-security-admin-dashboard.php';

// Plugin Activation / Deactivation
register_activation_hook(__FILE__, 'inspiredmonks_activate_plugin');
register_deactivation_hook(__FILE__, 'inspiredmonks_deactivate_plugin');

// Admin Hooks
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'inspiredmonks_security_settings_link');
add_action('admin_enqueue_scripts', 'inspiredmonks_enqueue_admin_scripts');
add_action('admin_notices', 'inspiredmonks_admin_notices');

// Apply Headers
add_action('send_headers', 'inspiredmonks_apply_security_headers');

// Detect Update & Auto Apply Defaults
add_action('plugins_loaded', 'inspiredmonks_check_plugin_version_update');

// Activation: Set defaults
function inspiredmonks_activate_plugin() {
    if (!get_option('inspiredmonks_security_header_options')) {
        update_option('inspiredmonks_security_header_options', inspiredmonks_default_headers());
    }
    update_option('inspiredmonks_security_header_version', '3.0');
}

// Deactivation: Clean settings
function inspiredmonks_deactivate_plugin() {
    delete_option('inspiredmonks_security_header_options');
    delete_option('inspiredmonks_security_header_version');
}

// Detect Plugin Update and Auto-set Defaults
function inspiredmonks_check_plugin_version_update() {
    $current_version = get_option('inspiredmonks_security_header_version', '0');
    if (version_compare($current_version, '3.0', '<')) {
        update_option('inspiredmonks_security_header_options', inspiredmonks_default_headers());
        update_option('inspiredmonks_security_header_version', '3.0');
    }
}

// Admin Settings Link
function inspiredmonks_security_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=inspiredmonks-security-header-settings">' . __('Settings', 'security-header') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Enqueue Admin Styles
function inspiredmonks_enqueue_admin_scripts($hook) {
    if ($hook === 'settings_page_inspiredmonks-security-header-settings') {
        wp_enqueue_style('inspiredmonks-admin-style', plugin_dir_url(__FILE__) . 'assets/admin-dashboard-style.css', [], '1.0');
    }
}

// Admin Notices after Save or Reset
function inspiredmonks_admin_notices() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated']) && isset($_GET['_wpnonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));
        if (wp_verify_nonce($nonce, 'inspiredmonks_security_header_options-options')) {
            if ('true' === sanitize_text_field(wp_unslash($_GET['settings-updated']))) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Security headers updated successfully.', 'security-header') . '</p></div>';
            }
        }
    }

    if (isset($_GET['reset']) && isset($_GET['_wpnonce'])) {
        $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));
        if (wp_verify_nonce($nonce, 'inspiredmonks_security_header_options-options')) {
            if ('success' === sanitize_text_field(wp_unslash($_GET['reset']))) {
                echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__('Security headers have been reset to default settings.', 'security-header') . '</p></div>';
            }
        }
    }
    
    // Show custom validation errors
$validation_errors = get_transient('inspiredmonks_header_validation_errors');
if ($validation_errors && is_array($validation_errors)) {
    foreach ($validation_errors as $key => $message) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>' . esc_html($key) . '</strong>: ' . esc_html($message) . '</p></div>';
    }
    delete_transient('inspiredmonks_header_validation_errors'); // Clear after showing
}

}

// Apply Security Headers Dynamically
function inspiredmonks_apply_security_headers() {
    if (headers_sent()) {
        return;
    }

    $options = get_option('inspiredmonks_security_header_options', []);
    if (empty($options)) return;

    foreach (inspiredmonks_get_header_definitions() as $key => $definition) {
        $header = $options[$key] ?? [];

        if (empty($header) || ($header['mode'] ?? 'disabled') === 'disabled') {
            continue;
        }

        $mode = sanitize_text_field($header['mode']);
        $custom_value = isset($header['custom']) ? trim($header['custom']) : '';
        $default_value = $definition['default'];

        $value_to_use = ($mode === 'custom' && $custom_value !== '') ? $custom_value : $default_value;

        // Validate before sending
        if (!inspiredmonks_validate_header($key, $value_to_use)) {
            $value_to_use = $default_value;
        }

        switch ($key) {
            case 'hsts_header':
                header('Strict-Transport-Security: ' . $value_to_use);
                break;
            case 'x_frame_header':
                header('X-Frame-Options: ' . $value_to_use);
                break;
            case 'x_content_type_header':
                header('X-Content-Type-Options: ' . $value_to_use);
                break;
            case 'referrer_policy_header':
                header('Referrer-Policy: ' . $value_to_use);
                break;
            case 'content_security_policy_header':
                header('Content-Security-Policy: ' . $value_to_use);
                break;
            case 'permissions_policy_header':
                header('Permissions-Policy: ' . $value_to_use);
                break;
            case 'x_xss_protection_header':
                header('X-XSS-Protection: ' . $value_to_use);
                break;
            case 'x_permitted_cross_domain_header':
                header('X-Permitted-Cross-Domain-Policies: ' . $value_to_use);
                break;
            case 'expect_ct_header':
                header('Expect-CT: ' . $value_to_use);
                break;
            case 'cross_origin_opener_policy_header':
                header('Cross-Origin-Opener-Policy: ' . $value_to_use);
                break;
            case 'cross_origin_resource_policy_header':
                header('Cross-Origin-Resource-Policy: ' . $value_to_use);
                break;
            case 'cross_origin_embedder_policy_header':
                header('Cross-Origin-Embedder-Policy: ' . $value_to_use);
                break;
        }
    }
}

// Return Default Headers
function inspiredmonks_default_headers() {
    return [
        'hsts_header' => ['mode' => 'default'],
        'x_frame_header' => ['mode' => 'default'],
        'x_content_type_header' => ['mode' => 'default'],
        'referrer_policy_header' => ['mode' => 'default'],

        'content_security_policy_header' => ['mode' => 'disabled'],
        'permissions_policy_header' => ['mode' => 'disabled'],
        'x_xss_protection_header' => ['mode' => 'disabled'],
        'x_permitted_cross_domain_header' => ['mode' => 'disabled'],
        'expect_ct_header' => ['mode' => 'disabled'],
        'cross_origin_opener_policy_header' => ['mode' => 'disabled'],
        'cross_origin_resource_policy_header' => ['mode' => 'disabled'],
        'cross_origin_embedder_policy_header' => ['mode' => 'disabled'],
    ];
}

// Define Header Defaults
function inspiredmonks_get_header_definitions() {
    return [
        'hsts_header' => ['default' => 'max-age=31536000; includeSubDomains; preload'],
        'x_frame_header' => ['default' => 'SAMEORIGIN'],
        'x_content_type_header' => ['default' => 'nosniff'],
        'referrer_policy_header' => ['default' => 'no-referrer-when-downgrade'],
        'content_security_policy_header' => ['default' => "default-src 'self'; script-src 'self'; style-src 'self';"],
        'permissions_policy_header' => ['default' => 'geolocation=(), microphone=(), camera=()'],
        'x_xss_protection_header' => ['default' => '1; mode=block'],
        'x_permitted_cross_domain_header' => ['default' => 'none'],
        'expect_ct_header' => ['default' => 'max-age=86400, enforce'],
        'cross_origin_opener_policy_header' => ['default' => 'same-origin'],
        'cross_origin_resource_policy_header' => ['default' => 'same-origin'],
        'cross_origin_embedder_policy_header' => ['default' => 'require-corp'],
    ];
}

function inspiredmonks_validate_header($key, $value) {
    $value = trim($value); // Always trim whitespace first

    $is_valid = true;
    $error = '';

    switch ($key) {
        case 'x_frame_header':
            $is_valid = in_array($value, ['SAMEORIGIN', 'DENY']) || stripos($value, 'ALLOW-FROM') === 0;
            if (!$is_valid) $error = __('X-Frame-Options must be SAMEORIGIN, DENY or ALLOW-FROM uri.', 'security-header');
            break;

        case 'x_content_type_header':
            $is_valid = strtolower($value) === 'nosniff';
            if (!$is_valid) $error = __('X-Content-Type-Options should be "nosniff".', 'security-header');
            break;

        case 'referrer_policy_header':
            $is_valid = preg_match('/^[a-z\-]+$/', $value);
            if (!$is_valid) $error = __('Referrer-Policy must be a valid policy like no-referrer, strict-origin, etc.', 'security-header');
            break;

        case 'x_xss_protection_header':
            $is_valid = in_array($value, ['0', '1; mode=block']);
            if (!$is_valid) $error = __('X-XSS-Protection must be "0" or "1; mode=block".', 'security-header');
            break;

        case 'permissions_policy_header':
            $is_valid = preg_match('/^[a-zA-Z0-9\-]+=\([a-zA-Z0-9\s\'\.\-]*\)(,\s*[a-zA-Z0-9\-]+=\([a-zA-Z0-9\s\'\.\-]*\))*$/', $value);
            if (!$is_valid) $error = __('Permissions-Policy must follow the format: geolocation=(self), camera=(), etc.', 'security-header');
            break;

        case 'content_security_policy_header':
            $is_valid = stripos($value, 'default-src') !== false && stripos($value, 'self') !== false;
            if (!$is_valid) $error = __('Content-Security-Policy must include default-src and self.', 'security-header');
            break;

        case 'expect_ct_header':
            $is_valid = stripos($value, 'max-age=') !== false;
            if (!$is_valid) $error = __('Expect-CT must include max-age, e.g., "max-age=86400, enforce".', 'security-header');
            break;

        case 'cross_origin_opener_policy_header':
        case 'cross_origin_resource_policy_header':
        case 'cross_origin_embedder_policy_header':
            $is_valid = in_array($value, ['same-origin', 'same-site', 'cross-origin', 'require-corp']);
            if (!$is_valid) $error = __('Cross-Origin headers must be "same-origin", "cross-origin", "require-corp", etc.', 'security-header');
            break;

        default:
            $is_valid = true;
    }

    // ❗️Log error message for user-friendly feedback
    if (!$is_valid) {
        $existing_errors = get_transient('inspiredmonks_header_validation_errors') ?: [];
        $existing_errors[$key] = $error;
        set_transient('inspiredmonks_header_validation_errors', $existing_errors, 60);
    }

    return $is_valid;
}

?>