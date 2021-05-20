<?php
/**
 * File Name: QueryOrder.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/4 9:59 上午
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
use qh4module\wechat\external\ExtWechat;
use qttx\web\ServiceModel;

class QueryOrder extends ServiceModel
{
    /**
     * @var string 接收参数,必须,订单的id
     */
    public $order_id;

    /**
     * @var int 接收参数,要查询的是微信订单号还是自定义的订单号
     * 1 自定义订单号  2微信订单号
     */
    public $type = 1;

    /**
     * @var ExtWechat
     */
    protected $external;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLangs()
    {
        return [
            'order_id' => '订单id',
        ];
    }


    public function run()
    {
        if (empty($this->getOrder())) {
            $this->addError('order_id', '无效的订单号');
            return false;
        }


        $model_pay = new WechatPay(null, [
            'external' => $this->external,
        ]);
        $client = $model_pay->getClient();

        $mch_id = $this->external->merchantId();

        if ($this->type == 1) {
            $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/{$this->order_id}?mchid={$mch_id}";
        } else {
            $url = "https://api.mch.weixin.qq.com/v3/pay/transactions/id/{$this->order_id}?mchid={$mch_id}";
        }

        try {
            $resp = $client->request(
                'GET',
                $url, //请求URL
                [
                    'headers' => ['Accept' => 'application/json']
                ]
            );
            $statusCode = $resp->getStatusCode();
            if ($statusCode == 200) {
                //处理成功
                PayNotify::handlePayFinish($resp->getBody()->getContents(), $this->external->payNotify());
                return $this->getOrder();
            } else {
                \QTTX::$app->log->warning("查询微信订单失败：\r\n URL：{$url}\r\n返回值：{$resp}");
                $this->addError('order_id', '订单查询失败');
                return false;
            }
        } catch (RequestException $exception) {
            \QTTX::$app->log->error("微信订单查询失败：" . PHP_EOL
                . $exception->getMessage() . PHP_EOL
                . "failed,resp code = " . $exception->getResponse()->getStatusCode() . " return body = " . $exception->getResponse()->getBody() . PHP_EOL
            );
            throw $exception;
        }
    }

    protected function getOrder()
    {
        // 查询订单
        $sql = $this->external->getDb()
            ->select('*')
            ->from($this->external->orderTableName());
        if ($this->type == 1) {
            $field = $this->external->orderTableOrderNoField();
            $sql->whereArray([
                $field => $this->order_id,
            ]);
        } else {
            $field = $this->external->orderTableWechatOrderNoField();
            $sql->whereArray([
                $field => $this->order_id
            ]);
        }
        return $sql->row();
    }
}