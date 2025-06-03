<?php
/*
Template Name: Carrinho Customizado
*/

get_header();

defined('ABSPATH') || exit;

?>




<div class="mbr-overlay" style="opacity: 0.5; background-color:rgb(12, 44, 104);"></div>

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

<section class="mbr-section content4 cid-uGH6gRSw1V" id="content4-1t">
    <div class="container">
        <h1>My Cart</h1>
        <?php
        do_action('woocommerce_before_cart'); // Necessário
        echo do_shortcode('[woocommerce_cart]'); 
        do_action('woocommerce_after_cart');  // Necessário
        ?>

    </div>
</section>

<?php get_footer(); ?>
