<?php get_header(); ?>

<?php if (have_posts()): ?>
    <ul class="post-list">
        <?php while (have_posts()): the_post(); ?>
            <li class="post-item layui-card">
                <div class="layui-card-header">
                    <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                </div>
                <div class="layui-card-body">
                    <div class="post-meta">
                        <span><?php echo get_the_date(); ?></span>
                        <span> · </span>
                        <span><?php the_category(', '); ?></span>
                        <span> · </span>
                        <span><?php comments_number('0 条评论', '1 条评论', '% 条评论'); ?></span>
                    </div>
                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                    <a class="read-more layui-btn layui-btn-sm" href="<?php the_permalink(); ?>">阅读全文</a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>

    <?php andu_render_pagination(); ?>
<?php else: ?>
    <p>暂无内容。</p>
<?php endif; ?>

<?php get_footer(); ?>
