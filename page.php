<?php get_header() ?>
	<section id="page">
		<div class="wrapper solo">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ) ?>
				<?php wp_link_pages(); ?>
				<?php comments_template() ?>
			<?php endwhile ?>
		</div>
	</section>
<?php get_footer(); ?>