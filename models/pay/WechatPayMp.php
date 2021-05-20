<?php
/**
 * File Name: WechatPay.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/30 9:02 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\pay;


use GuzzleHttp\Exception\RequestException;
use qh4module\wechat\external\ExtWechatMp;
use qttx\helper\StringHelper;
use WechatPay\GuzzleMiddleware\Auth\PrivateKeySigner;

/**
 * Class WechatPayMp
 * 微信公众号支付
 * @package qh4module\wechat\models\mp
 * @property ExtWechatMp $external
 */
class WechatPayMp extends WechatPay
{
    /**
     * 构造公众号支付用的二次签名数据
     * 返回的数据提供给前段用于拉起支付
     * @param $prepay_id string
     * @return array
     */
    public function generateMpJsApiPayData($prepay_id)
    {
        $ary = [
            'appId' => $this->external->mpAppId(),
            'timeStamp' => strval(time()),
            'nonceStr' => StringHelper::random(32),
            'package' => "prepay_id={$prepay_id}",
            'signType' => 'RSA',
        ];

        $message = $ary['appId'] . "\n" .
            $ary['timeStamp'] . "\n" .
            $ary['nonceStr'] . "\n" .
            $ary['package'] . "\n";

        $model = new PrivateKeySigner(
            $this->external->merchantApiSerialNumber(),
            file_get_contents($this->external->merchantPrivateKey())
        );
        $resp = $model->sign($message);
        $sign = $resp->sign;
        $ary['paySign'] = $sign;
        return $ary;
    }


    /**
     * 获取预支付订单
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPrepayOrder()
    {
        $client = $this->getClient();

        $options = [
            'headers' => ['Accept' => 'application/json'],
            // JSON请求体
            'json' => [
                "time_expire" => date('Y-m-d', $this->data->time_expire) . 'T' . date('H:i:s', $this->data->time_expire) . '+08:00',
                "amount" => [
                    "total" => $this->data->total_amount,
                    "currency" => "CNY",
                ],
                "mchid" => $this->external->merchantId(),
                "description" => $this->data->description,
                "notify_url" => $this->data->notify_url,
                "payer" => [
                    "openid" => $this->data->payer_openid,
                ],
                "out_trade_no" => $this->data->out_trade_no,
                "goods_tag" => $this->data->goods_tag,
                "appid" => $this->external->mpAppId(),
                "attach" => $this->data->attach,
            ],
        ];

        try {
            $resp = $client->request(
                'POST',
                'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi', //请求URL
                $options
            );

            $statusCode = $resp->getStatusCode();
            if ($statusCode == 200) {
                //处理成功
                return $resp->getBody()->getContents();
            } else if ($statusCode == 204) {
                //处理成功，无返回Body
                return true;
            }
        } catch (RequestException $e) {
            throw $e;

//            // 进行错误处理
//            echo $e->getMessage()."\n";
//            if ($e->hasResponse()) {
//                echo "failed,resp code = " . $e->getResponse()->getStatusCode() . " return body = " . $e->getResponse()->getBody() . "\n";
//            }
//            return;
        }

    }

}