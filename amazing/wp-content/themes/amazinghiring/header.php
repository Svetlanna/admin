<!DOCTYPE html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width,initial-scale=1" name=viewport>
	<?php if ( !is_front_page() && is_home() ) { ?>
	<meta name="description" content="<?php bloginfo('description') ?>">
	<meta property="og:title"              content="<?php bloginfo('name'); ?>" />
	<meta property="og:description"        content="<?php bloginfo('description') ?>" />
	<meta property="og:image" content="<?php bloginfo('template_directory');?>/img/logo200x200.png">
	<title><?php bloginfo('name'); ?></title>
	<?php }else{ ?>
	<meta name="description" content="<?php the_secondary_title(); ?>">
	<meta property="og:title"              content="<?php the_title(); ?>" />
	<meta property="og:description"        content="<?php the_secondary_title(); ?>" />
	<meta property="og:image" content="<?php the_post_thumbnail_url( $size ); ?>"/>
	<meta property="og:image:secure_url" content="<?php the_post_thumbnail_url( $size ); ?>"/>
	<title><?php the_title(); ?></title>
	<?php } ?>
	<link rel="shortcut icon" href="<?php bloginfo('template_directory');?>/img/favicon.ico">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/normalize.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/material.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/animate.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory');?>/css/style.css">
	<?php wp_head(); ?>
</head>
<body>
<script>
  window.intercomSettings = {
    app_id: "p39nj99i"
  };
</script>
<!--
<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/p39nj99i';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
-->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-90954916-2', 'auto');
  ga('send', 'pageview');

</script>
	<header>
		<div class="container">
			<nav class="menu new-menu">
			    <div class="mobile-menu-btn">
                    <i class="material-icons">menu</i>
                </div>
				<div class="mobile-menu-wrapper">
					<a href="http://amazinghiring.ru" class="logo"><img src="/img/logo.svg" alt=""></a>

					<?php wp_nav_menu('menu=Up'); ?>

					<?php wp_nav_menu('menu=Lang&menu_class=buttons'); ?>
				</div>
			</nav>
		</div>
	</header>