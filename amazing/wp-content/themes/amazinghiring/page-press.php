<?php
/*
Template Name: Press
*/
?>

<?php get_template_part('header'); ?>

<section class="parallax-window-page" data-parallax="scroll" data-image-src="http://amazinghiring.com/blog/wp-content/uploads/2016/12/press-bg.jpg">
	<div class="wrap">
		<div class="container">
			<div class="hithere"><span><?php the_title(); ?></span></div>
		</div>
	</div>
</section>

<div class="container empty-page-template">
	<div class="article-wrapper">
	<?php
	    $loop = new WP_Query( array( 'post_type' => array('press_web')) );
	    if ( $loop->have_posts() ) :
	        while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<?php get_template_part('loop-press_web'); ?>
	<?php endwhile; ?>
	<?php endif; wp_reset_postdata(); ?>
	</div>
</div>

<div class="about-publications-wrapper">
	<div class="about-publications-block">

	<?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
    <?php
    endwhile;
    wp_reset_query(); ?>
	</div>
</div>

<?php get_footer(); ?>