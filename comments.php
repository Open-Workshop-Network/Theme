<section id="comments">
	<?php if ( have_comments() ): ?>
		<h1>Comments</h1>
		<ul>
			<?php wp_list_comments() ?>
		</ul>
		<?php paginate_comments_links( array( 'prev_text' => 'Previous', 'next_text' => 'Next' ) ) ?> 
		<?php if ( ! comments_open() && get_comments_number() ): ?>
			<p class="nocomments">Comments are closed</p>
		<?php endif; ?>
	<?php endif ?>
	<?php comment_form() ?>
</section>