
<!-- home.php -->
<?php $curr_paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?>

<?php $iter = 1; ?>
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : ?>
        <?php the_post(); ?>
        <?php if( $curr_paged == 1 && $iter == 1 ) : ?>
            <h3>first post -- </h3><h2><?php the_title(); ?></h2>   
        <?php elseif( $curr_paged == 1 && ( $iter == 2 || $iter == 3 ) ): ?>
            <h3>second or third post -- </h3><h2><?php the_title(); ?></h2>
        <?php elseif( $curr_paged == 1 && ( $iter > 3 && $iter < get_option('posts_per_page') ) ): ?>
            <h3>pagination page 1 other posts -- </h3><h2><?php the_title(); ?></h2>
        <?php else: ?>
            <h3>other pagination pages -- </h3><h2><?php the_title(); ?></h2>
        <?php endif; ?>

        <?php $iter++; ?>
    <?php endwhile; ?>
<?php else: ?>
	No posts
<?php endif; ?>

<div class="pagination">
    <!-- First pagination page  -->
    <div>
        <?php if( $curr_paged != 1) : ?>
            <a href="<?php echo home_url('/blog/'); ?>">First</a>
        <?php endif; ?>
    </div>
    <!-- END First pagination page  -->
    <?php 
        the_posts_pagination( array(
            'screen_reader_text' => ' ',
            'prev_text'          => '<span>Prev</span>', 
            'next_text'          => '<span>Next</span>',
        ) );
    ?>
    <!-- Last pagination page -->
    <div>
        <?php $last_paged = $wp_query->max_num_pages; ?>
        <?php if( $curr_paged != $last_paged) : ?>
            <a href="<?php echo home_url('/blog/') . 'page/' . $last_paged . '/'; ?>">Last</a>
        <?php endif; ?>
    </div>
    <!-- END Last pagination page -->
</div>
<?php
wp_reset_postdata();


// functions.php

// Pagination fix to show -1 posts on first Blog! page

    function f7_offset_homepage( $query ) {
        if ($query->is_home() && $query->is_main_query() && !is_admin()) {
            $query->set( 'post_type', 'post' );
            $query->set( 'post_status', 'publish' );
        
            $ppp = get_option('posts_per_page');
            $offset = -1;

            if (!$query->is_paged()) {
                $query->set('posts_per_page', $offset + $ppp);
            } else {
                $offset = $offset + ( ($query->query_vars['paged']-1) * $ppp );
                $query->set('posts_per_page', $ppp);
                $query->set('offset', $offset);
            }
        }
    }
    add_action('pre_get_posts', 'f7_offset_homepage');
        
    function f7_homepage_offset_pagination( $found_posts, $query ) {

        $ppp = get_option('posts_per_page');
        $offset = -1;
        $abs_offset = abs($offset);
        if (!$query->is_paged()) {
            $found_posts = ceil( ( ($found_posts + $abs_offset) * ($ppp - $abs_offset) ) / $ppp); 
        }
        
        if( $query->is_home() && $query->is_main_query() ) {
            $found_posts = $found_posts - $offset;
        }
        return $found_posts;
    }
    add_filter( 'found_posts', 'f7_homepage_offset_pagination', 10, 2 );

// END Pagination fix to show -1 posts on first Blog! page