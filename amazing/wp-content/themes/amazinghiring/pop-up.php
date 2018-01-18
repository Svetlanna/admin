<?php
	$url = $_SERVER["REQUEST_URI"];
	$isItEn = strpos($url, 'en/');
?>
<?php 				
	if ($isItEn!==false)
{ ?>
	<html class="engLang">
<?php }else{?>
	<html class="rusLang">
<?php } ?>
<div style="position: relative; display: block;">
<div class="overflow">
	<div class="popup-one">
		<div class="container relative">
			<?php 				
			if ($isItEn!==false)
			{ ?>
				<ul>
					<li><span>Request free demo</span></li>
					<li>
						<a target="_blank" href="http://amazinghiring.com/request-demo.html" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
						Request
						</a>
					</li>
				</ul>
			<?php }else{?>
			<ul>
				<li><span>Получить бесплатный демо-доступ</span></li>
				<li>
					<a target="_blank" href="http://amazinghiring.ru/request-demo.html" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
					Получить
					</a>
				</li>
			</ul>
			<?php }	?>
			<span class="close"></span>
		</div>
	</div>
</div>
</div>