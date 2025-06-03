/* Template Name: Funcionamento */
<?php get_header(); ?>
<?php
$funcionamento = get_field('funcionamento');

?>


<div class="container">
<div class="row align-items-center">
    <div class="col-12 col-lg-6">
        <div class="image-wrapper">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/f047d7ec-cf9a-4166-822a-bd3809a7f5e4-1256x837.webp" alt="TOP Rentals">
            
        </div>
    </div>
    <div class="col-12 col-lg">
        <div class="text-wrapper">
            <h3 class="mbr-section-title mbr-fonts-style mb-3 display-5"><strong>Como Funciona</strong></h3>
            <p class="mbr-text mbr-fonts-style display-7"><strong>Faça sua reserva online 
                </strong><br>Escolha seu equipamento e selecione as datas desejadas.
                <br>
                <br><strong>Entrega e retirada fáceis 
                </strong><br>Entregamos seu carrinho ou scooter no seu hotel ou local combinado.
                <br>
                <br><strong>Aproveite sem preocupaçõe</strong>s 
                <br>Todos os nossos equipamentos são higienizados e prontos para uso!
                <br>
                <br><strong>Use o tempo que quiser
                </strong><br>Contrate o tempo que você vai se divertir e esqueça o resto!</p>
        </div>
    </div>
</div>
</div>
</section>
<?php get_footer();?>