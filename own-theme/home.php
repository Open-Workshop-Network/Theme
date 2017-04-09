<?php get_header() ?>
	<section id="blog">
		<div class="wrapper">
			<h1>Blog</h1>
			<?php while ( have_posts() ) : the_post(); ?>
				<article>
					<?php the_post_thumbnail() ?>
					<a href="<?php the_permalink() ?>"><h1><?php the_title() ?></h1></a>
					<?php the_content( 'Read more...' ) ?>
				</article>
			<?php endwhile ?>
			<nav>
				<?php next_posts_link( 'Next' ) ?>
				<?php previous_posts_link( 'Previous' ) ?>
			</nav>
		</div>
	</section>
<?php get_footer(); ?>
