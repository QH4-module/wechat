<?php
/**
 * File Name: ExtWechatPayNofity.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/4 10:20 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\external;


use qttx\web\External;

class ExtWechatPayNotify extends External
{
    /**
     * 支付成功的处理
     * @param array $data 订单的相关信息
     *  Array(
     *      [mchid] => 1608712345
     *      [appid] => wx89ddd4ca15ed1234
     *      [out_trade_no] => 177171582194683904
     *      [transaction_id] => 4200001009202105031916189568
     *      [trade_type] => JSAPI
     *      [trade_state] => SUCCESS
     *      [trade_state_desc] => 支付成功
     *      [bank_type] => OTHERS
     *      [attach] =>
     *      [success_time] => 2021-05-03T21:36:41+08:00
     *      [payer] => Array
     *      (
     *          [openid] => okwsJ638KcnwwBL79Q1Z1jXYTLUo
     *      )
     *
     *      [amount] => Array
     *      (
     *          [total] => 1
     *          [payer_total] => 1
     *          [currency] => CNY
     *          [payer_currency] => CNY
     *      )
     * )
     * @return bool 函数返回值决定了返回给微信的状态
     *              true  将返回给微信200, 微信不会再重复通知
     *              false 将返回给微信500,微信会通过一定的策略定期重新发起通知
     *              具体重试策略,参见微信支付文档
     *
     * 注意: 这个函数可能被多次调用,所以函数中一定要正确处理重复调用
     *      推荐的做法是，当商户系统收到通知进行处理时，先检查对应业务数据的状态，并判断该通知是否已经处理。如果未处理，则再进行处理；如果已处理，则直接返回结果成功。
     *      在对业务数据进行状态检查和处理之前，要采用数据锁进行并发控制，以避免函数重入造成的数据混乱
     */
    public function handle($data)
    {
        if (isset($data['trade_state']) && $data['trade_state'] == 'SUCCESS') {
            // 支付成功处理
        }else{
            // 支付失败处理
        }

        return true;
    }

}