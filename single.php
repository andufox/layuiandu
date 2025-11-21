<?php get_header(); ?>

<?php if (have_posts()): while (have_posts()): the_post(); ?>
    <article <?php post_class(); ?>>
        <div class="layui-card">
            <div class="layui-card-header">
                <h1 class="post-title"><?php the_title(); ?></h1>
                <div class="post-meta">
                    <span><?php echo get_the_date(); ?></span>
                    <span> · </span>
                    <span><?php the_category(', '); ?></span>
                    <span> · </span>
                    <span><?php comments_number('0 条评论', '1 条评论', '% 条评论'); ?></span>
                </div>
            </div>
            <div class="layui-card-body">
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
                <div class="pagination">
                    <div class="prev"><?php previous_post_link('%link', '上一篇'); ?></div>
                    <div class="next"><?php next_post_link('%link', '下一篇'); ?></div>
                </div>
            </div>
        </div>
        <?php comments_template(); ?>
    </article>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
