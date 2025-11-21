<?php
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    register_nav_menus([
        'primary' => '主导航',
    ]);
});

add_action('wp_enqueue_scripts', function () {
    $vendor_path = get_template_directory() . '/assets/vendor/layui';
    $vendor_uri  = get_template_directory_uri() . '/assets/vendor/layui';
    $has_local_css = file_exists($vendor_path . '/layui.css');
    $has_local_js  = file_exists($vendor_path . '/layui.js');
    $layui_css = $has_local_css ? ($vendor_uri . '/layui.css') : 'https://cdn.jsdelivr.net/npm/layui@2.8.17/dist/css/layui.css';
    $layui_js  = $has_local_js  ? ($vendor_uri . '/layui.js')  : 'https://cdn.jsdelivr.net/npm/layui@2.8.17/dist/layui.js';
    $css_ver = $has_local_css ? @filemtime($vendor_path . '/layui.css') : '2.8.17';
    $js_ver  = $has_local_js  ? @filemtime($vendor_path . '/layui.js')  : '2.8.17';
    wp_enqueue_style('andu-layui', $layui_css, [], $css_ver);
    wp_enqueue_style('andu-style', get_stylesheet_uri(), ['andu-layui'], wp_get_theme()->get('Version'));
    $accent = '#1a73e8';
    wp_add_inline_style('andu-style', ':root{--accent:' . esc_attr($accent) . '}');
    wp_enqueue_script('andu-layui', $layui_js, [], $js_ver, true);
});

// content width
add_action('after_setup_theme', function () {
    $GLOBALS['content_width'] = $GLOBALS['content_width'] ?? 760;
}, 0);

// excerpt length and more
add_filter('excerpt_length', function ($length) { return 28; });
add_filter('excerpt_more', function ($more) { return '...'; });

// menu fallback
function layuiandu_menu_fallback() {
    echo '<div class="nav">';
    wp_page_menu(['show_home' => true]);
    echo '</div>';
}

add_filter('get_avatar', function ($html, $id_or_email, $size, $default, $alt, $args) {
    if (is_object($id_or_email) && $id_or_email instanceof WP_Comment) { return ''; }
    return $html;
}, 9, 6);

add_filter('nav_menu_css_class', function($classes){
    $classes[] = 'layui-nav-item';
    return $classes;
}, 10);

add_filter('nav_menu_submenu_css_class', function($classes){
    $classes[] = 'layui-nav-child';
    return $classes;
}, 10);

add_filter('the_title', function ($title) {
    if (!is_search()) return $title;
    $q = trim(get_search_query());
    if ($q === '') return $title;
    $terms = preg_split('/\s+/u', $q);
    foreach ($terms as $t) {
        if ($t === '') continue;
        $title = preg_replace('/(' . preg_quote($t, '/') . ')/iu', '<mark class="search-highlight">$1</mark>', $title);
    }
    return $title;
}, 10);

add_filter('get_the_excerpt', function ($excerpt, $post) {
    if (!is_search()) return $excerpt;
    $q = trim(get_search_query());
    if ($q === '') return $excerpt;
    $terms = preg_split('/\s+/u', $q);
    $content = get_post_field('post_content', $post) ?: '';
    $plain = wp_strip_all_tags($content !== '' ? $content : $excerpt);
    $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
    $len = function_exists('mb_strlen') ? mb_strlen($plain, 'UTF-8') : strlen($plain);
    $pos = null; $matchTerm = '';
    foreach ($terms as $t) {
        if ($t === '') continue;
        $p = function_exists('mb_stripos') ? mb_stripos($plain, $t, 0, 'UTF-8') : stripos($plain, $t);
        if ($p !== false && ($pos === null || $p < $pos)) { $pos = $p; $matchTerm = $t; }
    }
    $context = 60; $snippetLen = 160;
    if ($pos === null) {
        $start = 0;
    } else {
        $start = max(0, (int)$pos - $context);
    }
    $substr = function($s, $st, $ln){ return function_exists('mb_substr') ? mb_substr($s, $st, $ln, 'UTF-8') : substr($s, $st, $ln); };
    $snippet = $substr($plain, $start, $snippetLen);
    $prefix = $start > 0 ? '…' : '';
    $suffix = ($start + $snippetLen) < $len ? '…' : '';
    foreach ($terms as $t) {
        if ($t === '') continue;
        $snippet = preg_replace('/(' . preg_quote($t, '/') . ')/iu', '<mark class="search-highlight">$1</mark>', $snippet);
    }
    return $prefix . $snippet . $suffix;
}, 10, 2);

