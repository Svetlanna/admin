<?php get_header(); ?>	


	<?php $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');  ?>

<div style="display: block; position: relative;">
	<section data-parallax="scroll" data-image-src="<?= $image_url[0]; ?>" data-ios-fix="false" class="parallax-window">

		<div class="wrap">
			<div class="container">
			    <h1><?php the_title(); ?></h1>			    
			    <h3><?php the_field('Подзаголовок'); ?></h3>
			    <div class="info">
					<div class="date"><?php the_time('d.m.Y') ?></div>
					<div class="views"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></div>
				</div>
				
				<?php
				$posttags = get_the_tags();
				if ($posttags) {	
					echo "<div class='tags'>";
					foreach($posttags as $tag) {
					    echo '<a class="tag" href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a>';
					}
					echo "</div>";
				}
				?>
			</div>
		</div>
	</section>
</div>
<?php get_template_part( 'pop-up', 'none' ); ?>
	<div class="container margin-top">		
		<section class="left-content">
			<div class="content">
				<div class="hide-img"><?= $image_url[0]; ?></div>
				<?php 
					the_post();
					the_content(); 
				?>
			</div>
			<div class="socials">				

				<?php 
				$url = $_SERVER["REQUEST_URI"];

				$isItEn = strpos($url, 'en');

				if ($isItEn!==false)
				{ ?>
					<span class="share-title">Share:</span>
				<?php }else{?>
					<span class="share-title">Поделиться:</span>
				<?php }	?>
				
				<div class="ya-share2" data-services="facebook,twitter,linkedin"></div>
			</div>
		</section>
		
		<?php get_sidebar(); ?>
		
		<div class="clearfix"></div>
		<div class="look-more">
			<?php if ($isItEn!==false)
			{ ?>
				<h3>Other articles</h3>
			<?php }else{?>
				<h3>Другие статьи по теме:</h3>
			<?php }	?>
			
			<div class="row-posts">
				<?php
					if( is_singular() ){
						global $post;
						$taxname = 'category';
						$post_terms = wp_get_object_terms( $post->ID, $taxname, array('fields'=>'ids') );

						$myposts = get_posts( array(
							'posts_per_page' => 3,
							$taxname   => $post_terms,
							'exclude'  => $post->ID, // исключим текущ. пост
						) );
				?>
				<?php foreach( $myposts as $post ) { ?>
					<?php setup_postdata($post); ?>
					<div class="div-25">
						<div class="post">
							<div class="pic">
								<?php the_post_thumbnail() ?>
							</div>
							<div class="txt">
								<a href="<?php the_permalink() ?>" class="title"><?php the_title(); ?></a>
								<div class="info">
									<div class="date"><?php the_date(); ?></div>
									<div class="views"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></div>
								</div>
								<div class="anons">
									<?php the_truncated_post( 100 ); ?>
								</div>
								<div class="read-more-wrap">
									<a href="<?php the_permalink() ?>" class="read-more mdl-button mdl-js-button mdl-button--primary">Читать далее</a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php wp_reset_postdata(); } ?>
			</div>
			
		</div>
	</div>
	<div class="container margin-top">
		<?php get_template_part( 'sidebar_mobile', 'none' ); ?>
	</div>
<?php get_template_part( 'pop-down', 'none' ); ?>
<?php get_footer(); ?>