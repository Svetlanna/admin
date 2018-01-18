<div class="overflow">
	<?php 
	$url = $_SERVER["REQUEST_URI"];

	$isItEn = strpos($url, '/');

	if ($isItEn!==false)
	{ ?>
		<?php /*
		<style>
			.popup-one .mdl-button--accent {
			    width: 145px !important;
			}
		</style>
		*/?>

		<div id="overlay-p">
		    <div class="popup">
		        <?php echo do_shortcode('[contact-form-7 id="511" title="Subscribe for recruiting tips v2.0"]'); ?>

		        <button class="close" title="Закрыть" onclick="document.getElementById('overlay-p').style.display='none';">
		        	<i class="material-icons">close</i>
		        </button>
		    </div>
		</div>
	<?php }else{?>
		<?php /*
		<div class="popup-two">
			<div class="container relative">
				
				<?php echo do_shortcode('[contact-form-7 id="66" title="Подписка на новости"]'); ?>
				
				<span class="close"></span>
			</div>
		</div>
		*/?>
		<div id="overlay-p">
		    <div class="popup">
		        <?php echo do_shortcode('[contact-form-7 id="510" title="Подписка на новости v2.0"]'); ?>

		        <button class="close" title="Закрыть" onclick="document.getElementById('overlay-p').style.display='none';">
		        	<i class="material-icons">close</i>
		        </button>
		    </div>
		</div>
	<?php }	?>
</div>
