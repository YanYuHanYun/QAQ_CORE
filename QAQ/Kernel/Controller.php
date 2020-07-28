<?php
/*
 * QAQ 控制器基类
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;
class Controller
{
    protected $_parents = [
        'QAQ\\Kernel\\Jump',
        'QAQ\\Kernel\\Request',
        'QAQ\\Kernel\\App',
        'QAQ\\Kernel\\View',
    ];

    public function __call($method, $args)
    {
        foreach ($this->_parents as $p) {
            if (is_callable([$p, $method])) {
                return call_user_func_array([
                    $p, $method
                ], $args);
            }
        }
        return call_user_func_array([
            $this, $method
        ], $args);
    }
}