<article class="post">
	<?php the_post_thumbnail() ?>
	<?php if ( is_single() ): ?>
		<h1 class="title"><?php the_title() ?></h1>
	<?php else: ?>
		<h1 class="title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
	<?php endif ?>
	<time datetime="<?php the_time( 'c' ) ?>"><?php the_date() ?></time>
	<?php the_content( 'Read more...' ) ?>
</article>