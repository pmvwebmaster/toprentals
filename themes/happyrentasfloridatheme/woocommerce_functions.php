<?php

add_action('init', function () {
    if (!WC()->session) {
        WC()->initialize_session();
    }
    if (class_exists('WooCommerce')) {
        WC()->session->get_session_cookie();
    }else{
        echo 'WooCommerce is not activated.';
    }
});


function rental_enqueue_scripts() {
    // Ensure jQuery is loaded
    wp_enqueue_script('jquery');

    // Path to rental.js
    wp_enqueue_script(
        'rental-script',
        get_template_directory_uri() . '/js/rental.js',
        array('jquery'),
        null,
        true // load in footer
    );

    // Inject data into JS
    wp_localize_script('rental-script', 'rentalAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('add_product_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'rental_enqueue_scripts');



function add_product_to_cart() {
    if (!class_exists('WC_Cart')) {
        wp_send_json_error(['message' => 'WooCommerce is not activated.']);
        return;
    }
    wc_load_cart();

    /*if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'add_product_nonce')) {

        wp_send_json_error([
            'message' => 'Invalid request (nonce).',
            'nonce' => $_POST['nonce'],
            'verify' => wp_verify_nonce($_POST['nonce'], 'add_product_nonce')
        ]);
        wp_die();
    }*/

    if (!isset($_POST['produto_id']) || !isset($_POST['preco'])) {
        wp_send_json_error([ 
            'message' => 'Required parameters missing.',
            'data' => $_POST ,
        ]);
        wp_die();
    }

    $produto_id = intval($_POST['produto_id']);
    $preco = floatval($_POST['preco']);
    $preco_final_sem_taxa = isset($_POST['preco_final_sem_taxa']) ? floatval($_POST['preco_final_sem_taxa']) : 0;
    $taxes = isset($_POST['taxes']) ? floatval($_POST['taxes']) : 0;
    $local_entrega = isset($_POST['local_entrega']) ? sanitize_text_field($_POST['local_entrega']) : '';
    $local_retorno = isset($_POST['local_retorno']) ? sanitize_text_field($_POST['local_retorno']) : '';
    $data_entrega = isset($_POST['data_entrega']) ? sanitize_text_field($_POST['data_entrega']) : '';
    $data_retorno = isset($_POST['data_retorno']) ? sanitize_text_field($_POST['data_retorno']) : '';
    $hora_entrega = isset($_POST['hora_entrega']) ? sanitize_text_field($_POST['hora_entrega']) : '';
    $hora_retorno = isset($_POST['hora_retorno']) ? sanitize_text_field($_POST['hora_retorno']) : '';
    $sobrenome = isset($_POST['sobrenome']) ? sanitize_text_field($_POST['sobrenome']) : '';
    $preco_extra = get_post_meta($produto_id, '_preco_extra', true);
    $valor_seguro = isset($_POST['seguro']) ? floatval($_POST['seguro']) : 0;

    // Recebe acessórios como array associativo (name/value) ou array de arrays
    $acessorios = [];
    if (isset($_POST['acessorios']) && is_array($_POST['acessorios'])) {
        foreach ($_POST['acessorios'] as $item) {
            // Suporta tanto {name, value} quanto {nome, valor}
            $nome = isset($item['name']) ? sanitize_text_field($item['name']) : (isset($item['nome']) ? sanitize_text_field($item['nome']) : '');
            $valor = isset($item['value']) ? floatval($item['value']) : (isset($item['valor']) ? floatval($item['valor']) : 0);
            if ($nome !== '') {
                $acessorios[] = ['nome' => $nome, 'valor' => $valor];
            }
        }
    }

    if (!$produto_id || !$preco) {
        wp_send_json_error([
            'message' => 'Invalid product or price.',
            'produto_id' => $produto_id,
            'preco' => $preco
        ]);
        wp_die();
    }

    $produto = wc_get_product($produto_id);
    if (!$produto || 'publish' !== get_post_status($produto_id)) {
        wp_send_json_error(['message' => 'Product not found or not published.']);
        wp_die();
    }

    $cart_item_data = [
        'price' => $preco,
        'preco_final_sem_taxa' => number_format($preco_final_sem_taxa, 2, '.', ''),
        '_preco_extra' => number_format($preco_extra, 2, '.', ''),
        '_valor_seguro' => number_format($valor_seguro, 2, '.', ''),
        'local_entrega' => $local_entrega,
        'local_retorno' => $local_retorno,
        'data_entrega' => $data_entrega,
        'data_retorno' => $data_retorno,
        'hora_entrega' => $hora_entrega,
        'hora_retorno' => $hora_retorno,
        'sobrenome' => $sobrenome,
        'acessorios' => $acessorios,
        'taxes' => number_format($taxes, 2, '.', ''),
    ];

    try {
        $added = WC()->cart->add_to_cart($produto_id, 1, 0, array(), $cart_item_data);

        if ($added) {
            wp_send_json_success([
                'message' => 'Product added to cart.',
                'cart_item_key' => $added,
                'cart_item_data' => $cart_item_data,
                'cart' => WC()->cart->get_cart(),
                'preco' => $preco,
                'preco_extra' => $preco_extra,
                'valor_seguro' => $valor_seguro,
            ]);
        } else {
            wp_send_json_error([
                'message' => 'Error adding product to cart.',
                'debug' => [
                    'produto_id' => $produto_id,
                    'preco' => $preco,
                    'status' => get_post_status($produto_id),
                    'cart_item_data' => $cart_item_data,
                    'added' => $added,
                    'cart' => WC()->cart,
                ]
            ]);
        }
    } catch (Exception $e) {
        wp_send_json_error([
            'message' => 'Exception caught: ' . $e->getMessage(),
            'debug' => [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]
        ]);
    }

    wp_die();
}           

add_action('wp_ajax_add_product_to_cart', 'add_product_to_cart');
add_action('wp_ajax_nopriv_add_product_to_cart', 'add_product_to_cart');

add_filter('woocommerce_before_calculate_totals', 'apply_custom_price_to_cart', 20, 1);
function apply_custom_price_to_cart($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    // Loop through cart items
    foreach ($cart->get_cart() as $cart_item) {
        if (isset($cart_item['price'])) {
            $cart_item['data']->set_price($cart_item['price']);
        }
    }
}



// Add custom fields to the 'Product' tab
function add_custom_product_fields() {
    global $post;

    echo '<div class="options_group">';

    // Extra Price per Additional Day
    woocommerce_wp_text_input(array(
        'id' => '_preco_extra',
        'label' => __('Extra Price per Additional Day US$', 'woocommerce'),
        'desc_tip' => 'true',
        'description' => __('Amount charged for each extra day beyond 4 days.', 'woocommerce'),
        'type' => 'number',
        'custom_attributes' => array('step' => '0.01', 'min' => '0'),
    ));

    // Insurance Value
    woocommerce_wp_text_input(array(
        'id' => '_valor_seguro',
        'label' => __('Insurance Value', 'woocommerce'),
        'desc_tip' => 'true',
        'description' => __('Insurance amount charged.', 'woocommerce'),
        'type' => 'number',
        'custom_attributes' => array('step' => '0.01', 'min' => '0'),
    ));

    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'add_custom_product_fields');

// Save custom fields
function save_custom_product_fields($post_id) {
    $fields = ['_preco_extra', '_valor_seguro'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('woocommerce_process_product_meta', 'save_custom_product_fields');

// Accessories Metabox
function add_accessories_metabox() {
    add_meta_box(
        'acessorios_produto',
        'Accessories',
        'render_accessories_metabox',
        'product',
        'normal',
        'default'
    );
}
// Show fields in "General" tab
add_action('woocommerce_product_options_general_product_data', 'show_accessories_field');

function show_accessories_field() {
    global $post;
    $acessorios = get_post_meta($post->ID, '_acessorios_produto', true);
    $acessorios = is_array($acessorios) ? $acessorios : [];

    echo '<div class="options_group">';
    echo '<p class="form-field"><strong>Accessories</strong></p>';
    echo '<div id="acessorios_wrapper">';

    if (!empty($acessorios)) {
        foreach ($acessorios as $i => $item) {
            echo '<p><input type="text" name="acessorios_produto['.$i.'][nome]" placeholder="Accessory name" value="'.esc_attr($item['nome']).'" />';
            echo ' <input type="number" step="0.01" name="acessorios_produto['.$i.'][valor]" placeholder="Value" value="'.esc_attr($item['valor']).'" />';
            echo ' <a href="#" class="remover_acessorio">Remove</a></p>';
        }
    }

    echo '</div>';
    echo '<button type="button" id="add_acessorio" class="button">Add Accessory</button>';
    echo '</div>';

    // JS
    ?>
    <script>
        jQuery(function($){
            let count = <?php echo count($acessorios); ?>;
            $('#add_acessorio').on('click', function(e){
                e.preventDefault();
                $('#acessorios_wrapper').append(
                    `<p><input type="text" name="acessorios_produto[${count}][nome]" placeholder="Accessory name" required oninvalid="this.setCustomValidity('Please enter an accessory name or remove accessory')" oninput="this.setCustomValidity('')" />
                    <input type="number" step="0.01" name="acessorios_produto[${count}][valor]" placeholder="Value" required oninvalid="this.setCustomValidity('Please enter a value or remove accessory')" oninput="this.setCustomValidity('')" />
                    <a href="#" class="remover_acessorio">Remove</a></p>`
                );
                count++;
            });

            $(document).on('click', '.remover_acessorio', function(e){
                e.preventDefault();
                $(this).parent('p').remove();
            });
        });
    </script>
    <?php
}

// Save accessories
add_action('woocommerce_process_product_meta', 'save_accessories_field');

function save_accessories_field($post_id) {
    if (isset($_POST['acessorios_produto'])) {
        update_post_meta($post_id, '_acessorios_produto', wc_clean($_POST['acessorios_produto']));
    } else {
        delete_post_meta($post_id, '_acessorios_produto');
    }
}





function add_custom_data_to_cart($cart_item_data, $product_id) {
    if (isset($_POST['local_entrega'])) {
        $cart_item_data['price'] = floatval($_POST['preco']);
        $cart_item_data['preco_final_sem_taxa'] = floatval($_POST['precofinalSemTaxa']);
        $cart_item_data['_preco_extra'] = floatval( get_post_meta($product_id, '_preco_extra', true));
        $cart_item_data['_valor_seguro'] =  floatval($_POST['seguro']);
        $cart_item_data['local_entrega'] = sanitize_text_field($_POST['local_entrega']);
        $cart_item_data['local_retorno'] = sanitize_text_field($_POST['local_retorno']);
        $cart_item_data['data_entrega'] = sanitize_text_field($_POST['data_entrega']);
        $cart_item_data['data_retorno'] = sanitize_text_field($_POST['data_retorno']);
        $cart_item_data['hora_entrega'] = sanitize_text_field($_POST['hora_entrega']);
        $cart_item_data['hora_retorno'] = sanitize_text_field($_POST['hora_retorno']);
        $cart_item_data['sobrenome'] = sanitize_text_field($_POST['sobrenome']);
        $cart_item_data['taxes'] = floatval($_POST['taxes']);
    }
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'add_custom_data_to_cart', 10, 2);

function show_custom_data_in_cart($item_data, $cart_item) {
    $fields = [
        'local_entrega' => 'Delivery Location',
        'local_retorno' => 'Return Location',
        'data_entrega' => 'Delivery Date',
        'hora_entrega' => 'Delivery Time',
        'data_retorno' => 'Return Date',
        'hora_retorno' => 'Return Time',
        'sobrenome' => 'Family Name',
        '_preco_extra' => 'Extra Price',
        '_valor_seguro' => 'Insurance',
        'acessorios' => 'Accessories',
        'preco_final_sem_taxa' => 'Price',
        'price' => 'Total',
        'taxes' => 'Taxes',
    ];

    foreach ($fields as $key => $label) {
        if (!empty($cart_item[$key])) {
            $item_data[] = [
                'name' => $label,
                'value' => wc_clean($cart_item[$key]),
            ];
        }
    }

    return $item_data;
}
add_filter('woocommerce_get_item_data', 'show_custom_data_in_cart', 10, 2);

function save_custom_data_to_order($item, $cart_item_key, $values, $order) {
    $keys = [
        'price', '_preco_extra', '_valor_seguro',
        'local_entrega', 'local_retorno',
        'data_entrega', 'data_retorno',
        'hora_entrega', 'hora_retorno',
        'sobrenome', 'acessorios'
    ];

    foreach ($keys as $key) {
        if (isset($values[$key])) {
            $item->add_meta_data($key, $values[$key]);
        }
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'save_custom_data_to_order', 10, 4);

function show_custom_data_in_admin_order($item_id, $item, $order) {
    $metas = [
        'price' => 'Total Price',
        'preco_final_sem_taxa' => 'Final Price Without Tax',
        '_preco_extra' => 'Extra Price',
        '_valor_seguro' => 'Insurance Value',
        'local_entrega' => 'Delivery Location',
        'local_retorno' => 'Return Location',
        'data_entrega' => 'Delivery Date',
        'hora_entrega' => 'Delivery Time',
        'data_retorno' => 'Return Date',
        'hora_retorno' => 'Return Time',
        'sobrenome' => 'Family Name',
        'acessorios' => 'Accessories',
        'taxes' => 'Taxes',
    ];

    foreach ($metas as $key => $label) {
        $value = wc_get_order_item_meta($item_id, $key, true);
        if (!empty($value) || $value === 0) {
            echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
        }
    }
}

add_filter('woocommerce_get_item_data', 'show_accessories_in_cart', 10, 2);
function show_accessories_in_cart($item_data, $cart_item) {
    if (!empty($cart_item['acessorios'])) {
        foreach ($cart_item['acessorios'] as $acessorio) {
            $item_data[] = array(
                'key'     => 'Accessory',
                'value'   => $acessorio['nome'] . ' (US$ ' . number_format($acessorio['preco'], 2) . ')',
                'display' => '',
            );
        }
    }
    return $item_data;
}



add_filter('woocommerce_order_item_display_meta_key', function($display_key, $meta, $item) {
    $labels = [
        'preco' => 'Total',
        'preco_final_sem_taxa' => 'Price',
        '_preco_extra' => 'Extra Price',
        '_valor_seguro' => 'Insurance',
        'local_entrega' => 'Delivery Location',
        'local_retorno' => 'Return Location',
        'data_entrega' => 'Delivery Date',
        'data_retorno' => 'Return Date',
        'hora_entrega' => 'Delivery Time',
        'hora_retorno' => 'Return Time',
        'sobrenome' => 'Family Name',
        'acessorios' => 'Accessories',
        'taxes' => 'Taxes',
        'disponibilidade' => 'Availability',
        // Traduções para possíveis labels em português
        'Local de entrega' => 'Delivery Location',
        'Local de devolução' => 'Return Location',
        'Data de entrega' => 'Delivery Date',
        'Data de retorno' => 'Return Date',
        'Prazo de entrega' => 'Delivery Time',
        'Horário de retorno' => 'Return Time',
        'Nome de família' => 'Family Name',
        'Disponibilidade' => 'Availability',
    ];
    return isset($labels[$meta->key]) ? $labels[$meta->key] : (isset($labels[$display_key]) ? $labels[$display_key] : $display_key);
}, 10, 3);



// Add availability status as 'reserved' to each order item
add_action('woocommerce_checkout_create_order_line_item', 'add_availability_status_to_item', 10, 4);
function add_availability_status_to_item($item, $cart_item_key, $values, $order) {
    $item->add_meta_data('disponibilidade', 'reserved', true);
}

// Show selector + save button in admin order
add_action('woocommerce_after_order_itemmeta', 'admin_edit_availability_status', 10, 3);
function admin_edit_availability_status($item_id, $item, $product) {
    if (!is_admin() || !current_user_can('edit_shop_orders')) return;

    $disponibilidade = $item->get_meta('disponibilidade');
    $status_options = ['reserved', 'rented', 'finished'];

    echo '<div class="disponibilidade-box">';
    echo '<p><strong>Availability Status:</strong> ';
    if($disponibilidade === 'finished') {
        echo 'finished (finished)';
        return;
    }
    echo '<select class="disponibilidade-select" data-item_id="' . esc_attr($item_id) . '">';
    foreach ($status_options as $status) {
        $selected = selected($disponibilidade, $status, false);
        echo '<option value="' . esc_attr($status) . '" ' . $selected . '>' . ucfirst($status) . '</option>';
    }
    echo '</select> ';
    echo '<button type="button" class="button salvar-disponibilidade" data-item_id="' . esc_attr($item_id) . '">Save</button></p>';
    echo '<div class="status-feedback" style="margin-top:5px;"></div>';
    echo '</div>';
}

add_action('admin_enqueue_scripts', 'enqueue_availability_admin_script');
function enqueue_availability_admin_script($hook) {
    global $post;

    $script_handle = 'disponibilidade-script';
    $script_path = get_template_directory_uri() . '/js/disponibilidade.js';

    wp_enqueue_script($script_handle, $script_path, ['jquery'], null, true);

    wp_add_inline_script($script_handle, "
        jQuery(function($) {
            $('.salvar-disponibilidade').on('click', function(e) {
                e.preventDefault();
                const btn = $(this);
                const item_id = btn.data('item_id');
                const select = $('.disponibilidade-select[data-item_id=\"' + item_id + '\"]');
                const status = select.val();
                const feedback = btn.closest('.disponibilidade-box').find('.status-feedback');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'ajax_save_availability_status',
                        item_id: item_id,
                        status: status,
                        nonce: '" . wp_create_nonce('save_availability_ajax') . "'
                    },
                    beforeSend: () => feedback.text('Saving...'),
                    success: (res) => {
                        if (res.success) {
                            feedback.text('Status updated to: ' + status);
                            if (status === 'finished') {
                                select.prop('disabled', true);
                                btn.prop('disabled', true);
                            } else {
                                select.prop('disabled', false);
                                btn.prop('disabled', false);
                            }
                        } else {
                            feedback.text('Error saving: ' + res.data);
                        }
                    },
                    error: (err) => feedback.text('Unexpected error.')

                });
            });
        });
    ");
}

// Handle AJAX request to save item status
add_action('wp_ajax_ajax_save_availability_status', 'ajax_save_availability_status');
function ajax_save_availability_status() {
    check_ajax_referer('save_availability_ajax', 'nonce');

    $item_id = intval($_POST['item_id']);
    $new_status = sanitize_text_field($_POST['status']);

    if (!$item_id || !in_array($new_status, ['reserved', 'rented', 'finished'])) {
        wp_send_json_error('Invalid data.');
    }

    try {
        // Get order from item
        $order_id = wc_get_order_id_by_order_item_id($item_id);
        if (!$order_id) {
            wp_send_json_error('Order not found.');
        }

        $order = wc_get_order($order_id);
        $item = $order->get_item($item_id);

        if (!$item) {
            wp_send_json_error('Item not found.');
        }

        $current_status = $item->get_meta('disponibilidade');

        if ($current_status === 'finished') {
            wp_send_json_error('Item already finished.');
        }

        $item->update_meta_data('disponibilidade', $new_status);

        // If returned to stock
        if ($new_status === 'finished') {
            $product = $item->get_product();
            if ($product && $product->managing_stock()) {
                $qty = $item->get_quantity();
                $stock = $product->get_stock_quantity();
                $product->set_stock_quantity($stock + $qty);
                $product->save();
            }
        }

        $item->save();
        wp_send_json_success();
    } catch (Exception $e) {
        wp_send_json_error('Error saving: ' . $e->getMessage());
    }
}

// Remove any previous filter (optional, if you used it before)
remove_all_actions('restrict_manage_posts');

// --- ADMIN NOTIFICATIONS FOR ATTENTION ITEMS ---

// --- ADMIN NOTIFICATIONS FOR ATTENTION ITEMS ---

// Notificação de novo pedido não visualizado pelo admin
add_action('all_admin_notices', function () {
    if (!current_user_can('edit_shop_orders')) return;

    // Busca o último pedido visualizado pelo admin (opção no banco)
    $last_seen_id = (int) get_option('happyrentasflorida_last_seen_order_id', 0);

    // Busca o maior ID de pedido atual
    $args = [
        'limit' => 1,
        'orderby' => 'ID',
        'order' => 'DESC',
        'return' => 'ids',
        'type' => 'shop_order',
        'status' => array_keys(wc_get_order_statuses()),
    ];
    $orders = wc_get_orders($args);
    $latest_id = !empty($orders) ? (int) $orders[0] : 0;

    // Se há novo pedido não visualizado, mostra aviso
    if ($latest_id > $last_seen_id && $latest_id > 0) {
        $edit_url = admin_url('post.php?post=' . $latest_id . '&action=edit');
        echo '<div class="notice notice-success notice-new-order"><p>';
        echo '🛒 <strong>New order received!</strong> ';
        echo '<a href="' . esc_url($edit_url) . '" class="button button-primary" id="mark-order-seen" data-order-id="' . esc_attr($latest_id) . '">View Order #' . esc_html($latest_id) . '</a>';
        echo '</p></div>';
        // JS para marcar como visualizado ao clicar
        ?>
        <script>
        jQuery(function($){
            $('#mark-order-seen').on('click', function(e){
                var orderId = $(this).data('order-id');
                $.post(ajaxurl, {
                    action: 'mark_order_seen',
                    order_id: orderId,
                    _wpnonce: '<?php echo wp_create_nonce('mark_order_seen_nonce'); ?>'
                });
            });
        });
        </script>
        <?php
    }
});

// AJAX handler para marcar pedido como visualizado
add_action('wp_ajax_mark_order_seen', function () {
    if (!current_user_can('edit_shop_orders')) wp_send_json_error();
    check_ajax_referer('mark_order_seen_nonce');
    $order_id = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
    if ($order_id > 0) {
        update_option('happyrentasflorida_last_seen_order_id', $order_id);
        wp_send_json_success();
    }
    wp_send_json_error();
});

add_action('all_admin_notices', function () {
    if (!current_user_can('edit_shop_orders')) return;

    $args = [
        'status' => ['wc-processing', 'wc-completed', 'wc-on-hold'],
        'limit' => 100, // adjust as needed
        'type' => 'shop_order',
        'return' => 'ids',
    ];
    $orders = wc_get_orders($args);
    if (empty($orders)) return;

    $now = new DateTime('now', wp_timezone());
    $today = $now->format('Y-m-d');
    $current_time = $now->format('H:i');
    $tomorrow = (clone $now)->modify('+1 day')->format('Y-m-d');

    $attention_items = [];

    foreach ($orders as $order_id) {
        $order = wc_get_order($order_id);
        foreach ($order->get_items() as $item_id => $item) {
            $status = $item->get_meta('disponibilidade');
            $data_entrega = $item->get_meta('data_entrega');
            $hora_entrega = $item->get_meta('hora_entrega');
            $data_retorno = $item->get_meta('data_retorno');
            $hora_retorno = $item->get_meta('hora_retorno');

            // Normalize date/time
            $entrega_dt = $data_entrega ? DateTime::createFromFormat('Y-m-d', $data_entrega, wp_timezone()) : false;
            $retorno_dt = $data_retorno ? DateTime::createFromFormat('Y-m-d', $data_retorno, wp_timezone()) : false;

            $entrega_hora = $hora_entrega ? $hora_entrega : '00:00';
            $retorno_hora = $hora_retorno ? $hora_retorno : '00:00';

            $entrega_full = $entrega_dt ? clone $entrega_dt : false;
            $retorno_full = $retorno_dt ? clone $retorno_dt : false;
            if ($entrega_full) $entrega_full->modify($entrega_hora);
            if ($retorno_full) $retorno_full->modify($retorno_hora);

            // 1. Delivery today or before delivery time today and still reserved
            if (
                $status === 'reserved' &&
                $entrega_full &&
                (
                    ($entrega_full->format('Y-m-d') === $today && $entrega_hora >= $current_time) ||
                    ($entrega_full->format('Y-m-d') < $today)
                )
            ) {
                $attention_items[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'product' => $item->get_name(),
                    'msg' => 'Delivery today or overdue, not marked as "rented".',
                    'status' => $status,
                    'data_entrega' => $data_entrega,
                    'hora_entrega' => $hora_entrega,
                ];
            }

            // 1b. Delivery tomorrow (attention)
            if (
                $status === 'reserved' &&
                $entrega_full &&
                $entrega_full->format('Y-m-d') === $tomorrow
            ) {
                $attention_items[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'product' => $item->get_name(),
                    'msg' => 'Delivery scheduled for tomorrow.',
                    'status' => $status,
                    'data_entrega' => $data_entrega,
                    'hora_entrega' => $hora_entrega,
                ];
            }

            // 2. After delivery date/time, but still reserved (should be rented)
            if (
                $status === 'reserved' &&
                $entrega_full &&
                $entrega_full->format('Y-m-d H:i') <= $now->format('Y-m-d H:i')
            ) {
                $attention_items[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'product' => $item->get_name(),
                    'msg' => 'Should be "rented" after delivery time.',
                    'status' => $status,
                    'data_entrega' => $data_entrega,
                    'hora_entrega' => $hora_entrega,
                ];
            }

            // 3. After return date/time, but not finished
            if (
                in_array($status, ['reserved', 'rented']) &&
                $retorno_full &&
                $retorno_full->format('Y-m-d H:i') <= $now->format('Y-m-d H:i')
            ) {
                $attention_items[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'product' => $item->get_name(),
                    'msg' => 'Should be "finished" after return time.',
                    'status' => $status,
                    'data_retorno' => $data_retorno,
                    'hora_retorno' => $hora_retorno,
                ];
            }

            // 3b. Return tomorrow (attention)
            if (
                in_array($status, ['reserved', 'rented']) &&
                $retorno_full &&
                $retorno_full->format('Y-m-d') === $tomorrow
            ) {
                $attention_items[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'product' => $item->get_name(),
                    'msg' => 'Return scheduled for tomorrow.',
                    'status' => $status,
                    'data_retorno' => $data_retorno,
                    'hora_retorno' => $hora_retorno,
                ];
            }
        }
    }

    if (!empty($attention_items)) {
        echo '<div class="notice notice-warning notice-orders"><p><strong>Attention: Rental Items Requiring Action</strong></p><ul>';
        foreach ($attention_items as $att) {
            $edit_url = admin_url('post.php?post=' . $att['order_id'] . '&action=edit');
            echo '<li>';
            echo '<a href="' . esc_url($edit_url) . '">Order #' . esc_html($att['order_id']) . '</a> - ';
            echo esc_html($att['product']) . ': ';
            echo esc_html($att['msg']);
            if (!empty($att['data_entrega'])) {
                echo ' | Delivery: ' . esc_html($att['data_entrega']) . ' ' . esc_html($att['hora_entrega']);
            }
            if (!empty($att['data_retorno'])) {
                echo ' | Return: ' . esc_html($att['data_retorno']) . ' ' . esc_html($att['hora_retorno']);
            }
            echo ' | Status: ' . esc_html($att['status']);
            echo '</li>';
        }
        echo '</ul></div>';
    }
});

// --- END ADMIN NOTIFICATIONS ---

add_action('woocommerce_admin_order_item_headers', function () {
    echo '<th class="attention-status">Attention</th>';
});

add_action('woocommerce_admin_order_item_values', function ($product, $item, $item_id) {
    $data_entrega_raw = wc_get_order_item_meta($item_id, 'data_entrega');
    $hora_entrega_raw = wc_get_order_item_meta($item_id, 'hora_entrega');
    $data_retorno_raw = wc_get_order_item_meta($item_id, 'data_retorno');
    $hora_retorno_raw = wc_get_order_item_meta($item_id, 'hora_retorno');
    $status = wc_get_order_item_meta($item_id, 'disponibilidade');

    $now = new DateTime('now', wp_timezone());
    $today = $now->format('Y-m-d');
    $current_time = $now->format('H:i');
    $tomorrow = (clone $now)->modify('+1 day')->format('Y-m-d');
    $text = '';
    $needs_attention = false;

    // Delivery
    if ($data_entrega_raw) {
        $entrega_dt = DateTime::createFromFormat('Y-m-d', $data_entrega_raw, wp_timezone());
        $entrega_hora = $hora_entrega_raw ? $hora_entrega_raw : '00:00';
        $entrega_full = $entrega_dt ? clone $entrega_dt : false;
        if ($entrega_full) $entrega_full->modify($entrega_hora);

        if (
            $status === 'reserved' &&
            $entrega_full &&
            (
                ($entrega_full->format('Y-m-d') === $today && $entrega_hora >= $current_time) ||
                ($entrega_full->format('Y-m-d') < $today)
            )
        ) {
            $needs_attention = true;
            $text = '⚠️ Delivery today or overdue';
        }
        if (
            $status === 'reserved' &&
            $entrega_full &&
            $entrega_full->format('Y-m-d H:i') <= $now->format('Y-m-d H:i')
        ) {
            $needs_attention = true;
            $text = '⚠️ Should be "rented"';
        }
        // Delivery tomorrow
        if (
            $status === 'reserved' &&
            $entrega_full &&
            $entrega_full->format('Y-m-d') === $tomorrow
        ) {
            $needs_attention = true;
            $text = '⚠️ Delivery scheduled for tomorrow';
        }
    }

    // Return
    if ($data_retorno_raw) {
        $retorno_dt = DateTime::createFromFormat('Y-m-d', $data_retorno_raw, wp_timezone());
        $retorno_hora = $hora_retorno_raw ? $hora_retorno_raw : '00:00';
        $retorno_full = $retorno_dt ? clone $retorno_dt : false;
        if ($retorno_full) $retorno_full->modify($retorno_hora);

        if (
            in_array($status, ['reserved', 'rented']) &&
            $retorno_full &&
            $retorno_full->format('Y-m-d H:i') <= $now->format('Y-m-d H:i')
        ) {
            $needs_attention = true;
            $text = '⚠️ Should be "finished"';
        }
        // Return tomorrow
        if (
            in_array($status, ['reserved', 'rented']) &&
            $retorno_full &&
            $retorno_full->format('Y-m-d') === $tomorrow
        ) {
            $needs_attention = true;
            $text = '⚠️ Return scheduled for tomorrow';
        }
    }

    echo '<td>';
    if ($needs_attention) {
        echo '<span style="color: red; font-weight: bold;">' . esc_html($text) . '</span>';
    } else {
        echo '-';
    }
    echo '</td>';
}, 10, 3);

// ... Flat Rates code ...
function show_flat_rates_field() {
    global $post;
    $flat_rates = get_post_meta($post->ID, '_flat_rates', true);
    $flat_rates = is_array($flat_rates) ? $flat_rates : [];
    echo '<div class="options_group">';
    echo '<p class="form-field"><strong>Flat Rates by Period</strong></p>';
    echo '<div id="flat_rates_wrapper">';
    if (!empty($flat_rates)) {
        foreach ($flat_rates as $i => $item) {
            $dias = esc_attr($item['dias']);
            $preco = esc_attr($item['preco']);
            echo "<p><input type='number' min='1' step='1' name='flat_rates[{$i}][dias]' class='flat-rate-dias' placeholder='Days' value='{$dias}' style='width:80px;' /> ";
            echo "<input type='number' min='0.01' step='0.01' name='flat_rates[{$i}][preco]' class='flat-rate-preco' placeholder='Price' value='{$preco}' style='width:120px;' /> ";
            echo "<a href='#' class='remover_flat_rate'>Remove</a></p>";
        }
    }
    echo '</div>';
    echo '<button type="button" id="add_flat_rate" class="button">Add Flat Rate</button>';
    echo '<div id="flat_rate_error" style="color:red;display:none;"></div>';
    echo '</div>';
    ?>
    <script>
    jQuery(function($){
        let count = <?php echo count($flat_rates); ?>;
        $('#add_flat_rate').on('click', function(e){
            e.preventDefault();
            $('#flat_rates_wrapper').append(
                `<p><input type='number' min='1' step='1' name='flat_rates[${count}][dias]' class='flat-rate-dias' placeholder='Days' style='width:80px;' required oninvalid="this.setCustomValidity('Please enter a number of days or remove the flat rate')" oninput="this.setCustomValidity('')" /> \
                <input type='number' min='0.01' step='0.01' name='flat_rates[${count}][preco]' class='flat-rate-preco' placeholder='Price' style='width:120px;' required oninvalid="this.setCustomValidity('Please enter a price or remove the flat rate')" oninput="this.setCustomValidity('')" /> \
                <a href='#' class='remover_flat_rate'>Remove</a></p>`
            );
            count++;
        });
        $(document).on('click', '.remover_flat_rate', function(e){
            e.preventDefault();
            $(this).parent('p').remove();
        });
        // Validação antes de salvar
        $('form#post').on('submit', function(){
            let valid = true;
            let lastDias = 0, lastPreco = 0;
            $('#flat_rate_error').hide();
            $('.flat-rate-dias').each(function(i){
                let dias = parseInt($(this).val());
                let preco = parseFloat($(this).parent().find('.flat-rate-preco').val());
                if (i > 0 && (dias <= lastDias || preco <= lastPreco)) {
                    $('#flat_rate_error').text('Each new period must have days and price greater than the previous.').show();
                    valid = false;
                    return false;
                }
                lastDias = dias;
                lastPreco = preco;
            });
            if (!valid) return false;
        });
    });
    </script>
    <?php
}
add_action('woocommerce_product_options_general_product_data', 'show_flat_rates_field', 21);

function save_flat_rates_field($post_id) {
    if (isset($_POST['flat_rates']) && is_array($_POST['flat_rates'])) {
        $rates = array_values(array_filter($_POST['flat_rates'], function($item){
            return !empty($item['dias']) && !empty($item['preco']);
        }));
        // Garante ordem e não sobreposição
        $lastDias = 0; $lastPreco = 0; $ok = true;
        foreach ($rates as &$item) {
            $item['dias'] = max((int)$item['dias'], $lastDias+1);
            $item['preco'] = floatval($item['preco']);
            if ($item['dias'] <= $lastDias || $item['preco'] <= $lastPreco) $ok = false;
            $lastDias = $item['dias'];
            $lastPreco = $item['preco'];
        }
        if ($ok) {
            update_post_meta($post_id, '_flat_rates', $rates);
        }
    } else {
        delete_post_meta($post_id, '_flat_rates');
    }
}
add_action('woocommerce_process_product_meta', 'save_flat_rates_field');

add_action('before_delete_post', function($post_id) {
    $post_type = get_post_type($post_id);
    if ($post_type !== 'shop_order') return;

    $order = wc_get_order($post_id);
    if (!$order) return;

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product && $product->managing_stock()) {
            $qty = $item->get_quantity();
            $stock = $product->get_stock_quantity();
            $product->set_stock_quantity($stock + $qty);
            $product->save();
        }
    }
});

add_action('wp_trash_post', function($post_id) {
    $post_type = get_post_type($post_id);
    if ($post_type !== 'shop_order') return;

    $order = wc_get_order($post_id);
    if (!$order) return;

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product && $product->managing_stock()) {
            $qty = $item->get_quantity();
            $stock = $product->get_stock_quantity();
            $product->set_stock_quantity($stock + $qty);
            $product->save();
        }
    }
});

add_action('woocommerce_order_status_cancelled', function($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if ($product && $product->managing_stock()) {
            $qty = $item->get_quantity();
            $stock = $product->get_stock_quantity();
            $product->set_stock_quantity($stock + $qty);
            $product->save();
        }
    }
});

