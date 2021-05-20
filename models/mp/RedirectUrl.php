<?php
/**
 * File Name: RedirectUrl.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/2 6:30 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mp;


use qttx\helper\StringHelper;
use qttx\web\Model;

class RedirectUrl extends WebCode2UserId
{
    /**
     * @var string 重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
     * 在服务器中转模式中,该参数被占用,表示实际跳转地址
     */
    public $state;

    public function run()
    {
        if (!$this->code) {
            echo '没有code';
            exit;
        }

        if (!$this->state) {
            echo '没有state';
            exit;
        }

        $user_id = parent::run();

        if (empty($user_id)) {
            echo '无效的链接';
            exit;
        }

        $url = urldecode($this->state);

        if (stristr($url, '?')) {
            return "{$url}&user_id={$user_id}";
        }else{
            return "{$url}?user_id={$user_id}";
        }

    }
}