<?php
class wx extends spController
{
	function index(){
        $wx = spClass('spWeiXin'); 
        $msg = $wx->run();
        if($msg){
            switch ($msg['MsgType']) {
                case 'text':
                    $wxDesp = '<p>这是一条来自微信端的消息，如果你也想发送，请微信搜索添加 dmrobot ，或者扫描用微信下面的二维码</p><p><img src="/public/img/wx.jpg" class="img-polaroid"></p>';
                    $now = time();
                    $length = strlen($msg['Content']);
                    if($length < 100){
                        $title = $msg['Content'];
                        $content = $wxDesp;
                    } else {
                        $title = cut_str($msg['Content'], 90, 0); ;
                        $content = $msg['Content'].'<hr>'.$wxDesp;
                    }
                    $title = '[来自微信]'.$title;
                    $info = array(
                        'uid'=>0,
                        'title'=>strip_illegal_tags($title),
                        'content'=>strip_illegal_tags($content),
                        'time' => $now,
                        'update_time' => $now,
                        'hits' => 1
                        );
                    $postsObj = spClass('libPosts');
                    if($postsObj->create($info)){
                        $wx->replyText('发布成功，去看看：'.spUrl('main','index'));
                    } else {
                        $wx->replyText('发布失败');
                    }
                    break;
                case 'image':
                    # code...
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
}