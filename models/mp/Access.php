<?php
/**
 * File Name: Access.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/24 9:23 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mp;


use qh4module\wechat\external\ExtWechat;
use qttx\web\ServiceModel;

class Access extends ServiceModel
{
    public $signature;

    public $timestamp;

    public $nonce;

    public $echostr;

    /**
     * @var ExtWechat
     */
    protected $external;

    public function run()
    {
        /*
         * 开发者通过检验signature对请求进行校验
         * 若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，成为开发者成功，否则接入失败。
         * 加密/校验流程如下：
         * 1）将token、timestamp、nonce三个参数进行字典序排序
         * 2）将三个参数字符串拼接成一个字符串进行sha1加密
         * 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
         */
        $token = $this->external->mpToken();
//        $tmpArr = array($token, $this->timestamp, $this->nonce);
//        sort($tmpArr, SORT_STRING);
//        $tmpStr = implode($tmpArr);
//        $tmpStr = sha1($tmpStr);
        $tmp_str = self::sha1($token, $this->timestamp, $this->nonce);
        if ($tmp_str == $this->signature) {
            echo $this->echostr;
            exit;
        } else {
            return false;
        }
    }

    public static function sha1($token, $timestamp, $nonce)
    {
        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr, SORT_STRING);
        $tmp_str = implode($tmp_arr);
        return sha1($tmp_str);
    }
}