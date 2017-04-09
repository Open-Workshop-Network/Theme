<?php
	/*
		Template Name: OWN Homepage
	*/

	get_header()
?>
	<section id="map"></section>

		<section id="filter">
			<div class="wrapper">
				<h1>Filter</h1>

				<?php $services = get_terms( 'own_services' ) ?>
				<?php if ( count( $services ) > 0 ): ?>
				<div class="taxonomy services">
					<h2>Services</h2>
					<ul>
						<?php foreach( $services as $service ): ?>
							<li><input type="checkbox" id="services_<?php echo $service->slug ?>" /> <label for="services_<?php echo $service->slug ?>"><?php echo $service->name ?></label></li>
						<?php endforeach ?>
					</ul>
				</div>
				<?php endif ?>

				<?php $disciplines = get_terms( 'own_disciplines' ) ?>
				<?php if ( count( $disciplines ) > 0 ): ?>
					<div class="taxonomy disciplines">
						<h2>Disciplines</h2>
						<ul>
							<?php foreach( $disciplines as $discipline ): ?>
								<li><input type="checkbox" id="disciplines_<?php echo $discipline->slug ?>" /> <label for="disciplines_<?php echo $discipline->slug ?>"><?php echo $discipline->name ?></label></li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>

				<?php $materials = get_terms( 'own_materials' ) ?>
				<?php if ( count( $materials ) > 0 ): ?>
					<div class="taxonomy materials">
						<h2>Materials</h2>
						<ul>
							<?php foreach( $materials as $material ): ?>
								<li><input type="checkbox" id="materials_<?php echo $material->slug ?>" /> <label for="materials_<?php echo $material->slug ?>"><?php echo $material->name ?></label></li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>

				<?php $tools = get_terms( 'own_tools' ) ?>
				<?php if ( count( $tools ) > 0 ): ?>
					<div class="taxonomy tools">
						<h2>Tools</h2>
						<ul>
							<?php foreach( $tools as $tool ): ?>
								<li><input type="checkbox" id="tools_<?php echo $tool->slug ?>" /> <label for="tools_<?php echo $tool->slug ?>"><?php echo $tool->name ?></label></li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>

				<?php $processes = get_terms( 'own_processes', 'parent=0' ) ?>
				<?php if ( count( $processes ) > 0 ): ?>
					<div class="taxonomy processes">
						<h2>Processes</h2>
						<ul>
							<?php foreach( $processes as $process ): ?>
								<li>
									<input type="checkbox" id="processes_<?php echo $process->slug ?>" /> <label for="processes_<?php echo $process->slug ?>"><?php echo $process->name ?></label>
									<?php $subprocesses = get_terms( 'own_processes', 'parent=' . $process->term_id ) ?>
									<?php if ( count( $subprocesses ) > 0 ): ?>
										<ul>
											<?php foreach( $subprocesses as $subprocess ): ?>
												<li><input type="checkbox" id="processes_<?php echo $process->slug ?>_<?php echo $subprocess->slug ?>" /> <label for="processes_<?php echo $process->slug ?>_<?php echo $subprocess->slug ?>"><?php echo $subprocess->name ?></label></li>
											<?php endforeach ?>
										</ul>
									<?php endif ?>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>
			</div>
		</section>
		
		<div class="wrapper">
			<?php while ( have_posts() ) : the_post(); ?>
				<section id="about">
					<h1><?php the_title() ?></h1>

					<p><?php echo get_the_content() ?></p>

					<?php $links = get_field( 'links' ) ?>
					<?php foreach ( $links as $link ): ?>
						<p><h2><a href="<?php echo $link['uri'] ?>"><?php echo $link['title'] ?></a></h2></p>	
					<?php endforeach ?>
				</section>
			<?php endwhile ?>

			<?php
				$query = new WP_Query( "post_type=post&posts_per_page=1" );
			?>

			<?php if ( $query->have_posts() ): ?>
				<section id="blog">
					<h1><a href="<?php echo get_permalink( get_option('page_for_posts' ) ) ?> ">Blog</a></h1>
					<?php while ( $query->have_posts() ): ?>
						<?php $query->the_post() ?>
						<article>
							<?php the_post_thumbnail() ?>
							<h1>
								<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
							</h1>

							<?php the_content( 'Read more...' ) ?>
						</article>
					<?php endwhile ?>
					<nav>
						<?php the_posts_pagination(  ); ?>
					</nav>
				</section>
			<?php endif ?>
			<?php wp_reset_postdata() ?>
		</div>
<?php get_footer(); ?>
