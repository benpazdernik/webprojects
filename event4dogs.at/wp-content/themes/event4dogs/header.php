<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // Google Chrome Frame for IE ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php wp_title(''); ?></title>

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		
		<?php // Google Fonts ?>
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,400italic|Bad+Script|Inika:400,700' rel='stylesheet' type='text/css'>

		<?php // IconFonts Font-Awesome ?>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

		

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>

		
		<?php if (in_category('weihnachtsmarkt') ):
		?>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/css/red.css" type="text/css" media="screen" />
		<?php elseif (in_category('trainerfortbildung') ):
		?>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/css/violett.css" type="text/css" media="screen" />
		<?php elseif (in_category('hundekongress') ):
		?>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/css/green.css" type="text/css" media="screen" />
		<?php elseif (in_category('seminare') ):
		?>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/css/blue.css" type="text/css" media="screen" />
		<?php else :
		?>
			<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/library/css/brown.css" type="text/css" media="screen" />
		<?php endif;  ?>


	</head>

	<body <?php body_class(); ?>>

		<div id="container">

			<header class="header" role="banner">

				<div id="inner-header" class="wrap clearfix">

					<div id="inner-header-top" class = "clearfix">

						<?php // to use a image just replace the bloginfo('name') with your img src and remove the surrounding <p> ?>
						<?php if (in_category('weihnachtsmarkt') ):
						?>
							<div id="logo"><a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/logo_red.png" alt="<?php bloginfo('name'); ?>" /></a></div>

						<?php elseif (in_category('trainerfortbildung') ):
						?>
							<div id="logo"><a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/logo_violett.png" alt="<?php bloginfo('name'); ?>" /></a></div>

						<?php elseif (in_category('hundekongress') ):
						?>
							<div id="logo"><a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/logo_green.png" alt="<?php bloginfo('name'); ?>" /></a></div>

						<?php elseif (in_category('seminare') ):
						?>
							<div id="logo"><a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/logo_blue.png" alt="<?php bloginfo('name'); ?>" /></a></div>

						<?php else :
						?>
							<div id="logo"><a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/logo.png" alt="<?php bloginfo('name'); ?>" /></a></div>
						<?php endif;  ?>

						<?php // if you'd like to use the site description you can un-comment it below ?>
						<?php // bloginfo('description'); ?>

						<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Header Widget')) : endif; ?>

					</div>

					<div id="inner-header-middle" class = "clearfix">
						
						<nav class="menu" id="main-menu" role="navigation">
							<?php bones_main_nav(); ?>
						</nav>

					</div>

					<div id="inner-header-bottom" class = "clearfix">

						<?php putRevSlider("home_slider_1","homepage") ?>

					</div>

				</div>

			</header>
