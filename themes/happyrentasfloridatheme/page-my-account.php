<?php

 defined('ABSPATH') || exit;

 if ( !is_user_logged_in() ) {
    wp_redirect( site_url('login-or-register') );
    exit;
 }

 $current_user = wp_get_current_user();

 if ( current_user_can( 'custom_admin') || current_user_can('administrator')) {
    wp_redirect( admin_url('admin.php?page=wc-orders') );
    exit;
 }

 // Continue with the rest of the page as normal...
 get_header();
 ?>

<style>
    .account-container {
       max-width: 1140px;
       margin: 60px auto;
       padding: 30px;
       background: #ffffff;
       box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
       border-radius: 12px;
       font-family: 'Segoe UI', sans-serif;
    }

    .account-tabs {
       display: flex;
       flex-wrap: wrap;
       gap: 15px;
       margin-bottom: 30px;
       justify-content: center;
    }

    .account-tabs a {
       flex: 1 1 200px;
       text-align: center;
       padding: 12px 20px;
       border-radius: 10px;
       background: #f3f3f3;
       color: #333;
       text-decoration: none;
       font-weight: 600;
       transition: all 0.2s ease-in-out;
       box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .account-tabs a:hover {
       background-color: #2fa8ff;
       color: white;
       box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .account-title {
       text-align: center;
       font-size: 32px;
       margin-bottom: 40px;
       color: #2fa8ff;
    }

    @media screen and (max-width: 768px) {
       .account-tabs a {
          flex: 1 1 100%;
       }
    }
</style>





    <div class="container-fluid">
       <div class="row">
          <div class="col-12">
             <div class="content-wrapper">





             </div>
          </div>
       </div>
    </div>
</section>

<section data-bs-version="5.1" class="mbr-section content4 cid-uGHKU5vod  cid-uGAR25mwdC" id="content4-2l">



    <div class="container">
       <div class="media-container-row">
          <div class="title col-12 col-md-8">



          </div>
       </div>
    </div>
</section>

<div class="account-container">
    <h2 class="account-title">👤 My Account</h2>

    <div class="account-tabs">
       <!-- <a href="<?php echo esc_url( wc_get_endpoint_url( 'dashboard', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
          🏠 Dashboard
       </a> -->
       <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
          🧾 Orders
       </a>
       <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
          📝 My Details
       </a>
       <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
          📍 Addresses
       </a>
       <a href="<?php echo esc_url( wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>">
          🚪 Logout
       </a>
    </div>

    <div class="woocommerce">
       <?php
          do_action( 'woocommerce_account_content' );
       ?>
    </div>
</div>

<?php get_footer(); ?>
