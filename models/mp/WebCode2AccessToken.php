<?php
/**
 * File Name: WebCode2AccessToken.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/25 5:01 下午
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
use qttx\web\ServiceModel;

/**
 * Class WebCode2AccessToken
 * @package qh4module\wechat\models\mp
 * @property ExtWechat $external
 */
class WebCode2AccessToken extends ServiceModel
{
    /**
     * @var string 接收参数,必须,微信给的code
     */
    public $code;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $url = self::getUrl($this->code, $this->external);
        $result = Request::get($url);
        $ary = json_decode($result, true);
        if ((isset($ary['errcode']) && $ary['errcode'] > 0) || !isset($ary['openid'])) {
            $this->addError('code', $result);
            return false;
        }
        return $ary;
    }

    public static function getUrl($code, ExtWechat $external)
    {
        $app_id = $external->mpAppId();
        $app_secret = $external->mpAppSecret();
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
        return sprintf($url, $app_id, $app_secret, $code);
    }
}