<?php
/**
 * File Name: TraitWechatMpMenuController.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/13 3:16 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat;


use qh4module\wechat\models\mpmenu\CreateMenuTemplate;
use qh4module\wechat\models\mpmenu\DelMenuTemplate;
use qh4module\wechat\models\mpmenu\MenuTemplateList;
use qh4module\wechat\models\mpmenu\SetMenuTemplate;

/**
 * Trait TraitWechatMpMenuController
 * @package qh4module\wechat
 */
trait TraitWechatMpMenuController
{

    /**
     * 新增微信公众号菜单模板
     * @return array
     */
    public function actionMpCreateMenuTemplate()
    {
        $model = new CreateMenuTemplate();

        return $this->runModel($model);
    }

    /**
     * 获取公众号菜单模板
     * @return array
     */
    public function actionMpMenuTemplateList()
    {
        $model = new MenuTemplateList();

        return $this->runModel($model);
    }

    /**
     * 设置公众号模板菜单数据
     * @return array
     */
    public function actionMpSetMenuTemplate()
    {
        $model = new SetMenuTemplate();

        return $this->runModel($model);
    }

    /**
     * 删除公众号菜单模板
     * @return array
     */
    public function actionMpDelMenuTemplate()
    {
        $model = new DelMenuTemplate();

        return $this->runModel($model);
    }
}