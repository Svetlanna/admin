<?php 
/*
Template Name: Home
*/
?>
<?php get_header(); ?>
<?php get_template_part( 'pop-up', 'none' ); ?>

	<div class="container margin-top">
		
		

		<section class="left-content">
			<?php 
			$urlTag = $_SERVER["REQUEST_URI"];

			$isItTag = strpos($urlTag, 'tag');

			if ($isItTag!==false)
			{ ?>
				<div class="row-posts mainpage-posts">
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
							<?php 
							$url = $_SERVER["REQUEST_URI"];

							$isItEn = strpos($url, 'en');

							if ($isItEn!==false)
							{?>
								<input type="text" name="s" placeholder="Search" value="<?php if(!empty($_GET['s'])){echo $_GET['s'];}?>"/>
							<?php }else{?>
							<input type="text" name="s" placeholder="Поиск" value="<?php if(!empty($_GET['s'])){echo $_GET['s'];}?>"/>
							<?php }	?>
							<input type="submit" value=""/>
						</div>
					</form>
				</aside>

				<?php if (function_exists('wp_corenavi')) wp_corenavi(); ?>

			<?php }else{?>
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
									<div class="date"><?php the_time('d.m.Y') ?></div>
									<div class="views"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></div>
								</div>
								<div class="anons">
									<?php the_truncated_post( 300 ); ?>
									
								</div>
								<div class="read-more-wrap">						
									<?php 
									$url = $_SERVER["REQUEST_URI"];

									$isItEn = strpos($url, 'en');

									if ($isItEn!==false)
									{ ?>
										<a href="<?php  the_permalink(); ?>" class="read-more mdl-button mdl-js-button mdl-button--primary">Read more</a>
									<?php }else{?>
										<a href="<?php  the_permalink(); ?>" class="read-more mdl-button mdl-js-button mdl-button--primary">Читать далее</a>
									<?php }	?>	
								</div>
							</div>
						</div>
				 	<?php } ?>			 
					<?php endwhile; ?>
				<?php endif; ?>	
				
				<div class="row-posts mainpage-posts">
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
							<?php 
							$url = $_SERVER["REQUEST_URI"];

							$isItEn = strpos($url, 'en');

							if ($isItEn!==false)
							{?>
								<input type="text" name="s" placeholder="Search" value="<?php if(!empty($_GET['s'])){echo $_GET['s'];}?>"/>
							<?php }else{?>
							<input type="text" name="s" placeholder="Поиск" value="<?php if(!empty($_GET['s'])){echo $_GET['s'];}?>"/>
							<?php }	?>
							<input type="submit" value=""/>
						</div>
					</form>
				</aside>

				<?php if (function_exists('wp_corenavi')) wp_corenavi(); ?>
			<?php }	?>
		</section>
		
		<?php get_sidebar(); ?>
		
	</div>
<?php get_template_part( 'sidebar_mobile', 'none' ); ?>
<?php get_template_part( 'pop-down', 'none' ); ?>
<?php get_footer(); ?>