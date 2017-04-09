<?php get_header() ?>
	<section id="blog">
		<div class="wrapper solo">
			<h1><a href="<?php if ( get_option( 'show_on_front' ) == 'page' ) echo get_permalink( get_option( 'page_for_posts' ) );
else echo bloginfo( 'url' ) ?>">Blog</a></h1>
			<?php if ( have_posts() ): ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'post' ) ?>
					<?php wp_link_pages(); ?>
					<?php comments_template() ?>
				<?php endwhile ?>
				<nav class="pagination">
					<span class="previous"><?php previous_posts_link( 'Previous' ) ?></span>
					<span class="next"><?php next_posts_link( 'Next' ) ?></span>
				</nav>
			<?php else: ?>
				<?php get_template_part( 'empty' ) ?>
			<?php endif ?>
		</div>
	</section>
<?php get_footer(); ?>