<?php
/*
 * Template name: Blog
 */
get_header(); ?>
<?php get_template_part( 'pop-up', 'none' ); ?>

	<div class="container margin-top">
		
		<?php get_template_part( 'sidebar_mobile', 'none' ); ?>

		<section class="left-content">
			<?php $i = 0; ?>
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
				<?php $i++ ?>
			 	<?php if ($i == 1) { ?>
					<div class="main-post">
						<div class="pic">
							<?php the_post_thumbnail() ?>
						</div>
						<div class="txt">
							<a href="<?php  the_permalink(); ?>" class="title"><?php the_title(); ?></a>
							<div class="info">
								<div class="date">27.08.2016</div>
								<div class="views"><i class="fa fa-eye"></i> 172</div>
							</div>
							<div class="anons">
								<?php the_truncated_post( 300 ); ?>
								
							</div>
							<div class="read-more-wrap">						
								<a href="<?php  the_permalink(); ?>" class="read-more mdl-button mdl-js-button mdl-button--primary">Читать далее</a>					
							</div>
						</div>
					</div>
			 	<?php } ?>			 
				<?php endwhile; ?>
			<?php endif; ?>	
			
			<div class="row-posts">
				<?php 
					if ( have_posts() ) : while ( have_posts() ) : the_post();					
		  	
						get_template_part( 'content', get_post_format() );
		  
					endwhile; endif; 
				?>
				
				
			</div>

			<div class="clearfix"></div>

			<aside class="mobile-right-content">
				<form action="<?php bloginfo( 'url' ); ?>" method="get">
					<div class="search">
						<input type="text" name="s" placeholder="Поиск" value="<?php if(!empty($_GET['s'])){echo $_GET['s'];}?>"/>
						<input type="submit" value=""/>
					</div>
				</form>
			</aside>			

		</section>
		
		<?php get_sidebar(); ?>
		
	</div>
<?php get_template_part( 'pop-down', 'none' ); ?>
<?php get_footer(); ?>