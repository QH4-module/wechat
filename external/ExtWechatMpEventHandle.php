<?php
/**
 * File Name: ExtWechatMpEventHandle.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/4 3:59 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\external;


use qh4module\wechat\models\mp\XMLMp;
use qttx\basic\Loader;
use qttx\helper\StringHelper;
use qttx\web\External;

/**
 * Class ExtWechatMpEventHandle
 * 微信公众号触发的各种回调处理
 * 主要在这个类中执行各种业务逻辑
 * @package qh4module\wechat\external
 */
class ExtWechatMpEventHandle extends External
{

    /**
     * 收到xml数据后的处理
     * 这个函数是在收到消息后马上触发,因为回调逻辑一般比较复杂,为了防止中间解析过程出现问题,导致无法确定是否收到过回调之类的问题,放置了这个函数
     * 这个函数中,不建议执行任何业务相关的处理,业务处理建议放到 `afterMessage()` 方法中
     * 建议只是在开发阶段用户记录测试数据,正式阶段留空即可
     * @param string $xml
     * @return bool 返回true继续执行,返回false将终止这条xml数据的处理
     */
    public function receivedXml($xml)
    {
        // 测试示例,将收到的xml数据记录下来

        $file = Loader::getAlias('@runtime');
        $file = StringHelper::combPath($file, 'wechat_mp_event_xml.log');
        file_put_contents($file, date("Y-m-d H:i:s") . '================>' . PHP_EOL, FILE_APPEND);
        file_put_contents($file, $xml, FILE_APPEND);
        file_put_contents($file, PHP_EOL . PHP_EOL, FILE_APPEND);

        return true;
    }

    /**
     * 收到消息后的统一处理
     * 解析消息后马上执行,不论收到任何类型消息,都会执行该方法
     * 该消息执行比 `receivedXml()` 方法晚,中间经过了xml的解析
     * 这里面一般放置一些通用处理,不必判断消息类型做特定的处理
     * 下面有额外的单独的特定消息类型处理函数
     *
     * @param string $xml 明文xml
     * @param string $openid 用户的openid
     * @param mixed $extra_data 输出参数,该参数会传递到后续的处理函数中
     *              例如在这个函数中执行了用户注册或用户登录,则可以将这个值赋予用户的id,以便后续使用
     * @return bool 返回true继续执行,返回false将终止这条xml数据的处理
     */
    public function afterMessage($xml, $openid, &$extra_data = null)
    {

        // 示例伪代码,下面的代码只是做个示例,不能使用

//        $user = \QTTX::$app->db
//            ->select('*')
//            ->from('tbl_user')
//            ->whereArray(['wechat_openid' => $openid])
//            ->row();
//        if (empty($user)) {
//            // 没有用户则去注册用户
//            $access_token = AccessToken::get(微信扩展类);
//            // 通过openid获取用户信息
//            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
//            $result = \qh4module\wechat\models\Request::get($url);
//            $extra_data = 新注册的信息;
//        }else{
//            $extra_data = $user;
//        }
//
//        return true;


        $extra_data = '';

        return true;
    }


    /**
     * 收到普通消息的统一处理
     * 触发时间在 afterMessage() 之后
     * 注意: 事件类消息不会触发该函数,关于事件类消息和普通消息,请查阅微信文档
     * https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html
     * @param string $xml 收到的xml字符串
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     *
     * 注意: 如果该函数有返回值并且不为null,则不会触发后续单独的消息类型处理
     *
     * 返回值是一个数组,格式: ['type'=>'','content'=>'']
     *      返回值为null或者type为null,则不做任何处理
     *      其它的返回值会依据type作为某种格式的消息发送给用户
     *      type的类型支持 `XMLMp::MESSAGE_TYPE_*` 其它元素根据type类型变化.
     *          例如 type 为 'text', 则另一个元素需要是 content
     *      具体每种type对应的参数,请查阅微信手册
     */
    public function receivedMessage($xml, $openid, $extra_data)
    {
//        return [
//            'type' => XMLMp::MESSAGE_TYPE_TEXT,
//            'Content' => '当前时间 ' . date('Y-m-d H:i:s'),
//        ];
    }


    /**
     * 关注事件处理函数
     * 分为两种: 普通关注和扫描带场景的二维码关注
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @param string|int $event_key 如果是扫码带场景的二维码(普通二维码无效)关注会携带该参数,普通关注这个值为空
     *              该参数格式是 `qrscene_场景值`, 例如 qrscene_1001. 也就是场景值需要自己拆分出来
     * @param string $ticket 创建二维码时候返回的 ticket,如果是扫码带场景的二维码(普通二维码无效)关注会携带该参数,普通关注这个值为空
     * @return array|null 返回值将作为消息发送给用户
     * 返回值是一个数组,格式: ['type'=>'','MediaId'=>'','Title'=>'']
     *      返回值为null或者type为null,则不做任何处理
     *      其它的返回值会依据type作为某种格式的消息发送给用户
     *      type的类型支持 `XMLMp::MESSAGE_TYPE_*` 其它元素根据type类型变化.
     *          例如 type 为 'text', 则另一个元素需要是 content
     *              type 为 'image', 则另一个元素需要是 MediaId
     *              type 为 'video', 则另一个元素需要是 MediaId,Title,Description
     *      type 为 MESSAGE_TYPE_NEWS 图文消息时,略微特殊,格式为
     *              [
     *                  'type'=>'news',
     *                  'ArticleCount'=>3
     *                  'item'=>[
     *                      [Title=>'',Description=>],
     *                      [Title=>'',Description=>],
     *                      ...
     *                  ]
     *              ]
     *      如果type和其他元素不匹配,则忽略这次的返回值
     *      具体每种type对应的参数,请查阅微信手册
     * @see onScan() 关注后扫码
     * @see XMLMp::EVENT_TYPE_SUB
     */
    public function subscribe($openid, $extra_data, $event_key, $ticket)
    {

    }


