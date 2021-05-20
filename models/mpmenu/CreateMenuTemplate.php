<?php
/**
 * File Name: CreateMenuTemplate.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/26 11:36 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mpmenu;


use qh4module\token\TokenFilter;
use qttx\helper\ArrayHelper;
use qttx\web\Model;

class CreateMenuTemplate extends WechatMpMenuTemplateModel
{
    /**
     * @var string 接收参数,必须
     */
    public $name;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return ArrayHelper::merge([
            [['name'], 'required'],
        ], parent::rules());
    }


    public function run()
    {
        $result = WechatMpMenuTemplateActiveRecord::find()
            ->select(['id'])
            ->whereArray(['name' => $this->name])
            ->where('del_time=0')
            ->asArray()
            ->one();
        if (!empty($result)) {
            $this->addError('name', '名字重复');
            return false;
        }

        $model = new WechatMpMenuTemplateActiveRecord();
        $model->id = \QTTX::$app->snowflake->id();
        $model->is_used = 0;
        $model->name = $this->name;
        $model->create_time = time();
        $model->del_time = 0;
        $model->insert();

        return true;
    }
}