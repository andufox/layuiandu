<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site layui-container">
    <header class="site-header">
        <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
        <?php if (get_bloginfo('description')): ?>
            <div class="site-description"><?php bloginfo('description'); ?></div>
        <?php endif; ?>
    </header>
    <div class="layui-row andu-layout">
        <aside class="layui-col-xs12 layui-col-md2 andu-left">
            <nav class="nav">
                <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'layui-nav layui-nav-tree', 'fallback_cb' => 'layuiandu_menu_fallback']); ?>
            </nav>
        </aside>
        <main id="content" class="content layui-col-xs12 layui-col-md8 andu-center">
