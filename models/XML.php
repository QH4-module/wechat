<?php
/**
 * File Name: XML.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/12 1:54 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\wechat\models;

use DOMDocument;
use Exception;
use QTTX;

class XML
{

    /**
     * 解析微信发送的xml消息体为数组
     * @param $xml string
     * @param $fields array 从xml中解析哪些字段
     * @return array
     */
    public static function parse($xml, $fields)
    {
        $ary = [];
        $document = new DOMDocument();
        $document->loadXML($xml);
        foreach ($fields as $field) {
            $ary[$field] = $document->getElementsByTagName($field)->item(0)->nodeValue;
        }
        return $ary;
    }

    /**
     * 从xml中获取单个节点的信息,如果节点不存在,返回null
     * @param $xml string
     * @param $node_name string
     * @return null|string
     */
    public static function parseOneNode($xml, $node_name)
    {
        try {
            $document = new DOMDocument();
            $document->loadXML($xml);
            $tag = $document->getElementsByTagName($node_name);
            if (empty($tag)) return null;
            $item = $tag->item(0);
            if (empty($item)) return null;
            $value = $item->nodeValue;
            if (empty($value) && $value !== '' && $value !== 0 && $value !== false) {
                return null;
            }
            return $value;
        } catch (Exception $exception) {
            QTTX::$app->log->warning("解析微信XML消息失败：" . $exception->getMessage());
            return false;
        }
    }
}