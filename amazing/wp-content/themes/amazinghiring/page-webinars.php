<?php
/*
Template Name: Webinars
*/
?>
<?php //get_header(); ?>
<?php get_template_part('header'); ?>

<div class="page-webinars">
	<?php echo do_shortcode('[Webinar_post_top]'); ?>
</div>
<?php /*
$args = array(
	 'numberposts' => 1,
	 'post_type' => 'webinars',
	 'post_status' => 'publish'
); 

$result = wp_get_recent_posts($args);

foreach( $result as $p ) { ?>

	<div class="recent-material-post">
		<div class="recent-material-wrap">
			<div class="recent-material-text">
				<h4>Вебинар:</h4>
				<h2><?php echo $p['post_title'] ?></h2>				
			</div>
			<div class="recent-material-img">
				<img src="/blog/wp-content/themes/amazinghiring/img/recent-img.png" alt="">
			</div>
			<div class="clearfix"></div>
			<div class="recent-material-link-wrap">
				<a href="<?php echo get_permalink($p['ID']) ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Скачать</a>
			</div>
		</div>
	</div>

<?php } */?>

<div class="material-container webinars-wrap">
	<h4>Прошедшие вебинары:</h4>
	<?php
	    $loop = new WP_Query( array( 'post_type' => array('webinars')) );
	    if ( $loop->have_posts() ) :
	        while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<?php get_template_part('loop-webinars'); ?>
    <?php endwhile; ?>

 
<nav>
    <?php previous_posts_link('&laquo; След.') ?>
    <?php next_posts_link('Пред. &raquo;') ?>
</nav>
    <?php endif; wp_reset_postdata(); ?>  

    <?php if (function_exists('wp_corenavi')) wp_corenavi(); ?>  
</div>
<?php get_footer(); ?>