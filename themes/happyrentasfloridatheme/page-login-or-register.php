<?php
/**
 * Template Name: Login or Register
 */

defined('ABSPATH') || exit;

// FORM PROCESSING
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Login
    if (isset($_POST['login_submit']) && isset($_POST['login_nonce']) && wp_verify_nonce($_POST['login_nonce'], 'login_user')) {
        $creds = [
            'user_login'    => sanitize_email($_POST['login_email']),
            'user_password' => $_POST['login_password'],
            'remember'      => true,
        ];

        $user = wp_signon($creds, false);
        if (!is_wp_error($user)) {
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);

            if ( current_user_can( 'custom_admin') || current_user_can('administrator')) {
                wp_redirect(admin_url('admin.php?page=wc-admin&path=%2Fanalytics%2Foverview'));
            } else {
                wp_redirect(site_url('my-account'));
            }
            exit;
        } else {
            $login_error = 'Incorrect email or password.';
        }

    }

    // Register
    if (isset($_POST['register_submit']) && isset($_POST['register_nonce']) && wp_verify_nonce($_POST['register_nonce'], 'register_user')) {
        $email = sanitize_email($_POST['register_email']);
        $name = sanitize_text_field($_POST['register_name']);
        $password = $_POST['register_password'];

        if (!email_exists($email)) {
            $user_id = wp_create_user($email, $password, $email);
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $name,
                'first_name' => $name,
            ]);

            wp_signon([
                'user_login'    => $email,
                'user_password' => $password,
                'remember'      => true,
            ], false);

            wp_redirect(site_url('my-account'));
            exit;
        } else {
            $register_error = 'This email is already registered.';
        }
    }
}

get_header();
?>

<style>
    .login-container {
        max-width: 960px;
        margin: 80px auto;
        background: #fff;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        padding: 40px;
        border-radius: 15px;
        font-family: 'Segoe UI', sans-serif;
    }

    .login-title {
        text-align: center;
        font-size: 36px;
        margin-bottom: 30px;
        color: #2fa8ff;
    }

    .login-row {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
    }

    .login-box {
        flex: 1 1 400px;
        padding: 20px;
        background: #f8f8f8;
        border-radius: 12px;
    }

    .login-box h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .login-box input {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    .login-box button {
        width: 100%;
        background: #2fa8ff;
        color: white;
        border: none;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
    }

    .login-box button:hover {
        background: #1c90e3;
    }

    .success-msg, .error-msg {
        text-align: center;
        margin-bottom: 15px;
        font-weight: bold;
    }

    .success-msg { color: green; }
    .error-msg { color: red; }

    .social-login {
        text-align: center;
        margin-bottom: 15px;
    }
    .social-login .google-btn {
        background: #fff;
        color: #444;
        border: 1px solid #ccc;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        transition: background 0.2s;
    }
    .social-login .google-btn:hover {
        background: #f1f1f1;
    }
</style>

<section data-bs-version="5.1" class="mbr-section content4 cid-uGHKU5vod  cid-uGAR25mwdC" id="content4-2l">
    <div class="container">
        <div class="media-container-row">
            <div class="title col-12 col-md-8"></div>
        </div>
    </div>
</section>

<div class="login-container">
    <h2 class="login-title">🚀 Login or Register</h2>
    <div class="login-row">
        <!-- Login -->
        <div class="login-box">
            <h3>Already have an account?</h3>

            <?php if (isset($login_error)) echo '<p class="error-msg">'.$login_error.'</p>'; ?>

            <!--<div class="social-login">
                <?php
                /*Example for Nextend Social Login
                    if (function_exists('nextend_social_login_button')) {
                        nextend_social_login_button('google');
                    } else {
                        // Fallback: link to wp-login.php?action=oauthlogin&provider=Google (for WP OAuth or similar)
                        echo '<a class="google-btn" href="'.esc_url( site_url('/wp-login.php?loginSocial=google') ).'"><img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20">Sign in with Google</a>';
                    }
               */ ?>
            </div>*-->

            <form method="post">
                <?php wp_nonce_field('login_user', 'login_nonce'); ?>
                <input type="email" name="login_email" placeholder="Email" required>
                <div style="position: relative;">
                    <input type="password" name="login_password" placeholder="Password" required id="login_password">
                    <span class="toggle-password" toggle="#login_password" style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer;">👁️</span>
                </div>
                <button type="submit" name="login_submit">Sign In</button>
            </form>
        </div>

        <!-- Register -->
        <div class="login-box">
            <h3>Register now</h3>

            <?php if (isset($register_error)) echo '<p class="error-msg">'.$register_error.'</p>'; ?>

            <!--<div class="social-login">
                <?php
                /* Example for Nextend Social Login
                if (function_exists('nextend_social_login_button')) {
                    nextend_social_login_button('google');
                } else {
                    // Fallback: link to wp-login.php?action=oauthlogin&provider=Google (for WP OAuth or similar)
                    echo '<a class="google-btn" href="'.esc_url( site_url('/wp-login.php?loginSocial=google') ).'"><img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20">Register with Google</a>';
                }
                */?>
            </div>-->

            <form method="post">
                <?php wp_nonce_field('register_user', 'register_nonce'); ?>
                <input type="text" name="register_name" placeholder="Full name" required>
                <input type="email" name="register_email" placeholder="Email" required>
                <div style="position: relative;">
                    <input type="password" name="register_password" placeholder="Password" required id="register_password">
                    <span class="toggle-password" toggle="#register_password" style="position:absolute; right:15px; top:50%; transform:translateY(-50%); cursor:pointer;">👁️</span>
                </div>
                <button type="submit" name="register_submit">Register</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(function(el) {
        el.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('toggle'));
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    });
</script>

<?php get_footer(); ?>
