<article class="post">
	<?php the_post_thumbnail() ?>
	<h1 class="title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
	<?php the_excerpt() ?>
	<a href="<?php the_permalink() ?>">Read more...</a>
</article>