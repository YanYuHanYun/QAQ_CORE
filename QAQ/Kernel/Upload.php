<?php
/*
 * QAQ 文件上传引擎
 * Author:烟雨寒云
 * Mail:admin@yyhy.me
 * Date:2020/04/18
 */

namespace QAQ\Kernel;

class Upload
{
    //设定属性：保存允许上传的MIME类型
    private static $types = ['image/jpg', 'image/jpeg', 'image/gif'];

    //修改允许上传类型
    public static function setTypes($types = [])
    {
        //判定是否为空
        if (!empty($types)) self::$types = $types;
    }

    public static $error;    //记录单文件上传过程中出现的错误信息
    public static $errors;   //记录多文件上传过程中出现的错误信息
    public static $files;    //记录多文件上传成功后文件名对应信息

    /**
     * @desc 单文件上传
     * @param string $file ,上传文件信息数组
     * @param string $path ,上传路径
     * @param int $max = 2M,最大上传大小
     * @return bool|string,成功返回文件名，失败返回false
     */
    public static function uploadOne($file, $path, $max = 2000000)
    {
        //判定文件有效性
        if (!isset($file['error']) || count($file) != 5) {
            self::$error = '错误的上传文件！';
            return false;
        }
        //路径判定
        if (!is_dir($path)) {
            QAQMakeDir($path);
        }
        //判定文件是否正确上传
        switch ($file['error']) {
            case 1:
            case 2:
                self::$error = '文件超过服务器允许大小！';
                return false;
            case 3:
                self::$error = '文件只有部分被上传！';
                return false;
            case 4:
                self::$error = '没有选中要上传的文件！';
                return false;
            case 6:
            case 7:
                self::$error = '服务器错误！';
                return false;
        }
        //判定文件类型
        if (!in_array($file['type'], self::$types)) {
            self::$error = '当前上传的文件类型不允许！';
            return false;
        }
        //判定业务大小
        if ($file['size'] > $max) {
            self::$error = '当前上传的文件超过允许的大小！当前允许的大小是：' . (string)($max / 1000000) . 'M';
            return false;
        }
        //获取随机名字
        $filename = self::getRandomName($file['name']);
        //移动上传的临时文件到指定目录
        if (move_uploaded_file($file['tmp_name'], $path . '/' . $filename)) {
            //成功
            return $filename;
        } else {
            //失败
            self::$error = '文件移动失败！';
            return false;
        }
    }

    /**
     * @desc 多文件上传
     * @param array $files ,上传文件信息二维数组
     * @param string $path ,上传路径
     * @param int $max = 2M,最大上传大小
     * @return bool 是否全部上传成功
     */
    public static function uploadAll($files, $path, $max = 2000000)
    {
        for ($i = 0, $len = count($files['name']); $i < $len; $i++) {
            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );
            $res = self::uploadOne($file, $path, $max);
            if (!$res) {
                //错误处理
                $error = self::$error;
                self::$errors[] = "文件：{$file['name']}上传失败:{$error}!<br>";
            } else {
                self::$files[] = $file['name'] . '=>' . $res;
            }
        }
        if (!empty(self::$errors)) {
            //错误处理
            //var_dump(self::$errors);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @desc 获取随机文件名
     * @param string $filename ,文件原名
     * @param string $prefix ,前缀
     * @return string,返回新文件名
     */
    public static function getRandomName($filename, $prefix = 'image')
    {
        //取出源文件后缀
        $ext = strrchr($filename, '.');
        //构建新名字
        $new_name = $prefix . date('YmdHis');
        //增加随机字符（6位大写字母）
        for ($i = 0; $i < 6; $i++) {
            $new_name .= chr(mt_rand(65, 90));
        }
        //返回最终结果
        return $new_name . $ext;
    }
}