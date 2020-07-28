<?php
/*
 * QAQ日志记录
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/26
 */

namespace QAQ\Kernel;

class Log
{
    protected $dir = QAQ_CORE_DIR . 'Storage/Log/';
    protected $log_name;

    public function __construct()
    {
        if (!is_dir($this->dir)) {
            QAQMakeDir($this->dir);
        }
        $this->log_name = $this->dir . 'Log_' . date('YmdH') . '.QAQ';
    }

    public static function Write($level, $msg)
    {
        $static = new static();
        $msg = date('Y-m-d H:i:s') . ' - [' . $level . '] - ' . $msg . PHP_EOL;
        @file_put_contents($static->log_name, $msg, FILE_APPEND);
    }

    private static function GetLogDIr()
    {
        return QAQ_CORE_DIR . 'Storage/Log/';
    }

    public static function Clear()
    {
        $dir = self::GetLogDIr();
        if (!file_exists($dir)) return true;
        $dh = @opendir($dir);
        while ($file = @readdir($dh)) {
            if ($file != "." && $file != "..") {
                $path = $dir . "/" . $file;
                if (!is_dir($path)) {
                    @unlink($path);
                } else {
                    self::Clear();
                }
            }
        }
        @closedir($dh);
        if (@rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    public static function CheckLog()
    {
        $dir = self::GetLogDIr();
        if (!file_exists($dir)) return true;
        $dh = @opendir($dir);
        while ($file = @readdir($dh)) {
            if ($file != "." && $file != "..") {
                $path = $dir . "/" . $file;
                //获取日志保留时间戳
                $rm_time = (time() - (3600 * 24 * Config::get('log_save_day')));
                //删除保留时间前的日志
                if (@filemtime($path) && filemtime($path) < $rm_time) {
                    @unlink($path);
                }
            }
        }
        @closedir($dh);
    }
}