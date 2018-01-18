<!DOCTYPE html>
<html>
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
<style>
	#menu-item-63 i,
	.botlang .menu-item-63 i {
		float: left;
		margin-top: -2px;
		margin-right: 5px;
		color: #f79933;
		display: block;
	}
	#menu-item-63:before,
	.botlang .menu-item-63:before {
		display: none;	    
	}
</style>
<header>		
		<div class="container">
		    <nav class="menu new-menu">
		    	<div class="mobile-menu-btn">
		        	<i class="material-icons">menu</i>
		        </div>
		    	<div class="mobile-menu-wrapper">
			        <a href="http://amazinghiring.ru/faq.html" class="logo"><img src="http://amazinghiring.com/blog/wp-content/themes/amazinghiring/img/logo.png" alt=""></a>
			        <div class="menu-up-container">
			            <ul id="menu-up" class="menu">
			            	<li class="current-menu-item inserted">
			            		<a href="#">О нас</a>
			            		<ul>
			            			<li><a href="#">Пресса</a></li>
			            			<li><a href="#">Команда</a></li>
			            		</ul>
			            	</li>
			            	<li>
			            		<a href="#">Продукт</a>
			            	</li>
			            	<li class="inserted">
			            		<a href="#">База знаний</a>
			            		<ul>
			            			<li><a href="/blog/вебинары/">Вебинары</a></li>
			            			<li><a href="/blog/?page_id=379&preview=true">Материалы</a></li>
			            		</ul>
			            	</li>
			                <li id="menu-item-232" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-232"><a href="http://amazinghiring.ru/faq.html">Вопросы и ответы</a></li>
			                <li id="menu-item-37" class="menu-item menu-item-type-post_type menu-item-object-page current_page_parent menu-item-37"><a href="http://amazinghiring.com/blog/blog/">Блог</a></li>
			            </ul>
			        </div>
			        <div class="menu-lang-container">
			            <ul id="menu-lang" class="buttons">
			                <li id="menu-item-63" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-63"><a href="http://search.amazinghiring.com/"><i class="material-icons"></i> Войти</a></li>
			                <li id="menu-item-9999999999" class="menu-item menu-item-type-custom menu-item-object-custom menu_item_wpglobus_menu_switch wpglobus-selector-link wpglobus-current-language menu-item-9999999999"><a href="http://amazinghiring.com/blog/"><span class="wpglobus_language_full_name">Русский</span></a>
			                    <ul class="sub-menu">
			                        <li id="menu-item-wpglobus_menu_switch_en" class="menu-item menu-item-type-custom menu-item-object-custom sub_menu_item_wpglobus_menu_switch wpglobus-selector-link menu-item-wpglobus_menu_switch_en"><a href="http://amazinghiring.com/blog/en/о-нас/"><span class="wpglobus_language_full_name">English</span></a></li>
			                    </ul>
			                </li>
			            </ul>
			        </div>
		        </div>		        
		    </nav>
		</div>

	</header>