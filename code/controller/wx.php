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
        $memInfo = spAccess('r',$msg['FromUserName']);
        $mem = '';
        //如果有缓存信息，则先处理缓存的信息
        if($memInfo){
            spAccess('c', $msg['FromUserName']);
            //如果缓存信息是数组那就是图片信息
            if(is_array($memInfo)){
                $dirInfo = $this->chkdir();
                foreach ($memInfo as $k => $v) {
                    $picName = $this->getRemotePic($v,$dirInfo['dirTime']);
                    $mem = $mem . '<p><img src="/upload/wx-upload/'.$dirInfo['dirTime'].'/'.$picName.'"></p>';
                }
            }
            switch ($memInfo['MsgType']) {
                case 'image':
                    break;
                case 'location':
                    $mem = '<p></p>';
                    break;
                
                default:
                    $mem = '';
                    break;
            }
        }
        $wxDesp = '<p>这是一条来自微信端的消息，如果你也想发送，请微信搜索添加 dmrobot ，或者扫描用微信下面的二维码</p><p><img src="/public/img/wx.jpg" class="img-polaroid"></p>';
        $now = time();
        $length = strlen($msg['Content']);
        if($length < 100){
            $title = $msg['Content'];
            $content = $mem.$wxDesp;
        } else {
            $title = cut_str($msg['Content'], 90, 0); ;
            $content = $mem.$msg['Content'].'<hr>'.$wxDesp;
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
        $postsObj = spClass('libPosts');
        if($id = $postsObj->create($info)){
            echo $wx->replyText('发布成功，去看看：http://'.$_SERVER['SERVER_NAME'].spUrl('main','l',array('id'=>$id)));
        } else {
            echo $wx->replyText('发布失败');
        }
    }

    private function imageType($msg,$wx){
        //能够接收多张图片
        $mem = spAccess('r' , $msg['FromUserName']);
        if($mem){
            array_push($mem, $msg);
        } else {
            $mem[0] = $msg;
        }
        spAccess('w' , $msg['FromUserName'], $mem, 3600);
        if(count($mem)<=1){
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