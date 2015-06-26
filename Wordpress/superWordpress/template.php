<?php 
/*
Template Name: Template
*/

get_header(); ?>

	<?php if ( have_posts() ) : the_post(); ?>

		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
	
	<?php else : ?>
				
		<h1>404</h1>

	<?php endif; ?>

<?php get_footer(); ?>