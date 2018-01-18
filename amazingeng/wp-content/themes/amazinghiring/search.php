<?php get_header(); ?>

<div class="container margin-top">

	<?php get_template_part( 'sidebar_mobile', 'none' ); ?>

	<section class="left-content">
		<h1>Поиск по: "<?php echo $_GET['s'];?>"</h1>
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			

			<div class="main-post">
				
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

						$isItEn = strpos($url, '/');

						if ($isItEn!==false)
						{ ?>
							<a href="<?php  the_permalink(); ?>" class="read-more mdl-button mdl-js-button mdl-button--primary">Read more</a>
						<?php }else{?>
							<a href="<?php  the_permalink(); ?>" class="read-more mdl-button mdl-js-button mdl-button--primary">Читать далее</a>
						<?php }	?>	
					</div>
				</div>
			</div>
		<?php endwhile; else: ?>
			<p>Поиск не дал результатов.</p>
		<?php endif;?>

	</section>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
