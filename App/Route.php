<?php
/*
 * 应用路由
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

use QAQ\Kernel\Route;

Route::get('/', 'Index/index');
Route::get('/index', 'Index/index');
Route::get('/say/^', 'Index/index');