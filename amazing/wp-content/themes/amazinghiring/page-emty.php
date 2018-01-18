<?php
/*
Template Name: Empty
*/
?>
<?php get_template_part('header'); ?>

<?php
	remove_filter( 'the_content', 'wpautop' );
    
    while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?> <!-- Page Content -->        
    <?php
    endwhile;
    wp_reset_query(); //resetting the page query
?>


<?php get_footer(); ?>