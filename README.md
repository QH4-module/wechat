QH4框架扩展模块-微信模块

涵盖了微信公众号菜单、事件、收发信息等一系列接口

包括微信支付，暂时只有公众号相关支付，后续会补充 h5，app，小程序等支付

### 注意
* 该模块的部分接口极度依赖实际业务,所以这些接口提供的是伪代码
* 模块包含的功能比较多,所以扩展类也很多,根据实际需要选择即可
* 该模块的扩展类,不仅有控制类,还有一些是数据类,使用时候,注意查看注释
* 该模块的 `actionGetDownloadWechatCertificateCommand()` 接口使用后请立刻注释掉,绝对禁止对外开放

### 依赖
使用微信支付需要以下依赖
```shell
composer require wechatpay/wechatpay-guzzle-middleware
```
但是微信给的sdk包,缺东西,所以还需要安装
```shell
composer require guzzlehttp/guzzle
```

### api 列表 
#### 公众号部分api
```php
/**
 * 微信公众号配置服务器验证
 * 服务器配置的服务器地址应该填写  http://域名/控制器/access
 * 例如: http://wwww.qttx.com/wechat/access
 * 注意,这个函数在配置服务器完成后需要注释掉,然后将另一个同名api取消注释
 * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html
 */
public function actionAccess()
```

```php
/**
 * 微信公众号事件回调方法
 * 微信公众号配置完服务器后,用户触发的事件会回调设置的服务器地址
 * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html
 * 由于配置服务器验证和回调是同一个地址,所以有了两个同名方法,配置完服务器后,请将此方法取消注释
 */
public function actionAccess()
```

```php
/**
 * 生成带场景的二维码
 * 如果有批量生成的需求,逻辑完全一致,可以自己写一个循环脚本
 * @see https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
 */
public function actionCreateQrcode()
```

```php
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
```

```php
/**
 * 微信公众号网页通过 code 获取 用户id
 * 该方法除了去微信换取access_token,还处理了用户部分,返回到客户端的是用户的id
 * 比 [actionMpWebCode2AccessToken] 接口更安全
 * @return array 用户id
 */
public function actionMpWebCode2UserId()
```

```php
/**
 * 通过模板设置公众号菜单
 * @return array
 */
public function actionSetTemplateToWechat()
```

```php
/**
 * 菜单访问重定向
 * 如果开启了服务器中转模式则会使用该方法中转
 * @see ExtWechatMp::mpMenuServerRedirect() 菜单跳转模式说明
 */
public function actionRedirectUrl()
```

#### 公众号菜单api
```php
/**
 * 新增微信公众号菜单模板
 * @return array
 */
public function actionMpCreateMenuTemplate()
```

```php
/**
 * 获取公众号菜单模板
 * @return array
 */
public function actionMpMenuTemplateList()
```

```php
/**
 * 设置公众号模板菜单数据
 * @return array
 */
public function actionMpSetMenuTemplate()
```

```php
/**
 * 删除公众号菜单模板
 * @return array
 */
public function actionMpDelMenuTemplate()
```

#### 支付相关api
```php
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
public function actionGetDownloadWechatCertificateCommand()
```

```php
/**
 * 发起公众号支付
 * 注意: 这个接口是示例接口,会报错,内部存在伪代码
 *      根据自己的实际业务需求,改写model
 */
public function actionMpCreateOrder()
```

```php
/**
 * 去微信查询订单支付情况
 * 注意 : 该接口也会触发微信支付回调,所以支付回调一定要处理多次请求的情况
 * 注意 : 该接口可以适用于多种支付(公众号,小程序,app),需要改成不同配置类
 * @see ExtWechatPayNotify::handle()
 * @return array
 */
public function actionQueryOrder()
```

```php
/**
 * 微信支付回调
 * 注意 : 该接口可以适用于多种支付(公众号,小程序,app),需要改成不同配置类
 */
public function actionPayNotify()
```

#### 公众号事件回调相关方法(基本都是空方法,根据业务补充)
```php
/**
 * 收到xml数据后的处理
 * 这个函数是在收到消息后马上触发,因为回调逻辑一般比较复杂,为了防止中间解析过程出现问题,导致无法确定是否收到过回调之类的问题,放置了这个函数
 * 这个函数中,不建议执行任何业务相关的处理,业务处理建议放到 `afterMessage()` 方法中
 * 建议只是在开发阶段用户记录测试数据,正式阶段留空即可
 * @param string $xml
 * @return bool 返回true继续执行,返回false将终止这条xml数据的处理
 */
public function receivedXml($xml)
```

```php
/**
 * 收到消息后的统一处理
 * 解析消息后马上执行,不论收到任何类型消息,都会执行该方法
 * 该消息执行比 `receivedXml()` 方法晚,中间经过了xml的解析
 * 这里面一般放置一些通用处理,不必判断消息类型做特定的处理
 * 下面有额外的单独的特定消息类型处理函数
 *
 * @param string $xml 明文xml
 * @param string $openid 用户的openid
 * @param string $timestamp 时间戳
 * @param mixed $extra_data 输出参数,该参数会传递到后续的处理函数中
 *              例如在这个函数中执行了用户注册或用户登录,则可以将这个值赋予用户的id,以便后续使用
 * @return bool 返回true继续执行,返回false将终止这条xml数据的处理
 */
public function afterMessage($xml, $openid, $timestamp, &$extra_data = null)
```

```php
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
```

```php
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
```

```php
/**
 * 取消关注事件处理函数
 * @param string $openid
 * @param mixed $extra_data
 */
public function unsubscribe($openid, $extra_data)
```

```php
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
```

```php
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
```


```php
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
```

```php
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
```

```php
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
```

```php
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
```

```php
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
```

```php
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
```

```php
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
```

```php
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
```


### 方法列表
```php
/**
 * 获取微信公众号调用接口用的 access_token
 * @param ExtWechat $external
 * @return false|mixed|string
 */
public static function getMpAccessToken(ExtWechat $external)
```