<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/28
 * Time: 21:14
 */

namespace App\Http\Result;

/**
 * 结果集表
 * Class Base
 * @package App\Http\Result
 */
class Resp
{
    /**
     * 返回码
     * @var int
     */
    private $_code = SUCCESS_CODE;

    /**
     * 返回提示
     */
    private $_message = SUCCESS_MSG;


    /**
     * 数据
     */
    private $_data = [];

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @var实例
     */
    private static $_instance;

    CONST ERROR_ILLEGAL = '非法请求';
    CONST ERROR_FAIL = '失败';


    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    private function __construct()
    {
    }

    public function isSucceed()
    {
        return $this->getCode() == SUCCESS_CODE;
    }

    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function setCodeMsg($code = '', $message = '')
    {
        return $this->setCode($code)->setMessage($message);
    }

    public function setMsgCode($message = '', $code = -1)
    {
        return $this->setCode($code)->setMessage($message);
    }

    /**
     * ajax返回
     * @param string $code
     * @param string $message
     */
    public static function Ajax($code = '', $message = '')
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode(['code'=>$code ,'message'=>$message]));
    }


}