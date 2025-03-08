<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2021/9/27
 * Time: 11:01
 */

namespace common\repository;


class LogRepo extends BaseRepo
{
    public static function file_log($file_target,$data)
    {
        $content = is_array($data) ? var_export($data,1):$data;
        $content = date('Y-m-d H:i:s').'|'.$content."\n";
        file_put_contents($file_target,$content,FILE_APPEND);
    }
}