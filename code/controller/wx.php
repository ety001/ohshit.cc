<?php
class wx extends spController
{
	function index(){
        $wx = spClass('spWeiXin'); 
        $msg = $wx->run();
        if($msg){
            switch ($msg['MsgType']) {
                case 'text':
                    $this->textType($msg,$wx);
                    break;
                case 'image':
                    $this->imageType($msg,$wx);
                    break;
                case 'location':
                    # code...
                    break;
                
                default:
                    $wx->replyText('暂时还没有你想要的功能');
                    break;
            }
        }
	}

    private function textType($msg,$wx){
        //获取缓存信息
        $cache = spAccess('r',$msg['FromUserName']);
        $mediaContent = '';
        //如果有缓存信息，则先处理缓存的信息
        if($cache){
            spAccess('c', $msg['FromUserName']);
            switch ($cache['MsgType']) {
                case 'image':
                    $mediaContent = implode('', $cache['picStr']);
                    break;
                case 'location':
                    $mediaContent = '<p></p>';
                    break;
                
                default:
                    break;
            }
        }
        //开始处理文字信息
        $wxDesp = '<p>这是一条来自微信公共平台端的消息，如果你也想发送，请微信搜索添加 dmrobot ，或者扫描用微信下面的二维码</p><p><img src="/public/img/wx.jpg" class="img-polaroid"></p>';
        $now = time();
        $length = strlen($msg['Content']);
        if($length < 100){
            $title = $msg['Content'];
            $content = $mediaContent.$wxDesp;
        } else {
            $title = cut_str($msg['Content'], 90, 0); ;
            $content = $mediaContent.$msg['Content'].'<hr>'.$wxDesp;
        }
        $title = '<img src="/public/img/wx-logo.png" class="px28 r_margin">'.$title;
        $info = array(
            'uid'=>0,
            'title'=>strip_illegal_tags($title),
            'content'=>strip_illegal_tags($content),
            'time' => $now,
            'update_time' => $now,
            'hits' => 1
            );
        //发布文字信息
        $postsObj = spClass('libPosts');
        if($id = $postsObj->create($info)){
            echo $wx->replyText('发布成功，去看看：http://'.$_SERVER['SERVER_NAME'].spUrl('main','l',array('id'=>$id)));
        } else {
            echo $wx->replyText('发布失败');
        }
    }

    private function imageType($msg,$wx){
        //获取远程图片保存到本地
        $dirInfo = $this->chkdir();
        $picName = $this->getRemotePic($msg,$dirInfo['dirTime']);
        $str = '<p><img src="/upload/wx-upload/'.$dirInfo['dirTime'].'/'.$picName.'" class="img-polaroid"></p>';
        //获取缓存信息
        $cache = spAccess('r' , $msg['FromUserName']);
        if($cache){
            array_push($cache['picStr'], $str);
        } else {
            $picStr[0] = $str;
            $cache['picStr'] = $picStr;
            $cache['MsgType'] = 'image';
        }
        spAccess('w' , $msg['FromUserName'], $cache, 3600);
        if(count($cache['picStr'])<=1){
            echo $wx->replyText('请输入图片的备注文字信息【有效期1小时】');
        }
    }

    //检查并创建目录
    private function chkdir(){
        $dirTime = date('Y-m-d',time());
        $dirName = APP_PATH.'/upload/wx-upload/'.$dirTime;
        if(!file_exists($dirName)){
            mkdir($dirName);
        }
        $dir = array('dirTime'=>$dirTime,'dirName'=>$dirName);
        return $dir;
    }

    //获取远程服务器图片并保存
    private function getRemotePic($memInfo,$dirTime){
        //图片保存在本地的名称
        $picFileName = md5($memInfo['PicUrl']);
        //图片保存在本地的位置
        $picPath = './upload/wx-upload/'.$dirTime.'/';
        //获取图片的内容
        $picContent = file_get_contents($memInfo['PicUrl']);
        //写入本地图片文件
        $fObj = fopen($picPath.$picFileName,'w');
        fwrite($fObj, $picContent);
        fclose($fObj);
        return $picFileName;
    }
}