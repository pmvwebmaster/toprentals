<?php

function create_new_custom_admin_role() {
    // Check if the role already exists
    if (!get_role('custom_admin')) {
        // Get admin capabilities
        $admin = get_role('administrator');
        add_role('custom_admin', 'Custom Admin', $admin->capabilities);
    }

    // Ensure extra capabilities are correctly assigned
    $role = get_role('custom_admin');

    if ($role) {
        $role->add_cap('manage_options');
        $role->add_cap('update_plugins');
        $role->add_cap('install_plugins');
        $role->add_cap('activate_plugins');
        $role->add_cap('edit_plugins');
        $role->add_cap('delete_plugins');
        $role->add_cap('edit_theme_options');
    }
}
add_action('init', 'create_new_custom_admin_role');



// Create submenu in admin
function delivery_locations_admin_menu() {
    $user = wp_get_current_user();
    if (in_array('custom_admin', $user->roles) || in_array('shop_manager', $user->roles) || in_array('administrator', $user->roles)) {

        add_menu_page(
            'Delivery/Pickup Locations',
            'Delivery Locations',
            'edit_products',
            'delivery-pickup-locations',
            'delivery_locations_admin_page',
            'dashicons-location-alt',
            20
        );
    }
}
add_action('admin_menu', 'delivery_locations_admin_menu');

// Settings page
function delivery_locations_admin_page() {
    if (isset($_POST['locations_nonce']) && wp_verify_nonce($_POST['locations_nonce'], 'save_locations_nonce')) {
        if (current_user_can('edit_products')) {
            update_option('delivery_locations', sanitize_textarea_field($_POST['delivery_locations']));
            update_option('pickup_locations', sanitize_textarea_field($_POST['pickup_locations']));
            echo '<div class="updated"><p>Locations saved successfully.</p></div>';
        } else {
            echo '<div class="error"><p>You do not have permission to save.</p></div>';
        }
    }
?>
    <div class="wrap">
        <h1>Delivery and Pickup Locations</h1>
        <form method="post">
            <?php wp_nonce_field('save_locations_nonce', 'locations_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Delivery Location (1 Per Line)</th>
                    <td>
                        <textarea name="delivery_locations" rows="8" class="large-text"><?php echo esc_textarea(get_option('delivery_locations')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pickup Locations (1 Per Line)</th>
                    <td>
                        <textarea name="pickup_locations" rows="8" class="large-text"><?php echo esc_textarea(get_option('pickup_locations')); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save'); ?>
        </form>
    </div>
<?php
}


// Register settings fields
function delivery_locations_register_settings() {
    register_setting('locations_options_group', 'delivery_locations');
    register_setting('locations_options_group', 'pickup_locations');

    add_settings_section('locations_section', '', null, 'delivery-pickup-locations');

    add_settings_field('delivery_locations', 'Delivery Location (1 Per Line)', 'delivery_locations_callback', 'delivery-pickup-locations', 'locations_section');
    add_settings_field('pickup_locations', 'Pickup Locations (1 Per Line)', 'pickup_locations_callback', 'delivery-pickup-locations', 'locations_section');
}
add_action('admin_init', 'delivery_locations_register_settings');

// Callbacks to display fields
function delivery_locations_callback() {
    $value = esc_textarea(get_option('delivery_locations'));
    echo "<textarea name='delivery_locations' rows='10' cols='50' class='large-text'>$value</textarea>";
}

function pickup_locations_callback() {
    $value = esc_textarea(get_option('pickup_locations'));
    echo "<textarea name='pickup_locations tst' rows='10' cols='50' class='large-text'>$value</textarea>";
}



/*add_action('admin_menu', 'add_orders_menu_for_custom_admin', 99);

function add_orders_menu_for_custom_admin() {
    // Check if the user has the 'custom_admin' role
    

    // Add the Orders menu (with the same link WooCommerce uses)
    add_menu_page(
        __('Orders'),                                // Page title
        __('Orders'),                                // Menu title
        'manage_woocommerce',                        // Required capability
        'edit.php?post_type=shop_order',             // Menu slug (link to orders)
        '',                                          // Callback function (not needed, WooCommerce handles it)
        'dashicons-cart',                            // Menu icon
        55                                           // Menu position (adjust as needed)
    );
}

add_action('admin_menu', function () {
   
    global $menu;

    // Look for the 'Orders' menu (inside edit.php?post_type=shop_order)
    foreach ($menu as $item) {
        if (
            isset($item[2]) &&
            $item[2] === 'edit.php?post_type=shop_order'
        ) {
            // Clone the item to another position (e.g., before the 'Comments' menu)
            $new_position = 55;
            $menu[$new_position] = $item;
            break;
        }
    }
}, 99);
function custom_admin_menu() {
    global $submenu;
    if (isset($submenu['edit.php?post_type=shop_order'])) {
        unset($submenu['edit.php?post_type=shop_order'][10]);
    }
}

add_action('admin_menu', 'custom_admin_menu', 99);  

*/

function custom_admin_css_file() {
    if (current_user_can('custom_admin')) {
        wp_enqueue_style('my-admin-css', get_template_directory_uri() . '/admin.css');
    }
}
add_action('admin_enqueue_scripts', 'custom_admin_css_file');



/*function restrict_custom_admin() {
    $role = get_role('custom_admin');
    if ($role) {
        $role->remove_cap('activate_plugins');
        $role->remove_cap('delete_plugins');
        $role->remove_cap('install_plugins');
        $role->remove_cap('update_plugins');
    }
}
add_action('init', 'restrict_custom_admin');*/