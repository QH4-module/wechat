<?php
/**
 * File Name: WechatMpMenuActiveRecord.php
 * Automatically generated by QGC tool
 * @date: 2021-04-26 14:57:57
 * @version: 4.0.4
 */

namespace qh4module\wechat\models\mpmenu;


use qttx\web\ActiveRecord;

/**
 * Class WechatMpMenuActiveRecord
 * @package qh4module\wechat\models\mp
 * @description 数据表tbl_project的ActiveRecord模型
 * @property string $id 
 * @property string $template_id 所属模板
 * @property string $name 菜单名称
 * @property string $type 菜单类型
 * @property int $level 菜单等级,只能是1和2
 * @property string $parent_id 上级菜单
 * @property string $menu_key 菜单key
 * @property string|null $url 网页链接
 * @property string|null $media_id 
 * @property string|null $appid 小程序appid
 * @property string|null $pagepath 小程序页面路径
 * @property int $create_time 
 * @property int $del_time
 */
class WechatMpMenuActiveRecord extends ActiveRecord
{
    /**
     * @var string 数据表名
     */
    protected static $table_name = "{{%wechat_mp_menu}}";

    /**
     * @var string 数据表别名
     */
    protected static $table_alias = "ta";

    /**
     * 返回数据表的主键
     * @return string
     */
    static function primaryKey()
    {
        return "id";
    }
}