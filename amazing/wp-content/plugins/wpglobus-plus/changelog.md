# Changelog #

## 1.1.20 ##

### Module Publish ###

* FIXED:
	* Incorrect saving post meta and options in bulk mode.

## 1.1.19 ##

* Compatibility: `WordPress 4.6`.
* New `License Manager / Updater` interface (requires `WPGlobus 1.6.0+`).

## 1.1.18 ##

* FIXED:
	* Yoast: Fix for Yoast SEO 3.3.4 (correct value of the hidden field `yoast_wpseo_focuskw` after validate.

## 1.1.17 ##

* FIXED:
	* Yoast: switching to "Readability" tab (WPSEO issue 5013, Ticket 8628).
	* Core: better CSS rules (`Customizr` theme breaks our admin page).
	* Core: a typo caused fatal error on the `Module Publish` tab in some cases (Ticket 8560). 
* ADDED:
	* Module `Menu Settings` for fine-tuning multilingual menus.

## 1.1.16 ##

* ADDED:
	* Compatibility with `Yoast SEO` version 3.3.

## 1.1.15 ##

* FIXED:
	* Home page notice and redirect loop with the `Storefront Checkout Customiser` plugin active (Ticket 8135).

## 1.1.14 ##

* FIXED:
	* Correct slug handling for the hierarchical post types (Ticket 6922).

## 1.1.13 ##

* FIXED:
	* Localize parent post's name as part of URL in menu items (Ticket 6662).

## 1.1.12 ##

* FIXED:
	* ACF reset after post update (Ticket 6506).

## 1.1.11 ##

* FIXED:
	* Hide draft posts in default language (Ticket 6103).
	* ACF tweaks: not removing empty paragraphs (Github 26), etc.
	* Various cosmetic improvements and code cleanup.

## 1.1.10 ##

* ADDED:
	* Support for Yoast SEO Version 3.1

## 1.1.9 ##

* ADDED:
	* Setting options in Customizer (BETA).

### Module Slug ###

* FIXED:
	* Correct handling of hierarchical pages level 2 and more.

## 1.1.8 ##

### Module Publish ###

* ADDED:
	* Tool to set draft status per language in bulk.
	
## 1.1.7 ##

### Module Slug ###

* FIXED:
	* `$wp_query` compatible for extra language slug
		
## 1.1.6 ##

### Module Slug ###

* FIXED:
	* `is_archive` value for pages with extra languages
		
## 1.1.5 ##

* ADDED:
	* Support for Yoast SEO Version 3.0

## 1.1.4 ##

### Module ACF ###

* FIXED:
	* Set up tags `p` in Wysiwyg editor for extra language

## 1.1.3 ##

### Module Publish ###

* ADDED:
	* WPGlobus Plus + WooCommerce WPGlobus: enabled;
	
### Module Slug ###

* ADDED:
	* filter the permalink for a post with a custom post type.
* FIXED:
	* redirect when trying opening post in extra language with post_name from default language
