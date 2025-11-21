<?php get_header(); ?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>
    <article <?php post_class(); ?>>
        <div class="layui-card">
            <div class="layui-card-header">
                <h1 class="post-title"><?php the_title(); ?></h1>
            </div>
            <div class="layui-card-body">
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
                <?php wp_link_pages(['before' => '<div class="pagination">', 'after' => '</div>']); ?>
                <?php comments_template(); ?>
            </div>
        </div>
    </article>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
