<?php
/**
 * File Name: MenuTemplateList.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/26 11:48 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mpmenu;


use qttx\helper\ArrayHelper;

class MenuTemplateList extends WechatMpMenuTemplateModel
{
    /**
     * @inheritDoc
     */
    public function run()
    {
        $result_temp = WechatMpMenuTemplateActiveRecord::find()
            ->select(['id', 'name', 'is_used'])
            ->where('del_time=0')
            ->asArray()
            ->all();

        foreach ($result_temp as &$temp) {
            $result = WechatMpMenuActiveRecord::find()
                ->select('*')
                ->whereArray(['template_id' => $temp['id']])
                ->where('del_time=0')
                ->asArray()
                ->all();
            $temp['menu'] = ArrayHelper::formatTree($result,'');
        }

        return $result_temp;
    }
}