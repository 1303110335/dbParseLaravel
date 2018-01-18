<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * --------页面生成器------------
 */
Route::get('test/index', 'TestController@index');
Route::get('test/model/{table}', 'TestController@model');
Route::get('test/request/{table}', 'TestController@request');
Route::get('test/form/{table}', 'TestController@form');
//使用示例： http://www.loan.com/admin/test/run/m_android_version/渠道添加/version_name
Route::get('test/run/{table}/{message}/{code}', 'TestController@run');
Route::get('test/route/{table}/{message}', 'TestController@route');
Route::get('test/controller/{table}/{message}', 'TestController@controller');
Route::get('test/viewAdd/{table}/{message}', 'TestController@viewAdd');
Route::get('test/viewIndex/{table}/{message}/{code}', 'TestController@viewIndex');
Route::get('test/viewEdit/{table}/{message}', 'TestController@viewEdit');
Route::get('test/viewAll/{table}/{message}/{code}', 'TestController@viewAll');