<?php
$field_single = get_post_meta( get_the_ID(), 'image', true );
$field_single_url = wp_get_attachment_url( $field_single );
$press_link = get_post_meta(get_the_ID(), 'press_link', true);
?>
<section>
	<a href="<?= $press_link; ?>" target="_blank">
		<?php echo '<img src="'.$field_single_url.'">'; ?>
	</a>
	<hr />
	<span class="date">19.12.2016</span>
	<a href="<?= $press_link; ?>" target="_blank"><?php the_title(); ?></a>
</section>