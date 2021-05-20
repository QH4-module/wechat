<?php
/**
 * File Name: CreateQrcode.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/13 9:13 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mp;


use qh4module\wechat\external\ExtWechatMp;
use qh4module\wechat\models\Request;
use qttx\web\ServiceModel;

/**
 * Class CreateQrcode
 * @package qh4module\wechat\models\mp
 * @property ExtWechatMp $external
 */
class CreateQrcode extends ServiceModel
{
    /**
     * @var string 二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值
     */
    public $action_name;

    /**
     * @var int 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     */
    public $scene_id;

    /**
     * @var string 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
     */
    public $scene_str;

    /**
     * @var int 该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
     */
    public $expire_seconds;


    /**
     * @var string[] 二维码类型的有效值
     */
    protected $action_name_limit = ['QR_SCENE', 'QR_STR_SCENE', 'QR_LIMIT_SCENE', 'QR_LIMIT_STR_SCENE'];

    /**
     * @inheritDoc
     */
    public function rules()
    {
        $required = ['action_name'];
        $scene_id_row = [['scene_id'], 'integer'];

        if ($this->action_name == 'QR_SCENE') {
            $required[] = 'expire_seconds';
            $required[] = 'scene_id';
        }else if ($this->action_name == 'QR_STR_SCENE') {
            $required[] = 'expire_seconds';
            $required[] = 'scene_str';
        }else if ($this->action_name == 'QR_LIMIT_SCENE') {
            $required[] = 'scene_id';
            $scene_id_row['max'] = 100000;
        }else if ($this->action_name == 'QR_LIMIT_STR_SCENE') {
            $required[] = 'scene_str';
            $scene_id_row['max'] = 100000;
        }
        return [
            [$required, 'required'],
            [['action_name'], 'in', 'range' => $this->action_name_limit],
            $scene_id_row,
            [['scene_str'], 'string', 'min' => 1, 'max' => 64],
            [['expire_seconds'], 'integer', 'max' => 2592000],
        ];
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $post = [
            'action_name'=>$this->action_name,
            'action_info'=>[
                'scene'=>[]
            ]
        ];
        if ($this->action_name == 'QR_SCENE') {
            $post['expire_seconds'] = $this->expire_seconds;
            $post['action_info']['scene']['scene_id'] = $this->scene_id;
        }else if ($this->action_name == 'QR_STR_SCENE') {
            $post['expire_seconds'] = $this->expire_seconds;
            $post['action_info']['scene']['scene_str'] = $this->scene_str;
        }else if ($this->action_name == 'QR_LIMIT_SCENE') {
            $post['action_info']['scene']['scene_id'] = $this->scene_id;
        }else if ($this->action_name == 'QR_LIMIT_STR_SCENE') {
            $post['action_info']['scene']['scene_str'] = $this->scene_str;
        }

        $access_token = AccessToken::get($this->external);
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
        $json = Request::post_json($url, json_encode($post));

        $resp =  json_decode($json, true);
        if (isset($resp['ticket']) && $resp['ticket']) {
            $resp['image'] = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($resp['ticket']);
            return $resp;
        }else{
            $this->addError('action_name', '二维码生成失败：' . $json);
            return false;
        }
    }
}