    </main>
        <aside class="layui-col-xs12 layui-col-md2 andu-right">
            <form role="search" method="get" class="search-form layui-form" action="<?php echo esc_url(home_url('/')); ?>">
                <label>
                    <input type="search" class="layui-input" name="s" value="<?php echo get_search_query(); ?>" placeholder="搜索...">
                </label>
                <input type="submit" class="layui-btn" value="搜索">
            </form>
            
            <?php if (is_active_sidebar('sidebar-1')) { dynamic_sidebar('sidebar-1'); } ?>
        </aside>
    </div>
    <footer class="footer">
        <div>© <?php echo date('Y'); ?> <?php bloginfo('name'); ?> · Powered by WordPress · <a href="https://github.com/andufox/layuiandu">Theme: Layui.Andu</a></div>
    </footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
