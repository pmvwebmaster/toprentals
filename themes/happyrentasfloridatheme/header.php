<?php
    $url_minha_conta = get_permalink( get_option('woocommerce_myaccount_page_id') );
    $usuario_logado = is_user_logged_in();
    $texto_botao = $usuario_logado ? 'Account' : 'Login';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/logo-sugesto-02.jpg-128x123.png" type="image/x-icon">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="keywords" content="scooter rentals, mobility scooters, Orlando, strollers, rental, top rentas, Florida, travel, vacation, accessibility">
    <meta name="author" content="Top Rentas Scooters">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <link rel="canonical" href="<?php echo esc_url( ( is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) ) ) ); ?>" />
    <?php if ( is_singular() ) : ?>
        <meta property="og:title" content="<?php the_title_attribute(); ?>" />
        <meta property="og:description" content="<?php echo esc_attr( get_the_excerpt() ); ?>" />
        <meta property="og:url" content="<?php the_permalink(); ?>" />
        <meta property="og:type" content="article" />
        <?php if ( has_post_thumbnail() ) : ?>
            <meta property="og:image" content="<?php echo get_the_post_thumbnail_url( null, 'large' ); ?>" />
        <?php endif; ?>
    <?php else : ?>
        <meta property="og:title" content="<?php bloginfo('name'); ?>" />
        <meta property="og:description" content="<?php bloginfo('description'); ?>" />
        <meta property="og:url" content="<?php echo home_url(); ?>" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/logo-02-transp.png-455x236.png" />
    <?php endif; ?>
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php wp_title('|', true, 'right'); bloginfo('name'); ?>" />
    <meta name="twitter:description" content="<?php bloginfo('description'); ?>" />
    <meta name="twitter:image" content="<?php echo get_template_directory_uri(); ?>/assets/images/logo-02-transp.png-455x236.png" />

    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/simple-line-icons/simple-line-icons.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/bootstrap/css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/bootstrap/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/parallax/jarallax.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/animatecss/animate.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/chatbutton/floating-wpp.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/popup-overlay-plugin/style.css">
    <!--<link rel="stylesheet" href="<?php //echo get_template_directory_uri(); ?>/assets/smart-cart/minicart-theme.css">-->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/dropdown/css/style.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/socicon/css/styles.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/theme/css/style.css">

    <link rel="preload" href="https://fonts.googleapis.com/css?family=Inter+Tight:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter+Tight:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap"></noscript>
    <link rel="preload" as="style" href="<?php echo get_template_directory_uri(); ?>/assets/mobirise/css/mbr-additional.css?v=ResYi8">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/mobirise/css/mbr-additional.css?v=ResYi8" type="text/css">
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css">
</head>

<body <?php body_class(); ?>>
    <section data-bs-version="5.1" class="menu menu1 cid-uLcMJyzaMN" once="menu" id="menu1-4f">
        <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
            <?php
            $cart_count = 0;
            if (function_exists('WC') && WC()->cart) {
                $cart_count = WC()->cart->get_cart_contents_count();
            }
            ?>
            <h3 class="menu-tite mbr-fonts-style display-4">
              <div class="right-wrap d-flex align-items-center">
                  <a class="skiptranslate" href="/cart" >
                      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png" alt="Cart" style="width:32px;">
                      <span id="cart-count-badge" style="position:absolute;top:-8px;right:-8px;background:#ff6a00;color:#fff;font-size:13px;font-weight:bold;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,0.12);z-index:2;">
                        <?php echo $cart_count; ?>
                      </span>
                  </a>

                  <div class="gtranslate_wrapper"><?php echo do_shortcode('[gtranslate]'); ?></div>
              </div>
            </h3>
            <script>
            // Atualiza badge do carrinho via AJAX (WooCommerce)
            document.addEventListener('DOMContentLoaded', function() {
              function updateCartBadge(count) {
                var badge = document.getElementById('cart-count-badge');
                if (badge) badge.textContent = count;
              }
              if (window.jQuery && typeof wc_cart_fragments_params !== 'undefined') {
                jQuery(document.body).on('added_to_cart removed_from_cart updated_cart_totals wc_fragments_refreshed', function() {
                  jQuery.get('<?php echo admin_url('admin-ajax.php'); ?>', {action:'get_cart_count'}, function(resp){
                    if(resp && typeof resp.count !== 'undefined') updateCartBadge(resp.count);
                  });
                });
              }
            });
            </script>
            <div class="container">
                <div class="navbar-brand">
                    <span class="navbar-logo">
                        <a href="<?php echo home_url(); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-02-transp.png-455x236.png" alt="" style="height: 8rem;">
                        </a>
                    </span>
                    <span class="navbar-caption-wrap skiptranslate">
                        <a class="navbar-caption text-primary display-7" href="#">Rentals Scooters</a>
                    </span>
                </div>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-toggle="collapse" data-target="#navbarSupportedContent" data-bs-target="#navbarSupportedContent" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <div class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav nav-dropdown nav-right" data-app-modern-menu="true">
                            <li class="nav-item skiptranslate"><a class="nav-link link text-primary display-4" href="<?php echo home_url(); ?>">Home</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/about-us'); ?>" aria-expanded="false">About Us</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/products'); ?>" aria-expanded="false">Products</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/how-it-works'); ?>">How it Works</a></li>
                            <li class="nav-item skiptranslate"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/faq'); ?>">FAQ</a></li>
                            <li class="nav-item"><a class="nav-link link text-primary display-4" href="<?php echo home_url('/contact'); ?>">Contacts</a></li>
                        </ul>
                        <div class="account-link text-center mt-2">
                        <a href="<?php echo $url_minha_conta; ?>" style="text-decoration: none; color: inherit;">
                            <div class="nav-link link text-primary display-4" style="font-size: 0.9rem;">
                                👤
                                <?php echo $texto_botao; ?>
                            </div>
                        </a>
                    </div>

            </div>
        </nav>
    </section>

    <?php
    // List of slugs used in the navbar menu
    $menu_slugs = [
        '', // Home
        'about-us',
        'products',
        'how-it-works',
        'faq',
        'contact'
    ];

    // Get current page slug
    global $post;
    $current_slug = '';
    if (is_front_page() || is_home()) {
        $current_slug = '';
    } elseif (isset($post->post_name)) {
        $current_slug = $post->post_name;
    }

    // Show section only if current slug is NOT in menu slugs
    if (!in_array($current_slug, $menu_slugs)) :
    ?>
    <section data-bs-version="5.1" class="header02 trustm5 cid-uH94Fx8XKn mbr-parallax-background" id="header02-2v">
        <div class="mbr-overlay" style="opacity: 0.5; background-color:rgb(12, 44, 104);"></div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="content-wrapper">
                        <!-- Custom content if needed -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
