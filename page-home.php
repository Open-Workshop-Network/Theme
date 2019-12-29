<?php
/*
	Template Name: OWN Homepage
*/

	get_header()
?>
	<noscript>
		<p>JavaScript must be enabled to load the map on this page.</p>
	</noscript>

	<section id="map"></section>

	<section id="list">
		<div class="wrapper">
			<div>
				<h1>Workshop Directory</h1>
				<span>0</span>
				<ul></ul>
			</div>
		</div>
	</section>

	<section id="homepage">
		<section class="filter">
			<div class="wrapper">
				<h1>Filter</h1>
				<div class="taxonomy other">
					<h2>Search</h2>
					<p><input type="search" placeholder="Search" /></p>

					<h2>Opened</h2>
					<div class="opened"><div></div><span></span></div>
				</div>

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

				<?php $materials = get_terms( 'own_materials', 'parent=0' ) ?>
				<?php if ( count( $materials ) > 0 ): ?>
					<div class="taxonomy materials">
						<h2>Materials</h2>
						<ul>
							<?php foreach( $materials as $material ): ?>
								<li>
									<input type="checkbox" id="materials_<?php echo $material->slug ?>" /> <label for="materials_<?php echo $material->slug ?>"><?php echo $material->name ?></label>
									<?php $submaterials = get_terms( 'own_materials', 'parent=' . $material->term_id ) ?>
									<?php if ( count( $submaterials ) > 0 ): ?>
										<ul>
											<?php foreach( $submaterials as $submaterial ): ?>
												<li><input type="checkbox" id="materials_<?php echo $material->slug ?>_<?php echo $submaterial->slug ?>" /> <label for="materials_<?php echo $material->slug ?>_<?php echo $submaterial->slug ?>"><?php echo $submaterial->name ?></label></li>
											<?php endforeach ?>
										</ul>
									<?php endif ?>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				<?php endif ?>

				<?php $tools = get_terms( 'own_tools', 'parent=0' ) ?>
				<?php if ( count( $tools ) > 0 ): ?>
					<div class="taxonomy tools">
						<h2>Tools</h2>
						<ul>
							<?php foreach( $tools as $tool ): ?>
								<li>
									<input type="checkbox" id="tools_<?php echo $tool->slug ?>" /> <label for="tools_<?php echo $tool->slug ?>"><?php echo $tool->name ?></label>
									<?php $subtools = get_terms( 'own_tools', 'parent=' . $tool->term_id ) ?>
									<?php if ( count( $subtools ) > 0 ): ?>
										<ul>
											<?php foreach( $subtools as $subtool ): ?>
												<li><input type="checkbox" id="tools_<?php echo $tool->slug ?>_<?php echo $subtool->slug ?>" /> <label for="tools_<?php echo $tool->slug ?>_<?php echo $subtool->slug ?>"><?php echo $subtool->name ?></label></li>
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
				<section class="about">
					<h1><?php the_title() ?></h1>
					<p><?php the_content() ?></p>
				</section>
				<section class="links">
					<h1>Links</h1>
					<?php $links = get_field( 'links' ) ?>
					<?php foreach ( $links as $link ): ?>
						<p><h2><a href="<?php echo $link['uri'] ?>"><?php echo $link['title'] ?></a></h2></p>
					<?php endforeach ?>
				</section>
			<?php endwhile ?>

			<?php
				$query = new WP_Query( "post_type=post&posts_per_page=3" );
			?>

			<?php if ( $query->have_posts() ): ?>
				<section class="blog">
					<h1><a href="<?php if ( get_option( 'show_on_front' ) == 'page' ) echo get_permalink( get_option( 'page_for_posts' ) );
else echo bloginfo( 'url' ) ?>">Blog</a></h1>
					<?php while ( $query->have_posts() ): ?>
						<?php $query->the_post() ?>
						<?php get_template_part( 'content', 'post-home' ) ?>
					<?php endwhile ?>
				</section>
			<?php endif ?>
			<?php wp_reset_postdata() ?>
		</div>
	</section>
<?php get_footer(); ?>
