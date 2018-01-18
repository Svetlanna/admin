<?php //get_header(); ?>
<?php get_template_part('header'); ?>
	<?php
		$active = get_post_meta( get_the_ID(), '_active_zap', true);
		$room_id = get_post_meta( get_the_ID(), '_webinar', true); 
		$field_single = get_post_meta( get_the_ID(), 'image', true );
		$field_single_url = wp_get_attachment_url( $field_single );		
		$wb_file_id = get_post_meta(get_the_ID(), 'wb_file_id', true);
	?>
		<?php if($active) {
			
			}else{
				echo '<div class="page-webinars">';
				echo do_shortcode('[Webinar_post_top]');
				echo '</div>';
			}
		?>
<div class="inner-material-container inner-webinar margin-top-50">
	<h1><?php the_title(); ?></h1>
	<?php echo '<img src="'.$field_single_url.'">'; ?>
	<?php 
		the_post();
		the_content(); 
	?>
	<div class="form-wrap">
	<?php
		$active = get_post_meta( get_the_ID(), '_active_zap', true);
		$room_id = get_post_meta( get_the_ID(), '_webinar', true);
		if($active) {
			echo do_shortcode('[Webinar_registration room_id="'.$room_id.'" post="yes"]'); 
		} else {
			echo do_shortcode('[email-download download_id="'.$wb_file_id.'" contact_form_id="524"]');
		  } ?>
	</div>
</div>

<?php get_footer(); ?>