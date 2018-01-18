<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 10:29
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\vendor\Generate;
use App\Http\vendor\DbParse;
/**
 * 页面生成器
 * Class TestController
 * @package App\Http\Controllers\Admin
 */
class TestController extends Controller
{

    public function index()
    {
        echo 'index';
    }

    /**
     * 生成form表单数据
     */
    public function form($table)
    {
        $result = DbParse::getInstance()
            ->readFromDb($table)
            ->generateForm();
        dd($result);
    }

    /**
     * 生成model类
     */
    public function model($table)
    {
        $result = DbParse::getInstance()
            ->readFromDb($table)
            ->generateModel();
        dd($result);
    }

    /**
     * 生成request类
     */
    public function request($table)
    {
        $result = DbParse::getInstance()
            ->readFromDb($table)
            ->generateRequest();
        dd($result);
    }

    //同一生成model\request\form
    public function run($table, $message, $code)
    {
        $result = DbParse::getInstance($table, $message, $code)
            ->readFromDb($table)
            ->generateModel()
            ->generateRequest()
            ->controller()
            ->addview()
            ->indexView($code)
            ->editView()
            ->route();
        dd($result);
    }

    //1.生成路由
    public function route($table, $message)
    {
        $result = DbParse::getInstance()
            ->readFromDb($table)
            ->route($message);
        dd($result);
    }

    //2.生成控制器
    public function controller($table, $message)
    {
        $result = DbParse::getInstance($table, $message)->controller();
        dd($result);
    }


    //3.生成页面
    //(1).添加页面
    public function viewAdd($table, $message)
    {
        $result = DbParse::getInstance($table, $message)->addview();
        dd($result);
    }

    //(2).列表页面
    public function viewIndex($table, $message, $code)
    {
        $result = DbParse::getInstance($table, $message)->indexView($code);
        dd($result);
    }

    //(3).编辑页面
    public function viewEdit($table, $message)
    {
        $result = DbParse::getInstance($table, $message)->editView();
        dd($result);
    }

    public function viewAll($table, $message, $code)
    {
        $result = DbParse::getInstance($table, $message)
            ->addview()
            ->indexView($code)
            ->editView();
        dd($result);
    }
}