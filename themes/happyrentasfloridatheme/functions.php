<?php


include 'admin_functions.php';
include 'woocommerce_functions.php';

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('site-loader', get_template_directory_uri() . '/loader.css', [], null);
    wp_enqueue_script('site-loader', get_template_directory_uri() . '/loader.js', [], null, true);
});
/*add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('admin-loader', get_template_directory_uri() . '/loader.css', [], null);
    wp_enqueue_script('admin-loader', get_template_directory_uri() . '/loader.js', [], null, true);
});*/

add_action('wp_ajax_get_cart_count', function() {
    $count = 0;
    if (function_exists('WC') && WC()->cart) {
        $count = WC()->cart->get_cart_contents_count();
    }
    wp_send_json(['count' => $count]);
});
add_action('wp_ajax_nopriv_get_cart_count', function() {
    $count = 0;
    if (function_exists('WC') && WC()->cart) {
        $count = WC()->cart->get_cart_contents_count();
    }
    wp_send_json(['count' => $count]);
});
