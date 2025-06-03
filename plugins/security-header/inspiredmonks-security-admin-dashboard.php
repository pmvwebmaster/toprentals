<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add Security Headers Admin Page
function inspiredmonks_security_header_settings_page() {
    add_options_page(
        __('Security Headers', 'security-header'),
        __('Security Headers', 'security-header'),
        'manage_options',
        'inspiredmonks-security-header-settings',
        'inspiredmonks_security_header_settings_html'
    );
}
add_action('admin_menu', 'inspiredmonks_security_header_settings_page');

// Render Admin Settings Page
function inspiredmonks_security_header_settings_html() {
    $options = get_option('inspiredmonks_security_header_options', []);
    ?>
    <div id="inspiredmonks-admin-wrapper">
        <h1><?php esc_html_e('Manage HTTP Security Headers', 'security-header'); ?></h1>
        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('inspiredmonks_security_header_options_group');
            wp_nonce_field('inspiredmonks_custom_actions', 'inspiredmonks_custom_nonce');
            do_settings_sections('inspiredmonks-security-header-settings');
            ?>

            <div class="inspiredmonks-settings-section">
                <h2><?php esc_html_e('Headers Configuration', 'security-header'); ?></h2>

                <div class="inspiredmonks-settings-fields">
                    <?php
                    foreach (inspiredmonks_get_admin_header_definitions() as $key => $header) {
                        $mode = $options[$key]['mode'] ?? 'default';
                        $custom_value = $options[$key]['custom'] ?? '';
                        ?>
                        <div class="inspiredmonks-header-option" style="margin-bottom:20px;">
                            <label><strong><?php echo esc_html($header['label']); ?></strong></label><br>
                            <select name="inspiredmonks_security_header_options[<?php echo esc_attr($key); ?>][mode]" onchange="inspiredmonksToggleCustomInput('<?php echo esc_attr($key); ?>', this.value)">
                                <option value="disabled" <?php selected($mode, 'disabled'); ?>><?php esc_html_e('Disabled', 'security-header'); ?></option>
                                <option value="default" <?php selected($mode, 'default'); ?>><?php esc_html_e('Default', 'security-header'); ?></option>
                                <option value="custom" <?php selected($mode, 'custom'); ?>><?php esc_html_e('Custom', 'security-header'); ?></option>
                            </select>

                            <div id="<?php echo esc_attr($key); ?>-custom-input" style="margin-top:10px;<?php echo ($mode === 'custom') ? '' : 'display:none;'; ?>">
                                <?php if ($header['type'] === 'textarea') { ?>
                                    <textarea name="inspiredmonks_security_header_options[<?php echo esc_attr($key); ?>][custom]" rows="3" style="width:100%;"><?php echo esc_textarea($custom_value); ?></textarea>
                                <?php } elseif ($header['type'] === 'select') { ?>
                                    <select name="inspiredmonks_security_header_options[<?php echo esc_attr($key); ?>][custom]">
                                        <?php foreach ($header['choices'] as $val => $label) { ?>
                                            <option value="<?php echo esc_attr($val); ?>" <?php selected($custom_value, $val); ?>><?php echo esc_html($label); ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } else { ?>
                                    <input type="text" name="inspiredmonks_security_header_options[<?php echo esc_attr($key); ?>][custom]" value="<?php echo esc_attr($custom_value); ?>" style="width:100%;">
                                <?php } ?>
                            </div>
                            <small><?php esc_html_e('Default:', 'security-header'); ?> <?php echo esc_html($header['default']); ?></small>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div style="margin-top:30px;">
                <button type="submit" class="button-primary"><?php esc_html_e('Save Settings', 'security-header'); ?></button>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=inspiredmonks-security-header-settings&reset=1&_wpnonce=' . wp_create_nonce('inspiredmonks_reset_nonce'))); ?>" class="button-secondary" style="margin-left: 10px;">
                    <?php esc_html_e('Reset to Default (Important Only)', 'security-header'); ?>
                </a>
                <button type="submit" name="disable_all_headers" class="button" style="margin-left: 10px;">
                    <?php esc_html_e('Disable All Headers', 'security-header'); ?>
                </button>
            </div>
        </form>

        <p style="margin-top:30px; text-align:center;">
            <a href="https://inspiredmonks.com/security-header-documentation/" target="_blank"><?php esc_html_e('View Documentation', 'security-header'); ?></a>
        </p>
    </div>

    <script>
    function inspiredmonksToggleCustomInput(id, value) {
        const el = document.getElementById(id + '-custom-input');
        if (el) el.style.display = (value === 'custom') ? 'block' : 'none';
    }
    </script>
    <?php
}

// Register Settings
add_action('admin_init', function() {
    register_setting(
        'inspiredmonks_security_header_options_group',
        'inspiredmonks_security_header_options',
        'inspiredmonks_security_sanitize_options'
    );
});

// Sanitize Options + Handle Disable All Button
function inspiredmonks_security_sanitize_options($input) {
    $sanitized = [];

    foreach ((array) $input as $key => $header) {
        $mode = sanitize_text_field($header['mode'] ?? 'disabled');
        $custom = sanitize_textarea_field($header['custom'] ?? '');

        if ($mode === 'custom') {
            $is_valid = inspiredmonks_validate_header($key, $custom);
            if (!$is_valid) {
                $sanitized[$key] = [
                    'mode' => 'disabled',
                    'custom' => '',
                ];
                add_settings_error(
                    'inspiredmonks_security_header_options',
                    "{$key}_invalid",
                    // translators: %s is the header key (e.g., hsts_header, permissions_policy_header)
                    sprintf(__('Custom value for %s is invalid. Header disabled.', 'security-header'), $key),

                    'error'
                );
                continue;
            }
        }

        $sanitized[$key] = [
            'mode' => $mode,
            'custom' => $custom,
        ];
    }

    add_settings_error(
        'inspiredmonks_security_header_options',
        'success_message',
        __('Security header settings saved.', 'security-header'),
        'updated'
    );

    return $sanitized;
}

// Reset Logic for Important Only
add_action('admin_init', function() {
    if (isset($_GET['reset']) && '1' === sanitize_text_field(wp_unslash($_GET['reset']))) {
        if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'inspiredmonks_reset_nonce')) {
            $important = [
                'hsts_header',
                'x_frame_header',
                'x_content_type_header',
                'x_xss_protection_header',
                'referrer_policy_header',
            ];
            $reset = [];
            foreach (inspiredmonks_get_admin_header_definitions() as $key => $header) {
                $reset[$key] = [
                    'mode' => in_array($key, $important) ? 'default' : 'disabled',
                    'custom' => '',
                ];
            }
            update_option('inspiredmonks_security_header_options', $reset);
            wp_safe_redirect(admin_url('options-general.php?page=inspiredmonks-security-header-settings&reset=success'));
            exit;
        }
    }
});

