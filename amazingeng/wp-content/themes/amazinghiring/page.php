<?php
/*
Template Name: Page
*/

get_header(); ?>	

	<div class="container margin-top">		
		<section class="left-content">
			<div class="content">
				<?php
				    // TO SHOW THE PAGE CONTENTS
				    while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
			            <?php the_content(); ?> <!-- Page Content -->				        
				    <?php
				    endwhile; //resetting the page loop
				    wp_reset_query(); //resetting the page query
				    ?>
			</div>			
		</section>
		
		<?php get_sidebar(); ?>
		
		<div class="clearfix"></div>		
	</div>	

	<div class="container margin-top">
		<?php get_template_part( 'sidebar_mobile', 'none' ); ?>
	</div>

<?php get_footer(); ?>