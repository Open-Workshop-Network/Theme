<!doctype html>
<html <?php language_attributes()?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ) ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<!-- <meta name="description" content=""> -->
		<!-- <meta name="author" content="Name"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="shortcut icon" href="<?php echo THEME_DIR ?>/path/favicon.ico" />
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ) ?> RSS2 Feed" href="<?php bloginfo( 'rss2_url' ) ?>" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />


	    <title><?php wp_title( '&mdash;', 'true', 'right' ) ?></title>
		
		<?php wp_head() ?>
	</head>
	<body <?php body_class() ?>>
		<!--[if lt IE 8]>
			<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->

		<header>
			<div class="logo">
				<a href="<?php bloginfo( 'url' ) ?>" class="home">Home</a>
				<span class="city"><?php bloginfo( 'description' ) ?><a href="http://openworkshopnetwork.com/" class="switch" title="Switch City">Switch City</a></span>
			</div>
		</header>