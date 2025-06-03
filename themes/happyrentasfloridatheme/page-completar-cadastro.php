<?php
/**
 * Template Name: Completar Cadastro
 */

get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['completar_cadastro'])) {
    update_user_meta($user_id, 'billing_first_name', sanitize_text_field($_POST['first_name']));
    update_user_meta($user_id, 'billing_last_name', sanitize_text_field($_POST['last_name']));
    update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['phone']));
    update_user_meta($user_id, 'billing_address_1', sanitize_text_field($_POST['address']));
    update_user_meta($user_id, 'billing_city', sanitize_text_field($_POST['city']));
    update_user_meta($user_id, 'billing_postcode', sanitize_text_field($_POST['postcode']));
    update_user_meta($user_id, 'billing_country', sanitize_text_field($_POST['country']));

    wp_redirect(wc_get_checkout_url());
    exit;
}
?>

<h2>Complete seu cadastro</h2>
<form method="post">
    <input type="text" name="first_name" placeholder="Nome" required><br>
    <input type="text" name="last_name" placeholder="Sobrenome" required><br>
    <input type="text" name="phone" placeholder="Telefone" required><br>
    <input type="text" name="address" placeholder="Endereço" required><br>
    <input type="text" name="city" placeholder="Cidade" required><br>
    <input type="text" name="postcode" placeholder="CEP" required><br>
    <input type="text" name="country" placeholder="País" required><br>
    <button type="submit" name="completar_cadastro">Salvar e Continuar</button>
</form>

<?php get_footer(); ?>
