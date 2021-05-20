<?php
/**
 * File Name: TraitWechatPayController.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/13 4:29 下午
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
use qh4module\wechat\external\ExtWechatPayNotify;
use qh4module\wechat\models\pay\GetDownloadWechatCertificateCommand;
use qh4module\wechat\models\pay\MpCreateOrder;
use qh4module\wechat\models\pay\PayNotify;
use qh4module\wechat\models\pay\QueryOrder;
use QTTX;

trait TraitWechatPayController
{
    /**
     * @return ExtWechatMp
     */
    public function ext_wechat_mp()
    {
        // todo 该方法必须被重写
    }

    /**
     * 预留,暂未使用
     */
    public function ext_wechat_mini()
    {

    }

    /**
     * 预留,暂未使用
     */
    public function ext_wechat_h5()
    {

    }

    /**
     * 预留,暂未使用
     */
    public function ext_wechat_app()
    {

    }


    /**
     * 特殊接口,访问该接口返回一条命令,用于下载微信支付平台证书
     * 这个接口调用的文件就是微信扩展包中的 tool/CertificateDownloader.php
     * 但是调用命令太过复杂,就有了这个接口,为了安全,这里被注释了,如果要使用可以解开注释
     * 最终生成的文件在 runtime 目录下
     *
     * 注意: 这个接口的返回值携带了很多密钥,绝对禁止对外使用!!!!
     * 注意: 这个接口的返回值携带了很多密钥,绝对禁止对外使用!!!!
     * 注意: 这个接口的返回值携带了很多密钥,绝对禁止对外使用!!!!
     *
     * @return array|false
     */
//    public function actionGetDownloadWechatCertificateCommand()
//    {
//        if (!ENV_DEV) {
//            QTTX::$response->setStatusCode(404);
//            return false;
//        }
//        $model = new GetDownloadWechatCertificateCommand([
//            'external' => $this->ext_wechat_mp(),
//        ]);
//
//        return $this->runModel($model);
//    }

    /**
     * 发起公众号支付
     * 注意: 这个接口是示例接口,会报错,内部存在伪代码
     *      根据自己的实际业务需求,改写model
     */
    public function actionMpCreateOrder()
    {
        $model = new MpCreateOrder([
            'external' => $this->ext_wechat_mp()
        ]);

        return $this->runModel($model);
    }



    /**
     * 去微信查询订单支付情况
     * 注意 : 该接口也会触发微信支付回调,所以支付回调一定要处理多次请求的情况
     * 注意 : 该接口可以适用于多种支付(公众号,小程序,app),需要改成不同配置类
     * @see ExtWechatPayNotify::handle()
     * @return array
     */
    public function actionQueryOrder()
    {
        $model = new QueryOrder([
            // 注意,这里根据你的支付类型,传入不同的配置
            'external' => $this->ext_wechat_mp(),
        ]);

        return $this->runModel($model);
    }


    /**
     * 微信支付回调
     * 注意 : 该接口可以适用于多种支付(公众号,小程序,app),需要改成不同配置类
     */
    public function actionPayNotify()
    {
        $model = new PayNotify([
            // 这里根据你的支付类型,传入不同配置
            'external' => $this->ext_wechat_mp(),
        ]);
        $model->run();
    }



    // TODO 校验收到的事件消息
}