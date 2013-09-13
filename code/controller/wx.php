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
        $original = 0;
        //获取缓存信息
        $cacheInfo = spAccess('r',$msg['FromUserName']);
        $mediaContent = '';
        $tagUpload = 0;
        //如果有缓存信息，则先处理缓存的信息
        if($cacheInfo){
            spAccess('c', $msg['FromUserName']);
            switch ($cacheInfo['MsgType']) {
                case 'image':
                    $dirInfo = $this->chkdir();
                    //因为图片上传需要时间，此处只生成帖子内容中的图片链接，图片上传将在echo后进行
                    foreach ($cacheInfo['pic'] as $k => $v) {
                        //$picName = $this->getRemotePic($v,$dirInfo['dirTime']);
                        $picName = md5($v['PicUrl']);
                        $mediaContent .= '<p><img src="/upload/wx-upload/'.$dirInfo['dirTime'].'/'.$picName.'"></p>';
                    }
                    $tagUpload = 1;
                    $original = 1;
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
        $info = array(
            'uid'=>0,
            'original'=>$original;
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
        //刷缓存，让微信先回复用户信息
        ob_flush();
        flush();
        //判断是否要处理上传图片
        if($tagUpload){
            foreach ($cacheInfo['pic'] as $k => $v) {
                $picName = $this->getRemotePic($v,$dirInfo['dirTime']);
            }
        }
    }

    private function imageType($msg,$wx){
        /*特别注意：把图片放到最后的发文字信息的地方，这样保证了一小时后过期的图片不会保存到本地形成垃圾文件*/
        //获取缓存信息
        $cacheInfo = spAccess('r' , $msg['FromUserName']);
        if($cacheInfo){
            array_push($cacheInfo['pic'], $msg);
        } else {
            $cacheInfo['MsgType'] = 'image';
            $cacheInfo['pic'][0] = $msg;
        }
        spAccess('w' , $msg['FromUserName'], $cacheInfo, 3600);
        if(count($cacheInfo['pic'])<=1){
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