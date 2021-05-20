<?php
/**
 * File Name: AccessToken.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/25 12:22 上午
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
use qh4module\wechat\models\Request;
use QTTX;

/**
 * Class AccessToken
 * @package qh4module\wechat\models
 * 维护请求微信接口用的 access_token
 * 注意: 使用该类必须启用redis, 因为类中依赖于redis处理并发冲突.
 *      这个类只适合一般并发场景.如果业务存在非常高并发的需求,请使用 中控服务器 来解决
 */
class AccessToken
{
    public static function get(ExtWechat $external)
    {
        $result = QTTX::$app->redis->get(self::getRedisKey());
        if (empty($result)) {
            return self::getAccessToken($external);
        }
        $ary = explode(';', $result);
        // token
        $token = $ary[0];
        // 到期时间
        $time = isset($ary[1]) ? $ary[1] : 0;
        // 还有10分钟到期,则申请新的token
        if (time() + 600 > $time) {
            $new_token = self::getAccessToken($external);
            if ($new_token) {
                return $new_token;
            }
        }

        return $token;
    }

    /**
     * 从微信获取新的token
     * @param ExtWechat $external
     * @return false|mixed
     */
    protected static function getAccessToken(ExtWechat $external)
    {
        // 检查互斥锁
        $lock_key = self::getRedisKey(2);
        $lock = QTTX::$app->redis->incr($lock_key);
        if ($lock > 1) {
            return false;
        }
        // 设置互斥锁超时
        QTTX::$app->redis->expire($lock_key, 5);

        // 从微信获取token
        $app_id = $external->mpAppId();
        $app_secret = $external->mpAppSecret();
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
        $result = Request::get($url);
        $ary = json_decode($result, true);
        if (isset($ary['access_token'])) {
            // 新的token
            $token = $ary['access_token'];
            // 到期时间
            $time = time() + $ary['expires_in'];
            // 保存到redis
            QTTX::$app->redis->set(self::getRedisKey(), $token . ';' . $time);
            return $token;
        }else{
            QTTX::$app->log->warning("获取微信AccessToken失败：" . $result);
        }
        return false;
    }


    /**
     * 获取redis中相关的key
     * @param int $type
     *          1 获取保存 access_token 的key
     *          2 获取冲突标记key
     * @return string
     */
    protected static function getRedisKey($type = 1)
    {
        if ($type == 2) {
            return QTTX::getConfig('app_name') . '_wechat_access_token_lock';
        } else {
            return QTTX::getConfig('app_name') . '_wechat_access_token';
        }
    }
}