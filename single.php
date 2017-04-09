<?php get_header() ?>
	<section id="single">
		<div class="wrapper solo">
			<h1><a href="<?php if ( get_option( 'show_on_front' ) == 'page' ) echo get_permalink( get_option( 'page_for_posts' ) );
else echo bloginfo( 'url' ) ?>">Blog</a></h1>
			
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'post' ) ?>
				<?php wp_link_pages(); ?>
				<?php comments_template() ?>
			<?php endwhile ?>
		</div>
	</section>
<?php get_footer(); ?>