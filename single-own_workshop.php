<?php get_header() ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<section id="workshop">
			<div class="wrapper solo">
				<h1><?php the_title() ?></h1>

				<p class="desc"><?php the_field( 'description' ) ?></p>

				<?php $url = parse_url( get_field( 'url' ) ) ?>
				<ul class="links">
					<?php if ( get_field( 'phone_number' ) ): ?><li><a href="tel:<?php echo str_replace( ' ', '', get_field( 'phone_number' ) ) ?>"><?php echo preg_replace( "/(0[1|3|5|7|8](?:\d){3})(\d{5,})|(020)(\d{4})(\d{4,})/", "$1$3 $2$4 $5", str_replace( ' ', '', get_field( 'phone_number' ) ) ) ?></a></li><?php endif ?>
					<?php if ( get_field( 'url' ) ): ?><li><a href="<?php the_field( 'url' ) ?>" target="_blank"><?php echo str_replace( "www.", "", $url['host'] ) . ( $url['path'] != '/' ? $url['path'] : null ) ?></a></li><?php endif ?>
					<?php if ( get_field( 'email_address' ) ): ?><li><a href="mailto:<?php the_field( 'email_address' ) ?>"><?php the_field( 'email_address' ) ?></a></li><?php endif ?>
				</ul>

				<p class="address"><a href="http://maps.google.co.uk/maps?q=<?php echo urlencode( get_the_title() . ", " . str_replace( "\n", ", ", get_field( 'address' ) ) ) ?>" target="_blank"><?php echo nl2br( get_field( 'address' ) ) ?></a></p>

				<?php if ( count( get_field( 'images' ) ) > 1 || get_field( 'images' )[0]['image'] ): ?>
					<ul class="photos">
						<?php foreach( get_field( 'images' ) as $i => $image ): ?>
							<?php
								$current = $i + 1;
								$next = $current + 1;

								if ( $next > count( get_field( 'images' ) ) )
									$next = 1;

								$prev = $current - 1;

								if ( $prev < 1 )
									$prev = count( get_field( 'images' ) );
							?>
							<input type="radio" name="photos" id="photo-<?php echo $current ?>" <?php echo ( $current == 1 ? 'checked' : null ) ?> />
							<li class="photo">
								<div style="background-image: url( <?php echo wp_get_attachment_image_src( $image['image'], 'large' )[0] ?> );">
								<?php if ( count( get_field( 'images' ) ) > 1 ): ?><label class="prev" for="photo-<?php echo $prev ?>"></label><?php endif ?>
								<?php if ( count( get_field( 'images' ) ) > 1 ): ?><label class="next" for="photo-<?php echo $next ?>"></label><?php endif ?>
							</li>
						<?php endforeach ?>
						<?php if ( count( get_field( 'images' ) ) > 1 ): ?>
							<li class="nav">
								<?php foreach( get_field( 'images' ) as $i => $image ): ?>
									<label for="photo-<?php echo $i + 1 ?>" for="photo-<?php echo $i + 1 ?>" id="photo-dot-<?php echo $i + 1 ?>"></label>
								<?php endforeach ?>
							</li>
						<?php endif ?>
					</ul>
				<?php endif ?>

				<?php
					$service_count = count( wp_get_post_terms( get_the_ID(), 'own_services' ) );
					$discipline_count = count( wp_get_post_terms( get_the_ID(), 'own_disciplines' ) );
					$material_count = count( wp_get_post_terms( get_the_ID(), 'own_materials' ) );
					$tool_count = count( wp_get_post_terms( get_the_ID(), 'own_tools' ) );
				?>
				<?php if ( $service_count > 0 || $discipline_count > 0 || $material_count > 0 || $tool_count > 0 ): ?>
					<section class="tax">
						<?php if ( $service_count > 0 ): ?>
							<h2>Services</h2>
							<p><?php OWN_Theme_Tax_List( 'own_services' ) ?></p>
						<?php endif ?>

						<?php if ( $discipline_count > 0 ): ?>
							<h2>Disciplines</h2>
							<p><?php OWN_Theme_Tax_List( 'own_disciplines' ) ?></p>
						<?php endif ?>

						<?php if ( $material_count > 0 ): ?>
							<h2>Materials</h2>
							<p><?php OWN_Theme_Tax_List( 'own_materials' ) ?></p>
						<?php endif ?>

						<?php if ( $tool_count > 0 ): ?>
							<h2>Tools</h2>
							<p><?php OWN_Theme_Tax_List( 'own_tools' ) ?></p>
						<?php endif ?>
					</section>
				<?php endif ?>

				<?php if ( get_field( 'twitter' ) || get_field( 'tiktok' ) || get_field( 'bluesky' ) || get_field( 'facebook' ) || get_field( 'google+' ) || get_field( 'instagram' ) || get_field( 'google_group' ) || get_field( 'youtube' ) ): ?>
					<section class="links">
						<h2>Links</h2>
						<ul>
							<?php if ( get_field( 'tiktok' ) ): ?><li class="icon tiktok"><a href="http://tiktok.com/<?php the_field( 'tiktok' ) ?>" target="_blank">@<?php the_field( 'tiktok' ) ?></a></li><?php endif ?>
							<?php if ( get_field( 'instagram' ) ): ?><li class="icon instagram"><a href="http://instagram.com/<?php the_field( 'instagram' ) ?>" target="_blank">instagram.com/<?php the_field( 'instagram' ) ?></a></li><?php endif ?>
							<?php if ( get_field( 'youtube' ) ): ?><li class="icon youtube"><a href="http://youtube.com/<?php the_field( 'youtube' ) ?>" target="_blank">youtube.com/<?php the_field( 'youtube' ) ?></a></li><?php endif ?>
							<?php if ( get_field( 'facebook' ) ): ?><li class="icon facebook"><a href="http://fb.com/<?php the_field( 'facebook' ) ?>" target="_blank">fb.com/<?php the_field( 'facebook' ) ?></a></li><?php endif ?>
							<?php if ( get_field( 'bluesky' ) ): ?><li class="icon bluesky"><a href="http://bsky.app/profile/<?php the_field( 'bluesky' ) ?>" target="_blank">@<?php the_field( 'bluesky' ) ?></a></li><?php endif ?>
							<?php if ( get_field( 'twitter' ) ): ?><li class="icon twitter"><a href="http://twitter.com/<?php the_field( 'twitter' ) ?>" target="_blank">@<?php the_field( 'twitter' ) ?></a></li><?php endif ?>
							<?php if ( get_field( 'google_group' ) ): ?><li class="icon ggroup"><a href="http://groups.google.com/d/forum/<?php the_field( 'google_group' ) ?>" target="_blank">groups.google.com/d/forum/<?php the_field( 'google_group' ) ?></a></li><?php endif ?>
						</ul>
					</section>
				<?php endif ?>

				<section class="opened">
					<h2>Opened</h2>
					<p><?php the_field( 'opened' ) ?></p>
				</section>

				<?php if ( get_field( 'long_description' ) ): ?>
					<section class="long">
						<h2>About</h2>
						<p><?php the_field( 'long_description' ) ?></p>
					</section>
				<?php endif ?>
			</div>
		</section>
	<?php endwhile ?>

<?php get_footer(); ?>
