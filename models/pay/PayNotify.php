<?php
/**
 * File Name: PayNotify.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/3 7:55 下午
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
use qh4module\wechat\external\ExtWechatPayNotify;
use QTTX;
use qttx\basic\Loader;
use qttx\helper\StringHelper;
use qttx\web\ServiceModel;
use WechatPay\GuzzleMiddleware\Auth\CertificateVerifier;
use WechatPay\GuzzleMiddleware\Util\AesUtil;

class PayNotify extends ServiceModel
{
    /**
     * @var string 头部获取,验签用,时间戳
     */
    protected $wechatpay_timestamp;

    /**
     * @var string 头部获取,验签用,随机数
     */
    protected $wechatpay_nonce;

    /**
     * @var string 头部获取,验签用,微信公钥的序列号
     */
    protected $wechatpay_serial;

    /**
     * @var string 头部获取,签名,需要验证该参数
     */
    protected $wechatpay_signature;

    /**
     * @var string $_POST ['resource'] 中获取,解析数据用
     */
    protected $associated_data;

    /**
     * @var string $_POST ['resource'] 中获取,解析数据用
     */
    protected $nonce;

    /**
     * @var string 请求体
     */
    protected $body;

    /**
     * @var string $_POST ['resource'] 中获取,密文
     */
    protected $ciphertext;


    /**
     * @var ExtWechat
     */
    protected $external;

    /**
     * 初始化所有的相关参数
     * @return bool
     */
    protected function getParam()
    {
        $header = QTTX::$request->getHeaders();
        $this->wechatpay_timestamp = $header->get('wechatpay-timestamp');
        $this->wechatpay_signature = $header->get('wechatpay-signature');
        $this->wechatpay_nonce = $header->get('wechatpay-nonce');
        $this->wechatpay_serial = $header->get('wechatpay-serial');
        if (empty($this->wechatpay_timestamp) || empty($this->wechatpay_signature) || empty($this->wechatpay_nonce) || empty($this->wechatpay_serial)) {
            return false;
        }

        $resource = QTTX::$request->post('resource');
        if (empty($resource)) return false;
        if (!isset($resource['associated_data']) || !isset($resource['nonce']) || !isset($resource['ciphertext'])) {
            return false;
        }
        $this->associated_data = $resource['associated_data'];
        $this->nonce = $resource['nonce'];
        $this->ciphertext = $resource['ciphertext'];
        if (empty($this->associated_data) || empty($this->nonce) || empty($this->ciphertext)) {
            return false;
        }

        $this->body = QTTX::$request->getRawBody();
        if (empty($this->body)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        if (!$this->getParam()) {
            QTTX::$response->setStatusCode(400);
            return false;
        }

        $message = $this->wechatpay_timestamp . "\n" .
            $this->wechatpay_nonce . "\n" .
            $this->body . "\n";

        // 验证签名
        $wechat_key = file_get_contents($this->external->wechatpayCertificate());
        $verifier = new CertificateVerifier([$wechat_key]);
        $resv = $verifier->verify($this->wechatpay_serial, $message, $this->wechatpay_signature);

        if ($resv == 1) {
            $aes_util = new AesUtil($this->external->merchantV3ApiKey());
            $json = $aes_util->decryptToString($this->associated_data, $this->nonce, $this->ciphertext);
            if (self::handlePayFinish($json, $this->external->payNotify())) {
                QTTX::$response->setStatusCode(204);
                return true;
            }
        } else {
            $this->writeFile('签名验证失败');
        }
        QTTX::$response->setStatusCode(500);
        return false;
    }

    /**
     * 支付完成的处理
     * @param string $response 微信返回的结果,字符串
     * @param ExtWechatPayNotify $notify
     * @return bool
     */
    public static function handlePayFinish($response,ExtWechatPayNotify $notify)
    {
        /*
                 * 返回格式样例:
                    Array(
                        [mchid] => 1608710427
                        [appid] => wx89ddd4ca15ed6645
                        [out_trade_no] => 177171582194683904
                        [transaction_id] => 4200001009202105031916189568
                        [trade_type] => JSAPI
                        [trade_state] => SUCCESS
                        [trade_state_desc] => 支付成功
                        [bank_type] => OTHERS
                        [attach] =>
                        [success_time] => 2021-05-03T21:36:41+08:00
                        [payer] => Array
                            (
                                [openid] => okwsJ638KcnwwBL79Q1ZTjXYTLUo
                            )

                        [amount] => Array
                            (
                                [total] => 1
                                [payer_total] => 1
                                [currency] => CNY
                                [payer_currency] => CNY
                            )

                    )
                 */
        try {
            $obj = json_decode($response, true);
            if (isset($obj['trade_state']) && isset($obj['out_trade_no'])) {
                return $notify->handle($obj);
            }
            return false;
        } catch (\Exception $exception) {
            QTTX::$app->log->error($exception);
            return false;
        }
    }

    /**
     * 作为测试函数,如果校验失败,将数据保存下来
     * @param $name
     * @param array $msg
     */
    protected function writeFile($name, $msg = [])
    {
//        $file = StringHelper::combPath(Loader::getAlias('@runtime'), 'wechat_notify.txt');
//        file_put_contents("$name: " . date('Y-m-d H:i:s'), $file, FILE_APPEND);
//        file_put_contents("\r\n", $file, FILE_APPEND);
//        file_put_contents(print_r(QTTX::$request->getRawBody(), true), $file, FILE_APPEND);
//        file_put_contents("\r\n", $file, FILE_APPEND);
//        file_put_contents(print_r(QTTX::$request->getHeaders(), true), $file, FILE_APPEND);
//        file_put_contents("\r\n", $file, FILE_APPEND);
//        foreach ($msg as $item) {
//            file_put_contents(print_r($item, true), $file, FILE_APPEND);
//            file_put_contents("\r\n", $file, FILE_APPEND);
//        }
//        file_put_contents("\r\n", $file, FILE_APPEND);
    }
}