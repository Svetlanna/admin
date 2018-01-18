<?php
	$active = get_post_meta( get_the_ID(), '_active_zap', true);
	$room_id = get_post_meta( get_the_ID(), '_webinar', true);
	$field_single = get_post_meta( get_the_ID(), 'image', true );
	$field_single_url = wp_get_attachment_url( $field_single );
?>
<?php if($active) {?>

<?php }else{ ?>
<article>
	<div class="img">
		<?php echo '<img src="'.$field_single_url.'">'; ?>
	</div>
	<div class="tablet-view-div">
		<div class="text">
			<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
		    <div class="anons">
		        <?php the_truncated_post( 500 ); ?>
		    </div>
		</div>
	    <div class="link">
	    	<a href="<?php the_permalink(); ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Получить запись</a>
	    </div>
    </div>
</article>
<?php } ?>