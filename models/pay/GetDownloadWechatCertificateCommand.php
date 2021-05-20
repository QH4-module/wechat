<?php
/**
 * File Name: GetDownloadWechatCertificateCommand.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/30 10:52 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\pay;


use qh4module\wechat\external\ExtWechat;
use qttx\basic\Loader;
use qttx\helper\FileHelper;
use qttx\helper\StringHelper;
use qttx\web\ServiceModel;

class GetDownloadWechatCertificateCommand extends ServiceModel
{
    /**
     * @var ExtWechat
     */
    protected $external;

    public function run()
    {
        $apiV3key = $this->external->merchantV3ApiKey();
        $mchId = $this->external->merchantId();
        $mchPrivateKeyFilePath = $this->external->merchantPrivateKey();
        $mchSerialNo = $this->external->merchantApiSerialNumber();
        $outputFilePath = StringHelper::combPath(Loader::getAlias('@runtime'), 'cert');
        FileHelper::mkdir($outputFilePath);
        // 如果第一次下载,是没有这个值的,
        $wechatpayCertificateFilePath = $this->external->wechatpayCertificate();

        // 第一次下载的命令
        $command = "php tool/CertificateDownloader.php -k ${apiV3key} -m ${mchId} -f ${mchPrivateKeyFilePath} -s ${mchSerialNo} -o ${outputFilePath}";

        // 非第一次下载可以用下面的
//        $command = "php tool/CertificateDownloader.php -k ${apiV3key} -m ${mchId} -f ${mchPrivateKeyFilePath} -s ${mchSerialNo} -o ${outputFilePath} -c ${wechatpayCertificateFilePath}";

        return $command;
    }
}