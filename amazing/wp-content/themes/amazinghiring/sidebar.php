<aside class="right-content">
	<?php 
	$url = $_SERVER["REQUEST_URI"];

	$isItEn = strpos($url, 'en/');

	$last = new WP_Query( array( 'post_type' => array('materials_web')) );
	$i = 0;

	if ($isItEn!==false)
	{

	}else{?>
		
		<div id="sidebar-webinarform-wrap">
			<?php echo do_shortcode('[Webinar_registration]'); ?>
		</div>
				
		<div class="material-form">
			<?php if ( $last->have_posts() ) :    	
			    while ( $last->have_posts() ) : $last->the_post(); ?>
			    <?php 
					$i++;
					$field = get_post_meta( get_the_ID(), 'image', true );
					$field_url = wp_get_attachment_url( $field );
				?>
			    <?php if ($i == 1) { ?>
				    <div class="background">
						<span class="form-name"><img src="<?php bloginfo('template_directory');?>/img/book.jpg" alt=""> МАТЕРИАЛ</span>
						<div class="wrap">
							<div class="material-name">
								<p><?php the_title(); ?></p>
							</div>
							<div class="material-img">
								<?php echo '<img src="'.$field_url.'">'; ?>
							</div>
						</div>
					</div>
					<?php $ml_file_id = get_post_meta(get_the_ID(), 'ml_file_id', true); ?>
					<?php echo do_shortcode('[email-download download_id="'.$ml_file_id.'" contact_form_id="533"]'); ?>
				<?php } ?>					
			    <?php endwhile; ?>
			<?php endif; wp_reset_postdata(); ?>
		</div>
		<div class="no-form hidden-material-form">
			<?php echo do_shortcode('[contact-form-7 id="317" title="Материалы Checkbox"]'); ?>
		</div>
	<?php }	?>

	

	<div class="topics">		
		<?php/*
		$posttags = get_tags();
		if ($posttags) {
			echo "<ul>";
			foreach($posttags as $tag) {
			    echo '<li><a href="'.get_tag_link($tag->term_id).'">'.$tag->name.'</a></li>';
			}
		}*/
		?>
		<ul>
		<?php wp_list_categories('depth=3&exclude=1&hide_empty=0&orderby=sort&show_count=0&use_desc_for_title=1&title_li='); ?>
		</ul>
	</div>
		
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