<?php
/**
 * File Name: CreateOrder.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/13 4:49 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\pay;


use GuzzleHttp\Exception\GuzzleException;
use qh4module\wechat\external\ExtWechatMp;
use qh4module\wechat\external\JSAPIRrePayData;
use qttx\web\ServiceModel;

/**
 * Class MpCreateOrder
 * 这个类并不能使用, 是一个示例类
 * @package qh4module\wechat\models\pay
 * @property ExtWechatMp $external
 */
class MpCreateOrder extends ServiceModel
{
    /**
     * @var int 金额,示例参数,具体接收参数根据实际业务定义
     */
    public $price;


    /**
     * @inheritDoc
     */
    public function rules()
    {
        // 根据实际业务填写
        return [];
    }

    /**
     * @inheritDoc
     */
    public function attributeLangs()
    {
        // 根据实际业务填写
        return [];
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        ///  实际根据需要开启事务,下面的代码可以经过修改后用在实际业务中


        // 插入订单
        $this->insertOrder();

        $data = $this->getPrepayData();

        $model = new WechatPayMp($data, [
            //  这里的语法是错误的,不能实例化抽象类,改成你自己的类
//            'external' => new ExtWechatMp()
        ]);

        try {
            $json = $model->getPrepayOrder();
            $obj = json_decode($json, true);
            if (isset($obj['prepay_id'])) {
                $prepay_id = $obj['prepay_id'];
                // 构建返回给前端的数据
                $client_data = $model->generateMpJsApiPayData($prepay_id);

                // 将拉起支付用的数据返回给前端
                return [
                    'client_data' => $client_data,
                    // 为了前端支付后查询订单,将订单id也返回
                    'order_id' => '系统生成的订单id'
                ];

            } else {
                \QTTX::$app->log->error('微信预支付订单返回无效:' . $json);
                $this->addError('price', '生成订单失败');
            }

        } catch (GuzzleException $exception) {
//            $file = StringHelper::combPath(Loader::getAlias('@runtime'), 'wechat_pay_error.log');
//            $str = date('Y-m-d H:i:s') . PHP_EOL;
//            $str .= '微信预支付失败：';
//            $str .= $exception->getMessage();
//            if ($exception->hasResponse()) {
//                $str .= "failed,resp code = " . $exception->getResponse()->getStatusCode() . " return body = " . $exception->getResponse()->getBody() . PHP_EOL;
//            }
//            $str .= PHP_EOL;
//            file_put_contents($file, $str, FILE_APPEND);
//            return false;
            throw $exception;
        }

    }

    /**
     * 获取预支付数据
     * @return JSAPIRrePayData
     */
    protected function getPrepayData()
    {
        $model = new JSAPIRrePayData();

        // 下面的参数根据实际业务修改
        $result = $this->external->getDb()
            ->select(['wechat_openid'])
            ->from('tbl_user')
            ->whereArray(['id' => '用户的id'])
            ->row();

        $model->payer_openid = $result['wechat_openid'];
        $model->out_trade_no = '系统生成的订单号';
        $model->total_amount = '金额,单位分';
        $model->notify_url = '支付回调地址';
        $model->time_expire = '订单超时时间';
        $model->description = '订单描述';
        return $model;
    }

    protected function insertOrder()
    {
        // 插入订单信息

        // 并将订单的信息存到一个私有变量中,用于 getPrepayData() 函数使用
    }
}