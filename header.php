<!doctype html>
<html <?php language_attributes()?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ) ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<!-- <meta name="description" content=""> -->
		<!-- <meta name="author" content="Name"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<link rel="shortcut icon" href="<?php echo get_template_directory() ?>/path/favicon.ico" />
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ) ?> RSS2 Feed" href="<?php bloginfo( 'rss2_url' ) ?>" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

	    <title><?php wp_title( '&mdash;', true, 'right' ) ?><?php echo bloginfo( 'name' ) ?> &mdash; <?php echo bloginfo( 'description' ) ?></title>
		
		<?php wp_head() ?>
	</head>
	<body <?php body_class() ?>>
		<header>
			<div class="logo">
				<a href="<?php bloginfo( 'url' ) ?>" class="home">Home</a>
			</div>
		</header>
