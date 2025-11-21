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
    <div class="layui-fixbar">
        <a href="#" class="layui-icon layui-icon-top layui-fixbar-top" id="andu-back-top" style="display:none"></a>
    </div>
    <footer class="footer">
        <div>© <?php echo date('Y'); ?> <?php bloginfo('name'); ?> · Powered by <a href="https://wordpress.org/">WordPress</a> ·Theme: <a href="https://github.com/andufox/layuiandu">LayuiAndu</a></div>
    </footer>
</div>
<?php wp_footer(); ?>
<script>
(function(){var b=document.getElementById('andu-back-top');if(!b){return}function t(){if(window.scrollY>200){b.style.display='block'}else{b.style.display='none'}}t();window.addEventListener('scroll',t);b.addEventListener('click',function(e){e.preventDefault();window.scrollTo({top:0,behavior:'smooth'})});})();
</script>
</body>
</html>