add_filter('comment_form_defaults', function ($defaults) {
    $defaults['class_submit'] = 'layui-btn layui-btn-primary';
    $defaults['submit_button'] = '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>';
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">评论</label><textarea id="comment" name="comment" class="layui-textarea" cols="45" rows="8" aria-required="true"></textarea></p>';
    return $defaults;
});

add_filter('comment_form_default_fields', function ($fields) {
    $append_class = function ($html, $class) {
        if (preg_match('/<input[^>]*class="([^"]*)"/i', $html)) {
            $html = preg_replace('/(<input[^>]*class=")([^"]*)("[^>]*>)/i', '$1$2 ' . $class . '$3', $html);
        } else {
            $html = preg_replace('/<input/i', '<input class="' . $class . '"', $html, 1);
        }
        return $html;
    };
    foreach (['author', 'email', 'url'] as $key) {
        if (isset($fields[$key])) { $fields[$key] = $append_class($fields[$key], 'layui-input'); }
    }
    return $fields;
});

add_filter('comments_open', function($open, $post_id){
    return true;
}, 99, 2);

function andu_render_pagination() {
    global $wp_query;
    if (!$wp_query) return;
    $found = intval($wp_query->found_posts);
    $limit = intval(get_query_var('posts_per_page')) ?: intval(get_option('posts_per_page')) ?: 10;
    $curr = 1;
    $candidates = [
        intval(get_query_var('paged')),
        intval(get_query_var('page')),
        isset($GLOBALS['paged']) ? intval($GLOBALS['paged']) : 0,
        isset($wp_query->query_vars['paged']) ? intval($wp_query->query_vars['paged']) : 0,
    ];
    foreach ($candidates as $c) { if ($c > 0) { $curr = $c; break; } }
    $max  = intval($wp_query->max_num_pages);
    if ($max <= 1) return;
    $links = [];
    for ($i = 1; $i <= $max; $i++) {
        $links[$i] = esc_url(get_pagenum_link($i));
    }
    if ($curr > $max) { $curr = $max; }
    echo '<div id="andu-pagination"></div>';
    echo '<script>(function(){var links=' . wp_json_encode($links) . ';function init(){if(!window.layui||!layui.laypage){return false;}var laypage=layui.laypage;laypage.render({elem:"andu-pagination",count:' . $found . ',limit:' . $limit . ',curr:' . $curr . ',theme:"#1E9FFF",layout:["prev","page","next"],jump:function(obj,first){if(!first){var url=links[obj.curr]||links[1];location.href=url;}}});return true;}if(!init()){var t=setInterval(function(){if(init()){clearInterval(t);}},80);}})();</script>';
}

