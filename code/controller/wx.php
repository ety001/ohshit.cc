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
        $memInfo = spAccess('r',$msg['FromUserName']);
        $mem = '';
        if($memInfo){
            spAccess('c', $msg['FromUserName']);
            switch ($memInfo['MsgType']) {
                case 'image':
                    $mem = '<p><img src="'.$memInfo['PicUrl'].'" class="img-polaroid"></p>';
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
        spAccess('w' , $msg['FromUserName'], $msg, 3600);
        echo $wx->replyText('请在1小时内输入你要给图片的备注文字信息');
    }
}