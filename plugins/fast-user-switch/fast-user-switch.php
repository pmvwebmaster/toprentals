<?php
/*
Plugin Name: Fast User Switch
Description: Quickly switch between users from a toolbar. Works for all user roles. Includes admin settings to enable/disable.
Version: 1.1
Author: Pablo Viana
Text Domain: fast-user-switch
*/

if (!defined('ABSPATH')) exit;

class FastUserSwitch {
    const OPTION_NAME = 'fast_user_switch_enabled';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_footer', [$this, 'render_toolbar']);
        add_action('admin_footer', [$this, 'render_toolbar']);
        add_action('init', [$this, 'handle_switch']);
    }

    public function add_settings_page() {
        add_options_page('Fast User Switch', 'Fast User Switch', 'manage_options', 'fast-user-switch', [$this, 'settings_page']);
    }

    public function register_settings() {
        register_setting('fast_user_switch_group', self::OPTION_NAME);
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Fast User Switch</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('fast_user_switch_group');
                do_settings_sections('fast_user_switch_group');
                $enabled = get_option(self::OPTION_NAME, false);
                ?>
                <label>
                    <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>" value="1" <?php checked($enabled, true); ?>>
                    Enable user switch toolbar
                </label>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_toolbar() {
        if (!is_user_logged_in()) return;
       // if (!current_user_can('manage_options')) return;
        if (!get_option(self::OPTION_NAME)) return;

        $users = get_users(['orderby' => 'display_name']);
        ?>
        <div id="fast-user-switch-toolbar" style="position:fixed;bottom:10px;right:10px;z-index:9999;background:#333;color:#fff;padding:10px;border-radius:6px;font-size:14px;">
            <form method="post" style="margin:0;">
                <select name="fast_user_switch_user_id" onchange="this.form.submit()">
                    <option value="">Switch user</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo esc_attr($user->ID); ?>">
                            <?php echo esc_html($user->display_name . ' (' . implode(', ', $user->roles) . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php wp_nonce_field('fast_user_switch_action', 'fast_user_switch_nonce'); ?>
            </form>
        </div>
        <?php
    }


    public function handle_switch() {
        if (
            isset($_POST['fast_user_switch_user_id']) &&
            //current_user_can('administrator') &&
            check_admin_referer('fast_user_switch_action', 'fast_user_switch_nonce')
        ) {
            $user_id = intval($_POST['fast_user_switch_user_id']);
            if ($user_id && get_user_by('ID', $user_id)) {
                wp_set_auth_cookie($user_id, true);
                wp_redirect(home_url());
                exit;
            }
        }
    }
}

new FastUserSwitch();