add_action('init', function () {
    if (!is_user_logged_in() || !current_user_can('publish_posts')) return;
    if (!isset($_GET['andu_add_random_post'])) return;
    $count = intval($_GET['andu_add_random_post']);
    if ($count <= 0) { $count = 1; }
    if ($count > 4) { $count = 4; }
    $sentences = [
        '在城市的光影里，人与事交织成网，我们在步履之间寻找节奏与意义。',
        '清晨的第一缕阳光穿过窗棂，未完成的梦想被重新点亮，新的问题也随之诞生。',
        '写作不是答案的陈列，而是提问的延伸；尝试不是避免失败，而是拥抱不确定。',
        '技术与生活并非泾渭分明，它们在键盘与日常之间相互渗透、彼此修正。',
        '如果你愿意停下来听一听，世界也会靠近一步，微小的光亮足以穿透厚重的迷雾。',
        '我们用键盘敲击生活的节拍，也用沉默记录心跳的呼应，让真实成为不断被发现的过程。',
        '每一次迭代都是对过去的温柔告别，也是对未来的勇敢邀请。',
        '在路途上，我们学会把困惑折叠进经验，把偶然缝进习惯，把偏见改写成理解。',
        '当我们在屏幕前凝视，别忘了抬头看看窗外：风在树影里奔跑，时间在云层中停顿。',
        '不必着急抵达，不妨慢一点走，给自己留出与世界对话的空白与距离。',
        '问题从不消失，它们在回答中变形，在沉默里重生，在尝试里取得平衡。',
        '学习的过程像河流，曲折向前；每一次失败都是一次拐弯，每一次成功都是一次回环。',
        '我们在不确定中练习耐心，在复杂里寻找秩序，在混沌中维持温柔。',
        '改变并非推翻旧事物，而是让它们在新的语境里继续发光。',
        '当夜色落下，屏幕之外仍有风景，别忘了为自己留下一点安静的角落。',
    ];
    $cats = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
    for ($i = 0; $i < $count; $i++) {
        $title = '随机长文 ' . wp_rand(10, 999);
        $body = '';
        $len = 0;
        while ($len < 520) {
            $idx = array_rand($sentences);
            $body .= $sentences[$idx] . "\n\n";
            $len = function_exists('mb_strlen') ? mb_strlen($body, 'UTF-8') : strlen($body);
        }
        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $body,
            'post_status'  => 'publish',
            'post_type'    => 'post',
        ]);
        if (!is_wp_error($post_id) && $post_id && !empty($cats)) {
            $pick = $cats[array_rand($cats)];
            wp_set_post_terms($post_id, [$pick->term_id], 'category', true);
        }
    }
    wp_safe_redirect(remove_query_arg('andu_add_random_post'));
    exit;
});

add_action('init', function () {
    if (get_option('andu_seed_posts20_done')) { return; }
    $need = 4;
    $existing = wp_count_posts('post');
    $existing_count = isset($existing->publish) ? intval($existing->publish) : 0;
    if ($existing_count >= $need) { update_option('andu_seed_posts20_done', 1); return; }
    $cats = ['技术','教程','生活','随笔'];
    for ($i = 1; $i <= $need; $i++) {
        $title = '示例长文 ' . $i;
        $parts = [
            '在城市的光影里，人与事交织成网，我们在步履之间寻找节奏与意义。',
            '当清晨的第一缕阳光穿过窗棂，未完成的梦想被重新点亮，新的问题也随之诞生。',
            '写作不是答案的陈列，而是提问的延伸；尝试不是避免失败，而是拥抱不确定。',
            '技术与生活并非泾渭分明，它们在键盘与日常之间相互渗透、彼此修正。',
            '如果你愿意停下来听一听，世界也会靠近一步，微小的光亮足以穿透厚重的迷雾。',
            '我们用键盘敲击生活的节拍，也用沉默记录心跳的呼应，让真实成为不断被发现的过程。',
            '每一次迭代都是对过去的温柔告别，也是对未来的勇敢邀请。',
            '在路途上，我们学会把困惑折叠进经验，把偶然缝进习惯，把偏见改写成理解。',
            '当我们在屏幕前凝视，别忘了抬头看看窗外：风在树影里奔跑，时间在云层中停顿。',
            '不必着急抵达，不妨慢一点走，给自己留出与世界对话的空白与距离。',
        ];
        $body = '';
        for ($k = 0; $k < 8; $k++) { $body .= $parts[$k % count($parts)] . "\n\n"; }
        $body .= '第 ' . $i . ' 次书写，让答案继续生长，让问题更清晰。';
        $postarr = [
            'post_title'   => $title,
            'post_content' => $body,
            'post_status'  => 'publish',
            'post_type'    => 'post',
        ];
        $post_id = wp_insert_post($postarr);
        if (!is_wp_error($post_id) && $post_id) {
            wp_set_post_terms($post_id, [$cats[($i - 1) % count($cats)]], 'category', true);
        }
    }
    update_option('andu_seed_posts20_done', 1);
});
