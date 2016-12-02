<!DOCTYPE html>
<html <?php language_attributes(); ?> class='no-js'>
	<head>
		<meta charset='utf-8'>
		<meta name='viewport' content='width=device-width,initial-scale=1'>

		<title><?php wp_title(''); ?></title>

		<link rel='alternate' type='application/rss+xml' title='<?php echo get_bloginfo('sitename') ?> Feed' href='<?php echo get_bloginfo('rss2_url') ?>'>

		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<header role='banner'>

			<nav role='navigation'>
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav>

			<a href='<?php echo home_url('/'); ?>' title='<?php bloginfo( 'name' ); ?>' rel='home'><?php bloginfo( 'name' ); ?></a>

		</header>
