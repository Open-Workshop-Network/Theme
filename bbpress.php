<?php get_header() ?>
	<section id="forum">
		<div class="wrapper solo">
			<?php while ( have_posts() ) : the_post(); ?>
				<h1><?php the_title() ?></h1>
				<?php the_content() ?>
			<?php endwhile ?>
		</div>
	</section>
<?php get_footer(); ?>
