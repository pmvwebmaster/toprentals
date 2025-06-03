<?php
/*
Template Name: Checkout Customizado
*/

get_header(); ?>

<div class="container">
    <h1>Finalizar Compra</h1>
    <?php echo do_shortcode('[woocommerce_checkout]'); ?>
</div>

<?php get_footer(); ?>
