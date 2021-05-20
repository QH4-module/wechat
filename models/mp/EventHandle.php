<?php
/**
 * File Name: EventHandle.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/24 10:08 下午
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
use QTTX;
use qttx\web\ServiceModel;

/**
 * Class EventHandle
 * @package qh4module\wechat\models\mp
 * @property ExtWechatMp $external
 */
class EventHandle extends ServiceModel
{

    /**
     * @var string 保存接收到的xml数据
     */
    protected $xml = '';


    public function run()
    {
        // 验证请求的有效性
        $token = $this->external->mpToken();
        $signature = QTTX::$request->get('signature');
        $timestamp = QTTX::$request->get('timestamp');
        $nonce = QTTX::$request->get('nonce');
        if (Access::sha1($token, $timestamp, $nonce) != $signature) {
            QTTX::$response->setStatusCode(400);
            return false;
        }


        $this->xml = QTTX::$request->getRawBody();
        if ($this->external->mpEventHandle()->receivedXml($this->xml)) {
            $mode = $this->external->mpMessageMode();
            if ($mode == WECHAT_MP_MESSAGE_PLAINTEXT) {
                // 明文模式
                $this->plaintextRun();
            }
        }
    }

    /**
     * 明文消息的处理
     */
    protected function plaintextRun()
    {
        $openid = QTTX::$request->get('openid');
        if (empty($openid)) {
            $openid = XMLMp::parseOneNode($this->xml, 'FromUserName');
        }

        // 收到事件的统一处理
        $extra_data = null;
        if (!$this->external->mpEventHandle()->afterMessage($this->xml, $openid, $extra_data)) return;

        // 解析事件类型
        $message_type = XMLMp::getMessageType($this->xml);


        switch (strtolower($message_type)) {
            case XMLMp::MESSAGE_TYPE_EVENT:
                $event_type = XMLMp::parseOneNode($this->xml, 'Event');
                if (strtoupper($event_type) == XMLMp::EVENT_TYPE_CLICK) {
                    // 点击菜单的处理
                    $event_key = XMLMp::parseOneNode($this->xml, 'EventKey');
                    $result = $this->external->mpEventHandle()->onClick($event_key, $openid, $extra_data, $this->xml);
                    echo $this->formatResponse($result, $openid);
                } else if (strtolower($event_type) == XMLMp::EVENT_TYPE_SUB) {
                    // 关注事件
                    $event_key = XMLMp::parseOneNode($this->xml, 'EventKey');
                    $ticket = XMLMp::parseOneNode($this->xml, 'Ticket');
                    $result = $this->external->mpEventHandle()->subscribe($openid, $extra_data, $event_key, $ticket);
                    echo $this->formatResponse($result, $openid);
                } else if (strtolower($event_type) == XMLMp::EVENT_TYPE_UNSUB) {
                    // 取消关注
                    $this->external->mpEventHandle()->unsubscribe($openid, $extra_data);
                } else if (strtoupper($event_type) == XMLMp::EVENT_TYPE_SCAN) {
                    // 关注后扫码
                    $event_key = XMLMp::parseOneNode($this->xml, 'EventKey');
                    $ticket = XMLMp::parseOneNode($this->xml, 'Ticket');
                    $result = $this->external->mpEventHandle()->onScan($event_key, $ticket, $openid, $extra_data);
                    echo $this->formatResponse($result, $openid);
                } else if (strtoupper($event_type) == XMLMp::EVENT_TYPE_LOCATION) {
                    // 上报位置事件
                    $lat = XMLMp::parseOneNode($this->xml, 'Latitude');
                    $lng = XMLMp::parseOneNode($this->xml, 'Longitude');
                    $precision = XMLMp::parseOneNode($this->xml, 'Precision');
                    $result = $this->external->mpEventHandle()->onUpLocation($lng, $lat, $precision, $openid, $extra_data);
                    echo $this->formatResponse($result, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_TEXT:
                // 收到文本消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $content = XMLMp::parseOneNode($this->xml, 'Content');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedTextMessage($content, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_IMAGE:
                // 收到图片消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $url = XMLMp::parseOneNode($this->xml, 'PicUrl');
                    $media_id = XMLMp::parseOneNode($this->xml, 'MediaId');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedImageMessage($url, $media_id, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_VOICE:
                // 收到语音消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $media_id = XMLMp::parseOneNode($this->xml, 'MediaId');
                    $format = XMLMp::parseOneNode($this->xml, 'Format');
                    $recognition = XMLMp::parseOneNode($this->xml, 'Recognition');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedVoiceMessage($media_id, $format, $recognition, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_VIDEO:
                // 收到视频消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $media_id = XMLMp::parseOneNode($this->xml, 'MediaId');
                    $thumb_media_id = XMLMp::parseOneNode($this->xml, 'ThumbMediaId');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedVideoMessage($media_id, $thumb_media_id, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_SHORT_VIDWO:
                // 收到小视频消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $media_id = XMLMp::parseOneNode($this->xml, 'MediaId');
                    $thumb_media_id = XMLMp::parseOneNode($this->xml, 'ThumbMediaId');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedShortVideoMessage($media_id, $thumb_media_id, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_LOCATION:
                // 收到位置消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $loc_x = XMLMp::parseOneNode($this->xml, 'Location_X');
                    $loc_y = XMLMp::parseOneNode($this->xml, 'Location_Y');
                    $scale = XMLMp::parseOneNode($this->xml, 'Scale');
                    $label = XMLMp::parseOneNode($this->xml, 'Label');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedLocationMessage($loc_y, $loc_x, $scale, $label, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                }
                break;
            case XMLMp::MESSAGE_TYPE_LINK:
                // 收到链接消息
                $result1 = $this->external->mpEventHandle()->receivedMessage($this->xml, $openid, $extra_data);
                if ($result1) {
                    echo $this->formatResponse($result1, $openid);
                } else {
                    $title = XMLMp::parseOneNode($this->xml, 'Title');
                    $desc = XMLMp::parseOneNode($this->xml, 'Description');
                    $url = XMLMp::parseOneNode($this->xml, 'Url');
                    $msg_id = XMLMp::parseOneNode($this->xml, 'MsgId');
                    $result2 = $this->external->mpEventHandle()->receivedLinkMessage($title, $desc, $url, $msg_id, $openid, $extra_data);
                    echo $this->formatResponse($result2, $openid);
                    break;
                }
                break;
            default:
                break;
        }
    }


    protected function formatResponse($result, $openid)
    {
        $message = '';

        if ($result && isset($result['type'])) {
            switch ($result['type']) {
                case XMLMp::MESSAGE_TYPE_TEXT:
                    if (isset($result['Content']) && $result['Content']) {
                        $template = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Content><![CDATA[%s]]></Content>
</xml>";
                        $message = sprintf($template, $openid, $this->external->mpOriginalId(),
                            time(), 'text', $result['Content']);
                    }
                    break;
                case XMLMp::MESSAGE_TYPE_IMAGE:
                    if (isset($result['MediaId']) && $result['MediaId']) {
                        $template = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Image>
    <MediaId><![CDATA[%s]]></MediaId>
  </Image>
</xml>";
                        $message = sprintf($template, $openid, $this->external->mpOriginalId(),
                            time(), 'image', $result['MediaId']);
                    }
                    break;
                case XMLMp::MESSAGE_TYPE_VOICE:
                    if (isset($result['MediaId']) && $result['MediaId']) {
                        $template = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Voice>
    <MediaId><![CDATA[%s]]></MediaId>
  </Voice>
</xml>";
                        $message = sprintf($template, $openid, $this->external->mpOriginalId(),
                            time(), 'voice', $result['MediaId']);
                    }
                    break;
                case XMLMp::MESSAGE_TYPE_VIDEO:
                    if (isset($result['MediaId']) && $result['MediaId']) {
                        $template = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Video>
    <MediaId><![CDATA[%s]]></MediaId> 
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
  </Video>
</xml>";
                        $title = isset($result['Title']) ? $result['Title'] : '';
                        $desc = isset($result['Description']) ? $result['Description'] : '';
                        $message = sprintf($template, $openid, $this->external->mpOriginalId(),
                            time(), 'video', $result['MediaId'], $title, $desc);
                    }
                    break;
                case XMLMp::MESSAGE_TYPE_MUSIC:
                    if (isset($result['ThumbMediaId']) && $result['ThumbMediaId']) {
                        $template = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
  </Music>
</xml>";
                        $title = isset($result['Title']) ? $result['Title'] : '';
                        $desc = isset($result['Description']) ? $result['Description'] : '';
                        $url = isset($result['Description']) ? $result['Description'] : '';
                        $hq_url = isset($result['Description']) ? $result['Description'] : '';
                        $message = sprintf($template, $openid, $this->external->mpOriginalId(),
                            time(), 'music', $title, $desc, $url, $hq_url, $result['ThumbMediaId']);
                    }
                    break;
                case XMLMp::MESSAGE_TYPE_NEWS:
                    if (isset($result['ArticleCount']) && $result['ArticleCount'] && isset($result['item']) && is_array($result['item'])) {
                        $template = "<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <ArticleCount>%s</ArticleCount>
  <Articles>
      %s
  </Articles>
</xml>";
                        $template_item = "<item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>";

                        $str_item = '';

                        foreach ($result['item'] as $item) {
                            $str_item .= sprintf($template_item,
                                isset($item['Title']) ? $item['Title'] : '',
                                isset($item['Description']) ? $item['Description'] : '',
                                isset($item['PicUrl']) ? $item['PicUrl'] : '',
                                isset($item['Url']) ? $item['Url'] : ''
                            );
                        }

                        $message = sprintf($template, $openid, $this->external->mpOriginalId(),
                            time(), 'news', $result['ArticleCount'], $str_item);
                    }
                    break;
                default:
                    $message = '';
                    break;
            }
        }

        return $message;
    }

}