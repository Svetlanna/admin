<aside class="mobile-right-content">
	<?php 
	$url = $_SERVER["REQUEST_URI"];

	$isItEn = strpos($url, 'en/');

	$last = new WP_Query( array( 'post_type' => array('materials_web')) );
	$i = 0;

	if ($isItEn!==false)
	{ ?>
		
	<?php }else{?>
<?php //echo do_shortcode('[Webinar_registration]'); ?>

		<div class="material-form same-div">
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
	<?php }	?>
	
	
	<div class="clearfix"></div>
</aside>