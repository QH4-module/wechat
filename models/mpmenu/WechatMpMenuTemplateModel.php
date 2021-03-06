<?php
/**
 * File Name: WechatMpMenuTemplateModel.php
 * Automatically generated by QGC tool
 * @date: 2021-04-26 11:39:14
 * @version: 4.0.4
 */

namespace qh4module\wechat\models\mpmenu;


use qttx\web\Model;

/**
 * Class WechatMpMenuTemplateModel
 * @package qh4module\wechat\models\mp
 * @description 数据表tbl_wechat_mp_menu_template的Validate模型
 */
class WechatMpMenuTemplateModel extends Model
{

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
			[['id'], 'string', ['max' => 64]],
			[['name'], 'string', ['max' => 100]], 
			[['is_used', 'create_time', 'del_time'], 'integer']
		];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLangs()
    {
        return [
			'id' => 'ID', 
			'name' => '名称', 
			'is_used' => '使用中'
		];
    }
}
