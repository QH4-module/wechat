<?php
/**
 * File Name: WechatMpMenuModel.php
 * Automatically generated by QGC tool
 * @date: 2021-04-26 14:59:18
 * @version: 4.0.4
 */

namespace qh4module\wechat\models\mpmenu;


use qttx\web\Model;

/**
 * Class WechatMpMenuModel
 * @package qh4module\wechat\models\mp
 * @description 数据表tbl_wechat_mp_menu的Validate模型
 */
class WechatMpMenuModel extends Model
{

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
			[['id', 'template_id', 'parent_id', 'appid'], 'string', ['max' => 64]],
			[['name'], 'string', ['max' => 60]], 
			[['type'], 'string', ['max' => 20]], 
			[['level'], 'in', ['range' => [1, 2]]], 
			[['menu_key'], 'string', ['max' => 128]], 
			[['url'], 'url', ['needSchemes' => true, 'validSchemes' => ['http', 'https']]], 
			[['media_id'], 'string', ['max' => 200]], 
			[['pagepath'], 'string', ['max' => 2000]], 
			[['create_time', 'del_time'], 'integer']
		];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLangs()
    {
        return [
			'id' => 'ID', 
			'template_id' => '所属模板', 
			'name' => '菜单名称', 
			'type' => '菜单类型', 
			'level' => '菜单等级', 
			'parent_id' => '上级菜单', 
			'menu_key' => '菜单key', 
			'url' => '网页链接', 
			'media_id' => '素材ID', 
			'appid' => '小程序appid', 
			'pagepath' => '小程序页面路径'
		];
    }
}
