<?php get_header() ?>
	<section id="workshops">
		<div class="wrapper solo">
			<h1>Workshops</h1>
			<?php while ( have_posts() ) : the_post(); ?>
				<article style="border-color: <?php the_field( 'arrow_colour' ) ?>" class="workshop">
					<a href="<?php the_permalink() ?>">
						<img src="<?php echo wp_get_attachment_thumb_url( get_field( 'logo' ) ) ?>" class="logo" />
						<div>
							<h1><?php the_title() ?></h1>
							<p><?php the_field( 'description' ) ?></p>
						</div>
					</a>
				</article>
			<?php endwhile ?>
			<nav>
				<?php next_posts_link( 'Next' ) ?>
				<?php previous_posts_link( 'Previous' ) ?>
			</nav>
		</div>
	</section>
<?php get_footer(); ?>
