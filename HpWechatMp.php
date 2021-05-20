<?php
/**
 * File Name: HpWechatMp.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/13 9:15 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat;

use qh4module\wechat\external\ExtWechat;
use qh4module\wechat\models\mp\AccessToken;

/**
 * Class HpWechatMp
 * 微信公众号相关方法
 * @package qh4module\wechat
 */
class HpWechatMp
{
    /**
     * 获取微信公众号调用接口用的 access_token
     * @param ExtWechat $external
     * @return false|mixed|string
     */
    public static function getMpAccessToken(ExtWechat $external)
    {
        return AccessToken::get($external);
    }
}