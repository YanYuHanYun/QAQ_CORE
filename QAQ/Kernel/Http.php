<?php
/*
 * QAQ HTTP服务核心
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/26
 */

namespace QAQ\Kernel;

class Http
{
    public static function Start()
    {
        //QAQ_Header
        header('X-Powered-By: QAQ_CORE_V' . QAQ_VERSION);
        //应用启动
        $res = App::init(self::AnalyticUrl())->run();
        if (is_array($res)) {
            header('Content-Type:text/json');
            echo json($res);
        } else if (is_string($res)) {
            echo $res;
        }
        //提高页面响应
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        //检查过期缓存
        Cache::CheckCache();
        //检查过期日志
        Log::CheckLog();
    }

    private static function AnalyticUrl()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        //路由模式
        if (Config::get('must_route')) {
            //注册并匹配路由
            $url = Route::register(Config::get('route_files'))->FindRule($path, self::GetRequestType());
        }
        //开始解析
        $params = explode('/', $url);
        //去空
        $params = array_filter($params);
        //重新排列下标
        $params = array_merge($params);
        //默认控制器
        if (count($params) < 1) $params[0] = Config::get('default_controller_name');
        //默认操作
        if (count($params) < 2) $params[1] = Config::get('default_action_name');
        //多应用模式
        if (Config::get('multi_app')) {
            //默认模块
            if (count($params) < 1) $params[0] = Config::get('default_module_name');
            //默认控制器
            if (count($params) < 2) $params[1] = Config::get('default_controller_name');
            //默认操作
            if (count($params) < 3) $params[2] = Config::get('default_action_name');
            //设置基础请求参数
            $module = $params[0];
            $controller = $params[1];
            $action = $params[2];
            $i = 3;
        } else {
            //设置基础请求参数
            $controller = $params[0];
            $action = $params[1];
            $i = 2;
        }
        //设置参数
        $value = [];
        while ($i < count($params)) {
            $value[] = $params[$i];
            $i++;
        }
        return [
            's' => $url,
            'params' => $params,
            'module' => $module ?? false,
            'controller' => $controller,
            'action' => $action,
            'value' => $value
        ];
    }

    public static function GetRequestType()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}