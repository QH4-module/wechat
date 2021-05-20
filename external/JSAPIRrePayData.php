<?php
/**
 * File Name: JSAPIRrePayData.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/30 9:44 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\external;

use qh4module\wechat\TraitWechatPayController;

/**
 * Class JSAPIRrePayData
 * 微信公众号预支付用的数据类
 * @package qh4module\wechat\external
 */
class JSAPIRrePayData
{
    /**
     * @var string 用户在当前应用下的openid
     */
    public $payer_openid;

    /**
     * @var string 自己生成的订单号,只能是数字、大小写字母_-*且在同一个商户号下唯一
     */
    public $out_trade_no;

    /**
     * @var int 支付金额,单位分
     */
    public $total_amount;

    /**
     * @var string 通知URL必须为直接可访问的URL，不允许携带查询串，要求必须为https地址。
     * @see TraitWechatPayController::actionPayNotify() 支付回调接口
     */
    public $notify_url;

    /**
     * @var int 订单失效时间,时间戳
     */
    public $time_expire;

    /**
     * @var string 商品描述
     */
    public $description;

    /**
     * @var string 附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用,可以不填
     */
    public $attach = '';

    /**
     * @var string 订单优惠标记,可以不填
     */
    public $goods_tag = '';

}