<?php
/**
 * File Name: ExtWechat.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/24 9:16 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\external;


use qh4module\upload\HpUpload;
use qttx\helper\FileHelper;
use qttx\helper\StringHelper;
use qttx\web\External;



/**
 * Class ExtWechat
 * 微信相关的配置类
 * 这个类是微信的公共配置,比如支付相关
 * @package qh4module\wechat\external
 */
abstract class ExtWechat extends External
{
    /**
     * 商户号
     * @return string
     */
    abstract public function merchantId();

    /**
     * 商户的api证书序列号
     * @return string
     * @see https://pay.weixin.qq.com/index.php/core/cert/api_cert
     * 生成证书后点击查看证书可以找到这个序列号
     */
    abstract public function merchantApiSerialNumber();

    /**
     * 商户私钥文件路径
     * @return string
     * @see https://pay.weixin.qq.com/index.php/core/cert/api_cert
     */
    abstract public function merchantPrivateKey();

    /**
     * 微信支付平台证书文件路径
     * 注意: 这里的文件,不是用微信api工具生成的文件
     *      需要自己用命令行下载,微信提供的开发包中带有一个 CertificateDownloader.php 的文件,就是专门下载这个证书的
     * @see TraitWechatController::actionGetDownloadWechatCertificateCommand() 下载命令太过复杂,可以看这个接口
     * @return string
     * @see https://pay.weixin.qq.com/index.php/core/cert/api_cert
     */
    abstract public function wechatpayCertificate();

    /**
     * 微信支付平台的序列号
     * 下载下来的支付平台证书文件名类似于 `wechatpay_34A057AD0EF4ED42D0D5F84D26052B907FB11737.pem`
     * 其中 `34A057AD0EF4ED42D0D5F84D26052B907FB11737` 就是这段序列号
     * @see wechatpayCertificate() 关于文件的下载
     * @return string
     */
    abstract public function wechatpaySerial();

    /**
     * 商户平台的api密钥
     * @return string
     */
    abstract public function merchantApiKey();

    /**
     * 商户平台v3版本的密钥
     * @return string
     */
    abstract public function merchantV3ApiKey();

    /**
     * 远程文件保存
     * @param $url string 文件地址
     * @return string 写入数据库的路径
     */
    public function saveFile($url)
    {
        $m = date('Y-m');
        $d = date('d');
        $dir = StringHelper::combPath(APP_PATH, 'uploads', $m, $d);
        FileHelper::mkdir($dir);
        $path = StringHelper::combPath($dir, StringHelper::random(16) . '.jpg');
        HpUpload::downloadFileFormUrl($url, $path);
        return str_replace(APP_PATH, '', $path);
    }

    /**
     * 微信支付回调处理
     * @return ExtWechatPayNotify
     */
    public function payNotify()
    {
        // 这里返回你自己的实际处理类
        return new ExtWechatPayNotify();
    }

    /**
     * @return string 返回订单表名称
     */
    abstract function orderTableName();

    /**
     * @return string 返回订单表中表示订单号的字段名
     */
    abstract function orderTableOrderNoField();

    /**
     * @return string 返回订单表中表示微信订单号的字段名称
     */
    abstract function orderTableWechatOrderNoField();

    /**
     * 用户账号表的名称
     * @return string
     */
    public function userTableName()
    {
        return '{{%user}}';
    }


}