    /**
     * 取消关注事件处理函数
     * @param string $openid
     * @param mixed $extra_data
     */
    public function unsubscribe($openid, $extra_data)
    {

    }


    /**
     * 菜单点击事件处理函数
     * @param $event_key string 菜单的key
     * @param $openid string 用户的标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @param string $xml 点击菜单后可能会有多种类型,模块暂未进行这方面处理,需要自己从xml中解析
     * @return array|null 返回值将作为消息发送给用户
     * @see subscribe() 返回值格式相同
     * @see XMLMp::EVENT_TYPE_CLICK
     */
    public function onClick($event_key, $openid, $extra_data, $xml)
    {

    }


    /**
     * 关注公众号以后的扫码事件
     * 注意: 扫码事件分为2中,未关注扫码和已关注扫码,触发的事件不同
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html
     * 1.用户未关注时，进行关注后的事件推送(触发subscribe函数)
     * 2.用户已关注时进行扫码事件推送(触发本函数)
     *
     * @param string|int $scene_id 生成二维码时自定义的二维码的场景值,根据生成是规则,可能是整数或者字符串
     * @param string $ticket 创建二维码时候返回的 ticket
     * @param string $openid 用户的openid
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see subscribe() 返回值格式相同
     * @see XMLMp::EVENT_TYPE_SCAN
     */
    public function onScan($scene_id, $ticket, $openid, $extra_data)
    {

    }

    /**
     * 用户上报位置事件处理函数
     * 注意: 用户在输入界面发送位置不会触发该函数
     * 触发机制参见 微信文档的 上报地理位置事件
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html
     * @param double $lng 经度
     * @param double $lat 纬度
     * @param double $precision 地理位置精度
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see subscribe() 返回值格式相同
     * @see XMLMp::EVENT_TYPE_LOCATION
     */
    public function onUpLocation($lng, $lat, $precision, $openid, $extra_data)
    {

    }


    /**
     * 收到文本消息的处理函数
     * @param string $content 消息内容
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see subscribe() 返回值格式相同
     * @see XMLMp::MESSAGE_TYPE_TEXT
     */
    public function receivedTextMessage($content, $msg_id, $openid, $extra_data)
    {

    }

    /**
     * 收到图片消息的处理函数
     * @param string $url 图片的url
     * @param string $media_id 图片消息媒体id，可以调用获取临时素材接口拉取数据
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see subscribe() 返回值格式相同
     * @see XMLMp::MESSAGE_TYPE_IMAGE
     */
    public function receivedImageMessage($url, $media_id, $msg_id, $openid, $extra_data)
    {

    }

    /**
     * 收到语音消息的处理函数
     * @param string $media_id 语音消息媒体id，可以调用获取临时素材接口拉取数据
     * @param string $format 语音格式
     * @param string $recognition 语音识别结果，UTF8编码. 开通语音识别后才会有这个字段
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see subscribe() 返回值格式相同
     * @see XMLMp::MESSAGE_TYPE_VOICE
     */
    public function receivedVoiceMessage($media_id, $format, $recognition, $msg_id, $openid, $extra_data)
    {

    }

    /**
     * 收到视频消息的处理函数
     * @param string $media_id 视频消息媒体id，可以调用获取临时素材接口拉取数据
     * @param string $thumb_media_id 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see XMLMp::MESSAGE_TYPE_VIDEO
     * @see subscribe() 返回值格式相同
     */
    public function receivedVideoMessage($media_id, $thumb_media_id, $msg_id, $openid, $extra_data)
    {

    }


    /**
     * 收到短视频消息的处理函数
     * @param string $media_id 视频消息媒体id，可以调用获取临时素材接口拉取数据
     * @param string $thumb_media_id 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see XMLMp::MESSAGE_TYPE_VIDEO
     * @see subscribe() 返回值格式相同
     */
    public function receivedShortVideoMessage($media_id, $thumb_media_id, $msg_id, $openid, $extra_data)
    {

    }

    /**
     * 收到位置信息后的处理函数
     * @param double $lng 经度
     * @param double $lat 纬度
     * @param int $scale 地图缩放大小
     * @param string $label 地理位置信息
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see XMLMp::MESSAGE_TYPE_LOCATION
     * @see subscribe() 返回值格式相同
     */
    public function receivedLocationMessage($lng, $lat, $scale, $label, $msg_id, $openid, $extra_data)
    {

    }

    /**
     * 收到链接消息的处理函数
     * @param string $title 消息标题
     * @param string $description 消息描述
     * @param string $url 消息链接
     * @param string $msg_id 消息id,消息可能重复发送,所以要注意处理msg_id重复
     * @param string $openid 用户标示
     * @param mixed $extra_data afterMessage() 函数的输出参数
     * @return array|null 返回值将作为消息发送给用户
     * @see XMLMp::MESSAGE_TYPE_LOCATION
     * @see subscribe() 返回值格式相同
     */
    public function receivedLinkMessage($title, $description, $url, $msg_id, $openid, $extra_data)
    {

    }
}