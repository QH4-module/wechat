<?php
/**
 * File Name: ExtWechatMp.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/12 11:47 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\external;


use qh4module\wechat\TraitWechatMpController;

define('WECHAT_MP_MESSAGE_PLAINTEXT', 'plaintext');
define('WECHAT_MP_MESSAGE_COMPATIBLE', 'compatible');
define('WECHAT_MP_MESSAGE_CIPHERTEXT', 'ciphertext');

/**
 * Class ExtWechatMp
 * 微信公众号相关的配置类
 * @package qh4module\wechat\mp
 */
abstract class ExtWechatMp extends ExtWechat
{
    /// 其它公共配置,请看父类

    /**
     * 公众号的原始ID
     * @return string
     */
    abstract public function mpOriginalId();

    /**
     * 微信公众号的基本配置中的 token
     * 必须为英文或数字，长度为3-32字符。
     * @return string
     */
    abstract public function mpToken();

    /**
     * 微信公众号基本配置中的 EncodingAESKey
     * 消息加密密钥由43位字符组成，可随机修改，字符范围为A-Z，a-z，0-9。
     * @return string
     */
    abstract public function mpEncodingAESKey();

    /**
     * 微信公众号消息加密类型
     * 返回 MP_MESSAGE_XXXX 定义,在文件头部
     * plaintext 明文
     * compatible 兼容
     * ciphertext 密文
     * 现在暂时只支持明文类型,其它类型会后续更新
     * @return string
     */
    public function mpMessageMode()
    {
        return WECHAT_MP_MESSAGE_PLAINTEXT;
    }

    /**
     * 公众号的appid
     * 微信公众平台-开发-基本配置 中获取
     * @return string
     */
    abstract public function mpAppId();

    /**
     * 公众号的 AppSecret
     * 微信公众平台-开发-基本配置 中获取
     * @return string
     */
    abstract public function mpAppSecret();

    /**
     * 公众号事件处理
     */
    public function mpEventHandle()
    {
        return new ExtWechatMpEventHandle();
    }

    /**
     * 是否启用服务器跳转
     * 模块提供了2种菜单跳转链接的方式
     * 方式1: 点击菜单后直接跳转指定链接,微信会在链接后追加 code 和 state 参数,state 参数被设置为菜单的id
     *       如果要得到用户信息,需要客户端用code参数调取服务器 `actionMpWebCode2UserId()` 接口
     *       这种方式对前端使用hash型路由,极度不友好,会造成url错乱,需要前端进行特定处理
     * 方式2: 点击菜单后跳转到服务器,服务器负责解析用户信息,然后服务器会再次跳转到实际连接,此时在连接后面直接追加 user_id 参数
     * @return string|null 返回null表示不使用服务器跳转
     *                     如果使用服务器跳转,需要返回一个连接,地址是 actionRedirectUrl() 接口
     * @see TraitWechatMpController::actionRedirectUrl()
     */
    public function mpMenuServerRedirect()
    {
        return null;

        // 例子:
//        return 'http://域名/wechat/redirectUrl';
    }

}