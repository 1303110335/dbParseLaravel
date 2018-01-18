<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/6
 * Time: 22:05
 */

namespace App\Http\vendor;


class View
{
    /**
     * 读取页面内容
     * @param $file
     * @param $data
     * @param $title
     * @param $addAction
     * @param $lowerName
     * @param $listAction
     * @return string
     */
    public static function includeAddFile($file, $data, $title, $addAction, $lowerName, $listAction)
    {
        ob_start();
        require_once $file;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    /**
     * @param $file
     * @param $data
     * @param $title
     * @param $code
     * @param $message
     * @param $addUrl
     * @param $className
     * @param $editUrl
     * @param $editAuthUrl
     * @param $addAuthUrl
     * @return string
     */
    public static function includeIndexFile($file, $data, $title, $code, $message, $addUrl, $className, $editUrl, $editAuthUrl, $addAuthUrl)
    {
        ob_start();
        require_once $file;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    /**
     * @param $file
     * @param $data
     * @param $title
     * @param $updateAction
     * @param $lowerName
     * @param $listAction
     * @return string
     */
    public static function includeEditFile($file, $data, $title, $updateAction, $lowerName, $listAction)
    {
        ob_start();
        require_once $file;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

}