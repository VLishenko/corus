<?php 
    if( !isset($attributes) || empty($attributes) || !isset($attributes['id']) || empty($attributes['id'])){
        return;
    }

    $post_id = $attributes['id'];
    $post = get_post($post_id);

    $title = get_the_title($post_id);
    $author_name = get_post_meta( $post_id, '_book_author_name')[0];
    $book_color = get_post_meta( $post_id, '_book_color')[0];
?>

<div class="corus-book-wrap">
    <div class="corus-book" style="background-color: <?php echo $book_color; ?>;">
        <div class="corus-book-title"><?php echo $title; ?></div>
        <div class="corus-book-author"><?php echo $author_name; ?></div>
    </div>
</div>