// Header Definitions
function inspiredmonks_get_admin_header_definitions() {
    return [
        'hsts_header' => [
            'label' => __('HTTP Strict Transport Security (HSTS)', 'security-header'),
            'default' => 'max-age=31536000; includeSubDomains; preload',
            'type' => 'text',
        ],
        'x_frame_header' => [
            'label' => __('X-Frame-Options', 'security-header'),
            'default' => 'SAMEORIGIN',
            'type' => 'select',
            'choices' => [
                'SAMEORIGIN' => 'SAMEORIGIN',
                'DENY' => 'DENY',
                'ALLOW-FROM uri' => 'ALLOW-FROM (Deprecated)',
            ],
        ],
        'x_content_type_header' => [
            'label' => __('X-Content-Type-Options', 'security-header'),
            'default' => 'nosniff',
            'type' => 'text',
        ],
        'referrer_policy_header' => [
            'label' => __('Referrer-Policy', 'security-header'),
            'default' => 'no-referrer-when-downgrade',
            'type' => 'text',
        ],
        'content_security_policy_header' => [
            'label' => __('Content-Security-Policy', 'security-header'),
            'default' => "default-src 'self'; script-src 'self'; style-src 'self';",
            'type' => 'textarea',
        ],
        'permissions_policy_header' => [
            'label' => __('Permissions-Policy', 'security-header'),
            'default' => 'geolocation=(), microphone=(), camera=()',
            'type' => 'textarea',
        ],
        'x_xss_protection_header' => [
            'label' => __('X-XSS-Protection', 'security-header'),
            'default' => '1; mode=block',
            'type' => 'text',
        ],
        'x_permitted_cross_domain_header' => [
            'label' => __('X-Permitted-Cross-Domain-Policies', 'security-header'),
            'default' => 'none',
            'type' => 'text',
        ],
        'expect_ct_header' => [
            'label' => __('Expect-CT', 'security-header'),
            'default' => 'max-age=86400, enforce',
            'type' => 'text',
        ],
        'cross_origin_opener_policy_header' => [
            'label' => __('Cross-Origin-Opener-Policy', 'security-header'),
            'default' => 'same-origin',
            'type' => 'text',
        ],
        'cross_origin_resource_policy_header' => [
            'label' => __('Cross-Origin-Resource-Policy', 'security-header'),
            'default' => 'same-origin',
            'type' => 'text',
        ],
        'cross_origin_embedder_policy_header' => [
            'label' => __('Cross-Origin-Embedder-Policy', 'security-header'),
            'default' => 'require-corp',
            'type' => 'text',
        ],
    ];
}
?>
