<?php
/**
 * File Name: DelMenuTemplate.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/26 3:27 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mpmenu;


use qttx\web\Model;

class DelMenuTemplate extends Model
{
    /**
     * @var string 接收参数,模板id
     */
    public $template_id;

    public function rules()
    {
        return [
            [['template_id'], 'required']
        ];
    }

    public function run()
    {
        $db = \QTTX::$app->db;

        $db->beginTrans();

        try {

            $model = WechatMpMenuTemplateActiveRecord::findOne($this->template_id);
            if (empty($model)) {
                $this->addError('template_id', '模板无效');
                return false;
            }
            $model->del_time = time();
            $model->update($db);

            $db->update(WechatMpMenuActiveRecord::tableName())
                ->col('del_time', time())
                ->whereArray(['template_id' => $this->template_id])
                ->where('del_time=0')
                ->query();

            $db->commitTrans();

            return true;

        } catch (\Exception $exception) {
            $db->rollBackTrans();
            \QTTX::$app->log->error($exception);
            return false;
        }
    }
}