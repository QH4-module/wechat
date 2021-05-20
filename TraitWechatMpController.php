<?php
/**
 * File Name: TraitWechatMpController.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/12 1:41 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat;


use qh4module\wechat\external\ExtWechatMp;
use qh4module\wechat\models\mp\Access;
use qh4module\wechat\models\mp\CreateQrcode;
use qh4module\wechat\models\mp\EventHandle;
use qh4module\wechat\models\mp\RedirectUrl;
use qh4module\wechat\models\mp\SetTemplateToWechat;
use qh4module\wechat\models\mp\WebCode2AccessToken;
use qh4module\wechat\models\mp\WebCode2UserId;

trait TraitWechatMpController
{
    /**
     * @return ExtWechatMp
     */
    public function ext_wechat()
    {
        // todo 该方法必须被重写
    }

    /**
     * 微信公众号配置服务器验证
     * 服务器配置的服务器地址应该填写  http://域名/控制器/access
     * 例如: http://wwww.qttx.com/wechat/access
     * 注意,这个函数在配置服务器完成后需要注释掉,然后将另一个同名api取消注释
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html
     */
//    public function actionAccess()
//    {
//        $model = new Access([
//            'external'=>$this->ext_wechat()
//        ]);
//
//        $model->run();
//    }

    /**
     * 微信公众号事件回调方法
     * 微信公众号配置完服务器后,用户触发的事件会回调设置的服务器地址
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html
     * 由于配置服务器验证和回调是同一个地址,所以有了两个同名方法,配置完服务器后,请将此方法取消注释
     */
    public function actionAccess()
    {
        $model = new EventHandle([
            'external' => $this->ext_wechat(),
        ]);

        $model->run();
    }


    /**
     * 生成带场景的二维码
     * 如果有批量生成的需求,逻辑完全一致,可以自己写一个循环脚本
     * @see https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
     */
    public function actionCreateQrcode()
    {
        $model = new CreateQrcode([
            'external' => $this->ext_wechat(),
        ]);

        return $this->runModel($model);
    }


    /**
     * 微信公众号网页通过 code 获取 access_token
     *
     * 注意: 该方法会将微信返回的所有信息返回,包括 access_token 和 open_id
     *      其中有些信息安全级别很高,并不应该传到客户端
     *      尽量不要使用该方法,或者应该修改返回值.
     * @return array
     * @see actionMpWebCode2UserId() 推荐使用该接口
     */
    public function actionMpWebCode2AccessToken()
    {
        $model = new WebCode2AccessToken([
            'external' => $this->ext_wechat(),
        ]);

        return $this->runModel($model);
    }

    /**
     * 微信公众号网页通过 code 获取 用户id
     * 该方法除了去微信换取access_token,还处理了用户部分,返回到客户端的是用户的id
     * 比 [actionMpWebCode2AccessToken] 接口更安全
     * @return array 用户id
     */
    public function actionMpWebCode2UserId()
    {
        $model = new WebCode2UserId([
            'external' => $this->ext_wechat(),
        ]);

        return $this->runModel($model);
    }


    /**
     * 通过模板设置公众号菜单
     * @return array
     */
    public function actionSetTemplateToWechat()
    {
        $model = new SetTemplateToWechat([
            'external' => $this->ext_wechat(),
        ]);

        return $this->runModel($model);
    }


    /**
     * 菜单访问重定向
     * 如果开启了服务器中转模式则会使用该方法中转
     * @see ExtWechatMp::mpMenuServerRedirect() 菜单跳转模式说明
     */
    public function actionRedirectUrl()
    {
        $model = new RedirectUrl([
            'external' => $this->ext_wechat(),
        ]);

        $url = $model->run();

        header('location:'.$url);
    }


}