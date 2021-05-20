<?php
/**
 * File Name: XMLMp.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/12 1:55 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models\mp;


use qh4module\wechat\models\XML;
use DOMDocument;
use Exception;
use QTTX;

class XMLMp extends XML
{
    // 文本消息
    const MESSAGE_TYPE_TEXT = 'text';
    // 图片
    const MESSAGE_TYPE_IMAGE = 'image';
    // 语音
    const MESSAGE_TYPE_VOICE = 'voice';
    // 视频
    const MESSAGE_TYPE_VIDEO = 'video';
    // 小视频,该消息只能收到,不能发送
    const MESSAGE_TYPE_SHORT_VIDWO = 'shortvideo';
    // 地理位置,该消息只能收到,不能发送
    const MESSAGE_TYPE_LOCATION = 'location';
    // 链接,该消息只能收到,不能发送
    const MESSAGE_TYPE_LINK = 'link';
    // 事件
    const MESSAGE_TYPE_EVENT = 'event';
    // 音乐消息,该消息只能发送,不会收到
    const MESSAGE_TYPE_MUSIC = 'music';
    // 图文消息,该消息只能发送,不会收到
    const MESSAGE_TYPE_NEWS = 'news';

    // 关注事件
    const EVENT_TYPE_SUB = 'subscribe';
    // 取消关注事件
    const EVENT_TYPE_UNSUB = 'unsubscribe';
    // 关注后扫描二维码
    const EVENT_TYPE_SCAN = 'SCAN';
    // 上报地理位置事件
    const EVENT_TYPE_LOCATION = 'LOCATION';
    // 自定义菜单事件
    const EVENT_TYPE_CLICK = 'CLICK';

    // 文本消息
    public static $text_message = ['ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Content', 'MsgId'];

    // 关注/取消关注事件
    public static $subscribe_event = ['ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Event'];

    /**
     * 获取xml消息的类型
     * @param $xml
     * @return string|null
     */
    public static function getMessageType($xml)
    {
        try {
            $document = new DOMDocument();
            $document->loadXML($xml);
            return $document->getElementsByTagName('MsgType')->item(0)->nodeValue;
        } catch (Exception $exception) {
            QTTX::$app->log->warning("解析微信XML消息失败：" . $exception->getMessage());
            return null;
        }
    }
}