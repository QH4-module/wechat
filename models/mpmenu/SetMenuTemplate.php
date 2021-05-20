<?php
/**
 * File Name: SetMenuTemplate.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/26 2:47 下午
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
use qttx\web\Model;

class SetMenuTemplate extends Model
{
    /**
     * @var string 接收参数,必须,模板id
     */
    public $template_id;

    /**
     * @var array 接收参数,必须,模板菜单数据
     * 必须是下面的格式:
     * [
     *  name=>一级名字,
     *  type=>类型,
     *  children=>[
     *      [
     *          name=>二级名字,
     *          type=>二级类型,
     *      ]
     *      ...
     *  ]
     *  ...
     * ]
     */
    public $data;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['data'], 'required'],
            [['data'], 'array'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLangs()
    {
        return [
            'data' => '菜单数据',
        ];
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $db = \QTTX::$app->db;

        $db->beginTrans();

        try {

            $result = WechatMpMenuTemplateActiveRecord::findOne($this->template_id);
            if (empty($result)) {
                $this->addError('template_id', '无效的模板');
                return false;
            }
            // 模板使用状态变为否
            $result->is_used = 0;
            $result->update($db);

            // 删除以前的菜单
            $db->update(WechatMpMenuActiveRecord::tableName())
                ->col('del_time', time())
                ->whereArray(['template_id' => $this->template_id])
                ->where('del_time=0')
                ->query();

            if (!$this->insert($db)) {
                $db->rollBackTrans();
                return false;
            }

            $db->commitTrans();

            return true;

        } catch (\Exception $exception) {
            $db->rollBackTrans();
            \QTTX::$app->log->error($exception);
            return false;
        }
    }

    /**
     * 插入菜单表
     * @param $db
     * @return bool
     */
    protected function insert($db)
    {
        foreach ($this->data as $item) {
            if (!$this->checkMenuItem($item,1)) {
                return false;
            }
            // 插入一级菜单
            $id = \QTTX::$app->snowflake->id();
            $this->_insert($id, '', 1, $item, $db);
            if (isset($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $item2) {
                    if (!$this->checkMenuItem($item2,2)) {
                        return false;
                    }
                    // 插入二级菜单
                    $id2 = \QTTX::$app->snowflake->id();
                    $this->_insert($id2, $id, 2, $item2, $db);
                }
            }
        }
        return true;
    }

    /**
     * 构建插入语句
     * @param $id
     * @param $pid
     * @param $level
     * @param $item
     * @param $db
     */
    protected function _insert($id, $pid, $level, $item, $db)
    {
        $model = new WechatMpMenuActiveRecord();
        $model->id = $id;
        $model->template_id = $this->template_id;
        $model->name = $item['name'];
        $model->type = $item['type'];
        $model->level = $level;
        $model->parent_id = $pid;
        $model->menu_key = '';
        $model->url = '';
        $model->media_id = '';
        $model->appid = '';
        $model->pagepath = '';
        $model->create_time = time();
        $model->create_by = TokenFilter::getPayload('user_id');
        $model->del_time = 0;

        if ($item['type'] != 'children') {
            $model->menu_key = $item['menu_key'];
            if (in_array($item['type'], ['view', 'miniprogram'])) {
                $model->url = $item['url'];
            }
            if ($item['type'] == 'miniprogram') {
                $model->appid = $item['appid'];
                $model->pagepath = $item['pagepath'];
            }
            if (in_array($item['type'], ['media_id', 'view_limited'])) {
                $model->media_id = $item['media_id'];
            }
        }
        $model->insert($db);
    }

    /**
     * 校验一条菜单数据是否有效
     * @param $item
     * @param $level
     * @return bool
     */
    protected function checkMenuItem($item, $level = 1)
    {
        if (empty($item['name'])) {
            $this->addError('data', '菜单名称不能为空');
            return false;
        }
        if (empty($item['type'])) {
            $this->addError('data', '菜单类型必须选择');
            return false;
        }else{
            if ($level == 2 && $item['type'] == 'children') {
                $this->addError('data', '二级菜单不能包含子级');
                return false;
            }
        }
        if ($item['type'] != 'children') {
            if (empty($item['menu_key'])) {
                $this->addError('data', '菜单KEY必须填写');
                return false;
            }
            if (in_array($item['type'], ['view', 'miniprogram'])) {
                if (empty($item['url'])) {
                    $this->addError('data', '连接和小程序类型必须填写URL');
                    return false;
                }
            }
            if ($item['type'] == 'miniprogram') {
                if (empty($item['appid']) || empty($item['pagepath'])) {
                    $this->addError('data', '小程序类型必须填写AppId和页面');
                    return false;
                }
            }
            if (in_array($item['type'], ['media_id', 'view_limited'])) {
                if (empty($item['media_id'])) {
                    $this->addError('data', '素材类型必须填写素材ID');
                    return false;
                }
            }
        }
        return true;
    }
}