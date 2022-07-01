<?php
	global $post;
	$post_id = $post->ID;
	$author_name = get_post_meta( $post_id, '_book_author_name', true );
	$book_color = get_post_meta( $post_id, '_book_color', true );
	

	if( empty($book_color) ) {
		$book_color = '#593873';
	}
?>

<?php if( isset( $post_id  ) ): ?>
	<div class="corus-admin-shortcode">
		<span class="corus-font-weight-bold"><?php echo esc_html_e( 'Short Code:', 'Corus' ); ?></span> 
		[book id="<?php echo $post_id; ?>"]
	</div>
<?php endif; ?>


<div class="corus-admin-fields-wrap">
	<div class="corus-admin-fields">
		<div class="corus-font-weight-bold"><?php echo esc_html_e( 'Author Name:', 'Corus' ); ?></div>
		<input type="text" name="_book_author_name" placeholder="<?php echo esc_html_e( 'Author Name', 'Corus' ); ?>" value="<?php echo $author_name; ?>">
	</div>

	<div class="corus-admin-fields">
		<div class="corus-font-weight-bold"><?php echo esc_html_e( 'Book Color:', 'Corus' ); ?></div>
		<input type="text" name="_book_color" class="js-color-field" value="<?php echo $book_color; ?>">
	</div>
</div>

<input type="hidden" name="_corus-admin-nonce" value="<?php echo wp_create_nonce("_corus-admin-nonce") ?>">
