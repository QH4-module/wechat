<?php
/**
 * File Name: SetTemplateToWechat.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/26 3:51 下午
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
use qh4module\wechat\external\ExtWechatMp;
use qh4module\wechat\models\Request;
use qttx\web\ServiceModel;

/**
 * Class SetTemplateToWechat
 * @package qh4module\wechat\models\mp
 * @property ExtWechatMp $external
 */
class SetTemplateToWechat extends ServiceModel
{
    /**
     * @var string 接收参数,模板id
     */
    public $template_id;


    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['template_id'], 'required']
        ];
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $result = $this->external->getDb()
            ->select('*')
            ->from('{{%wechat_mp_menu}}')
            ->whereArray(['template_id' => $this->template_id])
            ->where('del_time=0')
            ->query();
        if (empty($result)) {
            $this->addError('template_id', '模板无效');
            return false;
        }

        // 格式化结果,剔除无用字段
        $menu = ['button' => []];
        foreach ($result as $item) {
            if ($item['level'] == 1) {
                $level1_menu = $this->format($item);
                if ($item['type'] == 'children') {
                    foreach ($result as $item2) {
                        if ($item2['level'] == 2 && $item2['parent_id'] == $item['id']) {
                            $level1_menu['sub_button'][] = $this->format($item2);
                        }
                    }
                }
                $menu['button'][] = $level1_menu;
            }
        }

        // 开始设置微信
        $token = AccessToken::get($this->external);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";
        $result = Request::post_json($url, json_encode($menu, JSON_UNESCAPED_UNICODE));
        $ary = json_decode($result, true);
        if (isset($ary['errmsg']) && $ary['errmsg'] == 'ok') {

            // 设置模板为使用中
            $this->external->getDb()
                ->update('{{%wechat_mp_menu}}')
                ->col('is_used', 1)
                ->whereArray(['id' => $this->template_id])
                ->query();

            return true;

        } else {
            $this->addError('menu', $result);
            return false;
        }
    }


    protected function format($item)
    {
        $ary = [
            'name' => $item['name'],
        ];
        if ($item['type'] == 'children') {
            $ary['sub_button'] = [];
            return $ary;
        }
        $ary['type'] = $item['type'];
        $ary['key'] = $item['menu_key'];
        if ($item['type'] == 'view') {
            $app_id = $this->external->mpAppId();
            $server_url = $this->external->mpMenuServerRedirect();
            if (empty($server_url)) {
                $redirect_uri = urlencode($item['url']);
                $state = $item['id'];
                $ary['url'] = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
            }else{
                $redirect_uri = urlencode('https://ssl.test.hyusu.xyz/wechat/redirectUrl');
                // 重定向后会带上state参数
                $state = urlencode($item['url']);
                $ary['url'] = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
            }
        }
        if ($item['type'] == 'miniprogram') {
            $ary['url'] = $item['url'];
            $ary['appid'] = $item['appid'];
            $ary['pagepath'] = $item['pagepath'];
        }
        if (in_array($item['type'], ['media_id', 'view_limited'])) {
            $ary['media_id'] = $item['media_id'];
        }
        return $ary;
    }
}