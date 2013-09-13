<?php

/**
 * <b>SpeedPHP微信接口扩展</b>
 * <p>2013年8月15日 08:51:23</p>
 * @author Lee(leeldy[AT]163.com)
 * @version 1.0
 */
class spWeiXin {

    //微信通信密钥
    private $token;
    //微信公众帐号ID
    private $publicuserid;
    //信息接受者
    private $touserid;

    public function __construct() {
        $params = spExt('spWeiXin');
        //获取配置参数
        if (empty($params['TOKEN'])) {
            spError('微信公众帐号通信密钥未设置！');
        }
        $this->token = $params['TOKEN'];
    }

    /**
     * 绑定微信接口
     * @return string|false
     */
    public function bind() {

        //随机字符串
        $echoStr = $_GET["echostr"];
        //微信加密签名
        $signature = $_GET["signature"];
        //签名时间戳
        $timestamp = $_GET["timestamp"];
        //加密随机数
        $nonce = $_GET["nonce"];

        /*
         * 加密/校验流程：
          1. 将token、timestamp、nonce三个参数进行字典序排序
          2. 将三个参数字符串拼接成一个字符串进行sha1加密
          3. 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
         */
        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr);
        $_signature = sha1(implode($tmpArr));

        if ($_signature != $signature) {
            //签名不正确，返回false
            $echoStr = false;
        }

        return $echoStr;
    }

    /**
     * 接收消息，返回消息数组
     * @return array
     */
    public function receive() {
        $raw = $this->php_fix_raw_query();
        $msg = false;
        if (!empty($raw)) {
            $msg = (array) simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->publicuserid = $msg['ToUserName'];
            $this->touserid = $msg['FromUserName'];
        }

        return $msg;
    }

    /**
     * 获取 POST 提交的原始数据
     * @author robotreply at gmail dot com (24-Jul-2009 08:17)
     * @return string
     */
    private function php_fix_raw_query() {

        // Try globals array
        //试图从全局变量中获取
        if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
            return $GLOBALS["HTTP_RAW_POST_DATA"];
        }
        // Try globals variable
        //试图从全局变量中获取
        if (isset($HTTP_RAW_POST_DATA)) {
            return $HTTP_RAW_POST_DATA;
        }

        $post = '';
        // Try stream
        //试图从流中获取
        if (!function_exists('file_get_contents')) {
            //php://input is not available with enctype="multipart/form-data".
            $fp = fopen("php://input", "r");
            if ($fp) {
                while (!feof($fp)) {
                    $post = fread($fp, 1024);
                }

                fclose($fp);
            }
        } else {
            $post = file_get_contents("php://input");
        }

        return $post;
    }

    /**
     * <b>回复文本消息</b>
     * <p>对于每一个POST请求，开发者在响应包中返回特定xml结构</p>
     * <p>对该消息进行响应（现支持回复文本、图文、语音、视频、音乐和对收到的消息进行星标操作）</p>
     * <p>微信服务器在五秒内收不到响应会断掉连接。</p>
     * @param string $Content  回复的消息内容
     * @return string|false
     */
    public function replyText($Content) {
        $msg = false;

        if (!empty($Content)) {
            //CreateTime     消息创建时间
            $CreateTime = time();

            $msg = <<<XML
<xml>
    <ToUserName><![CDATA[{$this->touserid}]]></ToUserName>
    <FromUserName><![CDATA[{$this->publicuserid}]]></FromUserName>
    <CreateTime>{$CreateTime}</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[{$Content}]]></Content>
</xml>
XML;
        }

        return $msg;
    }

    /**
     * <b>回复音乐消息</b>
     * @param string $Title 标题
     * @param string $Description 描述
     * @param string $MusicUrl 音乐链接
     * @param string $HQMusicUrl 高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @return string|false
     */
    public function replyMusic($Title, $Description, $MusicUrl, $HQMusicUrl) {
        $msg = false;

        if (!empty($MusicUrl)) {
            //CreateTime     消息创建时间
            $CreateTime = time();

            $msg = <<<XML
<xml>
    <ToUserName><![CDATA[{$this->touserid}]]></ToUserName>
    <FromUserName><![CDATA[{$this->publicuserid}]]></FromUserName>
    <CreateTime>{$CreateTime}</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    <Music>
        <Title><![CDATA[{$Title}]]></Title>
        <Description><![CDATA[{$Description}]]></Description>
        <MusicUrl><![CDATA[{$MusicUrl}]]></MusicUrl>
        <HQMusicUrl><![CDATA[{$HQMusicUrl}]]></HQMusicUrl>
    </Music>
</xml>
XML;
        }

        return $msg;
    }

    /**
     * 回复图文消息
     * @param type $Articles 文章列表 array(array(Title,PicUrl,Url))
     * @return string|false
     */
    public function replyNews($Articles) {
        $msg = false;

        $articlesStr = '';
        //图文消息个数，限制为10条以内
        $ArticleCount = 0;
        foreach ($Articles as $_art) {
            if (!empty($_art['Title']) && !empty($_art['PicUrl']) && !empty($_art['Url'])) {
                $ArticleCount++;
                $articlesStr .= "
    <item>
        <Title><![CDATA[{$_art['Title']}]]></Title>
        <Description><![CDATA[{$_art['Description']}]]></Description>
        <PicUrl><![CDATA[{$_art['PicUrl']}]]></PicUrl>
        <Url><![CDATA[{$_art['Url']}]]></Url>
    </item>";
            }
        }

        if (!empty($ArticleCount)) {
            //CreateTime     消息创建时间
            $CreateTime = time();

            $msg = <<<XML
<xml>
    <ToUserName><![CDATA[{$this->touserid}]]></ToUserName>
    <FromUserName><![CDATA[{$this->publicuserid}]]></FromUserName>
    <CreateTime>{$CreateTime}</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>{$ArticleCount}</ArticleCount>
    <Articles>{$articlesStr}
    </Articles>
</xml>
XML;
        }

        return $msg;
    }

    /**
     * 运行
     * @return type
     */
    function run() {
        //微信服务器每次请求都会将signature,timestamp,nonce三个参数GET到接口
        //只能通过是否存在echostr来判断是否是接口绑定动作
        if (isset($_GET['echostr'])) {
            //绑定
            exit($this->bind());
        } else {
            //收到信息
            return $this->receive();
        }
    }

}

?>