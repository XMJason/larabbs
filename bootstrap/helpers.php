<?php

/**
 * 将当前请求的路由名称转换为 CSS 类名称，作用是允许我们针对某个页面做页面样式定制
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
// 通过文章内容自动生成话题摘录，作为文章页面的 description 元标签使用，有利于 SEO 搜索引擎优化
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', '', strip_tags($value)));
    return str_limit($excerpt, $length);
}

 ?>
