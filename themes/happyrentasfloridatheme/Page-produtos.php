<?php
/* Template Name: Página de Produtos */
get_header();




?>

<section data-bs-version="5.1" class="header02 trustm5 cid-uGAQKqFBUq mbr-parallax-background" id="header02-1d">
    

    
    <div class="mbr-overlay" style="opacity: 0.5; background-color: rgb(181, 31, 31);"></div>

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

<section data-bs-version="5.1" class="mbr-section content4 cid-uGB1oxoTFh" id="content4-1g">

    

    <div class="container">
        <div class="media-container-row">
            <div class="title col-12 col-md-8">
                <h2 class="align-center pb-3 mbr-fonts-style display-5">
                    
                <div><strong>NOSSOS PRODUTOS</strong></div></h2>
                
                
            </div>
        </div>
    </div>
</section>

<section data-bs-version="5.1" class="features4 start cid-uGAR25mwdC" id="features04-1e">
	
	
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 content-head">
				<div class="mbr-section-head mb-5">
					<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2"><strong>Scooters Elétricas</strong></h4>
					<h5 class="mbr-section-subtitle mbr-fonts-style align-center mb-0 mt-4 display-7">Oferecemos scooters seguras e potentes para facilitar sua locomoção nos parques.
<div>&nbsp;•	Bateria de longa duração
</div><div>&nbsp;•	Assento acolchoado e confortável
</div><div>&nbsp;•	Controle intuitivo e fácil de operar
</div><div><br></div></h5>
					
				</div>
			</div>
		</div>
		<div class="row justify-content-center">
            <?php
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'product_cat'    => 'mobility-scooters',
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                );
                
                $loop = new WP_Query($args);
                while ($loop->have_posts()) : $loop->the_post();
                global $product;
            ?>
                <div class="item features-image col-12 col-md-6 col-lg-3 active">
                    <div class="item-wrapper">
                        <div class="item-img">
                            <img src="<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>" alt="<?php the_title(); ?>" data-slide-to="1" data-bs-slide-to="1">
                        </div>
                        <div class="item-content">
                            <h5 class="item-title mbr-fonts-style display-7"><strong><?php the_title(); ?></strong></h5>
                            
                            <div class="mbr-section-btn item-footer"><a href="<?php the_permalink(); ?>" class="btn item-btn btn-primary display-7">Alugue</a></div>
                        </div>

                    </div>
                </div>
            <?php endwhile;
            wp_reset_query(); ?>
		
		</div>
	</div>
</section>

<section data-bs-version="5.1" class="features4 start cid-uGB3WNprXn" id="features04-1h">
	
	
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 content-head">
				<div class="mbr-section-head mb-5">
					<h4 class="mbr-section-title mbr-fonts-style align-center mb-0 display-2"><strong>Carrinhos de bebê</strong></h4>
					
					
				</div>
			</div>
		</div>
		<div class="row justify-content-center">
            <?php
                   $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => -1,
                        'product_cat'    => 'baby-strollers',
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                    );
                
                    $loop = new WP_Query($args);
                    while ($loop->have_posts()) : $loop->the_post();
                    global $product;
                ?>
                <div class="item features-image col-12 col-md-6 col-lg-3 active">
                    <div class="item-wrapper">
                        <div class="item-img">
                           <img src="<?php echo wp_get_attachment_url( $product->get_image_id() ); ?> " alt="<?php the_title(); ?>" data-slide-to="1" data-bs-slide-to="1">
                        </div>
                        <div class="item-content">
                            <h5 class="item-title mbr-fonts-style display-7"><strong><?php the_title(); ?></strong></h5>
                            
                            <div class="mbr-section-btn item-footer"><a href="<?php the_permalink(); ?>" class="btn item-btn btn-primary display-7">Alugue</a></div>
                        </div>

                    </div>
                </div>
            <?php endwhile;
            wp_reset_query(); ?>
			
			</div>
		</div>
	</div>
</section>





<?php
get_footer();
?>
