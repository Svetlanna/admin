

<div class="div-50">
	<div class="post">
		<div class="pic">
			<?php the_post_thumbnail() ?>
		</div>
		<div class="txt">
			<a href="<?php  the_permalink(); ?>" class="title"><?php the_title(); ?></a>
			<div class="info">
				<div class="date"><?php the_date(); ?></div>
				<div class="views"><i class="fa fa-eye"></i> <?php if(function_exists('the_views')) { the_views(); } ?></div>
			</div>
			<div class="anons">				
				<?php the_truncated_post( 180 ); ?>
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
</div>

 
