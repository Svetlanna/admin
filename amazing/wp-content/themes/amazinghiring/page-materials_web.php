<?php
/*
Template Name: Materials
*/
?>
<?php //get_header(); ?>
<?php get_template_part('header'); ?>

<?php
$last = new WP_Query( array( 'post_type' => array('materials_web')) );

$i = 0;
if ( $last->have_posts() ) :    	
    while ( $last->have_posts() ) : $last->the_post(); ?>
	<?php 
		$i++;
		$field = get_post_meta( get_the_ID(), 'image', true );
		$field_url = wp_get_attachment_url( $field );
	?>
	<?php if ($i == 1) { ?>
		<div class="recent-material-post">
			<div class="recent-material-wrap">
				<div class="recent-material-text">
					<h4>Материал:</h4>
					<h2><?php the_title(); ?></h2>				
				</div>
				<div class="recent-material-img">
					<?php echo '<img src="'.$field_url.'">'; ?>
				</div>
				<div class="clearfix"></div>
				<div class="recent-material-link-wrap">
					<a href="<?php the_permalink(); ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Скачать</a>
				</div>
			</div>
		</div>
	<?php } ?>
    <?php endwhile; ?>
<?php endif; wp_reset_postdata(); ?>

<div class="material-container">
	<h4>Полезные материалы:</h4>
	<?php
	    $loop = new WP_Query( array( 'post_type' => array('materials_web')) );
	    if ( $loop->have_posts() ) :
	        while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<?php get_template_part('loop-materials_web'); ?>
    <?php endwhile; ?>
    <?php endif; wp_reset_postdata(); ?>    
</div>
<?php get_footer(); ?>