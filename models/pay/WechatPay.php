<?php
/**
 * File Name: WechatPay.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/13 5:06 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\pay;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use qh4module\wechat\external\ExtWechat;
use qh4module\wechat\external\JSAPIRrePayData;
use qttx\web\ServiceModel;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

/**
 * Class WechatPay
 * 微信支付的基类
 * @package qh4module\wechat\models\pay
 * @property ExtWechat $external
 */
class WechatPay extends ServiceModel
{

    /**
     * @var JSAPIRrePayData
     */
    public $data;


    public function __construct(JSAPIRrePayData $data = null, $config = [])
    {
        parent::__construct($config);
        $this->data = $data;
    }


    /**
     * 获取支付用的client
     * @return Client
     */
    public function getClient()
    {
        // 商户号
        $merchantId = $this->external->merchantId();
        // 商户API证书序列号
        $merchantSerialNumber = $this->external->merchantApiSerialNumber();
        // 商户私钥文件路径
        $merchantPrivateKey = PemUtil::loadPrivateKey($this->external->merchantPrivateKey());
        // 微信支付平台证书文件路径
        $wechatpayCertificate = PemUtil::loadCertificate($this->external->wechatpayCertificate());

        // 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            // 传入商户相关配置
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey)
            // 可传入多个微信支付平台证书，参数类型为array
            ->withWechatPay([$wechatpayCertificate])
            ->build();

        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client时，将HandlerStack传入，接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        $client = new Client(['handler' => $stack]);

        return $client;
    }
}