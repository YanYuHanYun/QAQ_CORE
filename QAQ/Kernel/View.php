<?php
/*
 * QAQ模板引擎使用thinkphp组件
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

use think\facade\Template;

class View extends Template
{
    public static function Rendering($path, $vars = [], $config)
    {
        $template = new \think\Template();
        $template->config($config);
        $template->assign($vars);
        return $template->fetch($path);
    }
}