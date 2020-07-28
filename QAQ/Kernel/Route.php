<?php
/*
 * QAQ 路由引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;
class Route
{
    protected static $group = false;
    protected static $rules = [];

    public static function register($route_files)
    {
        //初始化
        self::$rules['get'] = [];
        self::$rules['post'] = [];
        self::$rules['rule'] = [];
        //注册路由
        foreach ($route_files as $route_file) {
            if (!file_exists($route_file)) {
                throw new \Exception('QAQ Route File No Exists！');
            }
            include $route_file;
        }
        return new self();
    }

    public static function get($url, $action)
    {
        self::SetRule('get', $url, $action);

    }

    public static function post($url, $action)
    {
        self::SetRule('post', $url, $action);
    }

    public static function rule($url, $action)
    {
        self::SetRule('rule', $url, $action);
    }

    private static function SetRule($type, $url, $action)
    {
        if (self::$group) {
            $url = self::$group . $url;
        }
        //去除最后的/
        if ($url[strlen($url) - 1] == '/' && $url != '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        self::$rules[$type][$url] = $action;
    }

    public static function group($name, $func)
    {
        //设置分组
        self::$group = $name;
        //执行闭包
        $func();
        //分组路由注册完毕,清空分组
        self::$group = false;
    }

    public function FindRule($url, $type)
    {
        //去除最后的/
        if ($url[strlen($url) - 1] == '/' && $url != '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        $rules = self::$rules[$type];
        foreach ($rules as $rule => $real) {
            //路由兼容处理
            $rule = str_replace('//', '/', $rule);
            //直接匹配到
            if ($url == $rule) return $real;
            //开始进行规则匹配
            if (strpos($rule, '/^') !== false) {
                //去除^
                $rule = str_replace('/^', '', $rule);
                //防止去除完为空
                if ($rule == '') $rule = '/';
                //再次尝试直接匹配
                if ($url == $rule) return $real;
                //正则匹配
                $preg = '~^\\' . $rule . '~';
                if (preg_match($preg, $url)) {
                    $value = str_replace_once($rule, '', $url);
                    //防止value前面没有/
                    if ($value[0] != '/') $value = '/' . $value;
                    return $real . $value;
                }
            }
        }
        //匹配任意路由
        if ($type != 'rule') {
            self::FindRule($url, 'rule');
        }
        //路由不存在
        throw new \Exception('QAQ Route Rule Not Found！', -2000);
    }
}