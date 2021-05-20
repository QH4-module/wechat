<?php
/**
 * File Name: MpWebCode2UserId.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/25 5:33 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mp;

/**
 * Class WebCode2UserId
 * @package qh4module\wechat\models\mp
 */
class WebCode2UserId extends WebCode2AccessToken
{
    /**
     * @inheritDoc
     */
    public function run()
    {
        $result = parent::run();
        if (!$result) return '';

        $openid = $result['openid'];

        $result_user = $this->external->getDb()
            ->select(['id'])
            ->from($this->external->userTableName())
            ->whereArray(['wechat_openid' => $openid])
            ->row();
        if (empty($result_user)) {
            return '';
        }else{
            return $result_user['id'];
        }
    }
}