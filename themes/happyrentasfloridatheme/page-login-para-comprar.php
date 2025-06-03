<?php
/**
 * Template Name: Login para Comprar
 */

get_header(); ?>

<div class="login-cadastro-wrapper" style="display: flex; justify-content: space-around; padding: 40px;">
    <div class="login-box">
        <h3>Faça login na sua conta</h3>
        <?php
        wp_login_form([
            'redirect' => wc_get_checkout_url()
        ]);
        ?>
        <a href="<?php echo wp_lostpassword_url(); ?>">Esqueceu sua senha?</a>
    </div>

    <div class="login-divider" style="display: flex; align-items: center; justify-content: center;">
        <strong>OU</strong>
    </div>

    <div class="register-box">
        <h3>Crie um novo usuário</h3>
        <form method="post">
            <input type="text" name="nome" placeholder="Digite seu primeiro nome" required><br>
            <input type="email" name="email" placeholder="Digite seu e-mail" required><br>
            <input type="password" name="senha" placeholder="Crie uma senha" required><br>
            <button type="submit" name="registrar_usuario">Cadastrar-se Agora</button>
        </form>
    </div>
</div>

<?php
// Registro do novo usuário
if (isset($_POST['registrar_usuario'])) {
    $email = sanitize_email($_POST['email']);
    $senha = $_POST['senha'];
    $nome = sanitize_text_field($_POST['nome']);

    if (!email_exists($email)) {
        $user_id = wp_create_user($email, $senha, $email);
        wp_update_user(['ID' => $user_id, 'display_name' => $nome, 'first_name' => $nome]);

        // Loga o usuário
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        wp_redirect(wc_get_checkout_url());
        exit;
    } else {
        echo "<p style='color:red'>Este e-mail já está cadastrado.</p>";
    }
}
?>

<?php get_footer(); ?>
