<?php
class main extends spController
{
	function index(){
		$postsObj = spClass('libPosts');
        $allPosts = $postsObj->findAll();

        $this->allPosts = $allPosts;
        $this->display('index.html');
	}

    function addPost(){
        $this->display('addPost.html');
    }

    function savePost(){
        $info = $this->spArgs();
        unset($info['c']);
        unset($info['a']);
        $info['time'] = time();
        $info['uid'] = 0;
        $postsObj = spClass('libPosts');
        $verifier = $postsObj->spVerifier($info);
        if( false == $verifier){
            if($postsObj->create($info)){
                $this->success('发布成功',spUrl('main','index'));
            } else {
                $this->error('发布失败');
            }
        } else {
            $msg = array_pop($verifier);
            $this->error(array_pop($msg));
        }
    }

    function saveComment(){
        $info = $this->spArgs();
        unset($info['c']);
        unset($info['a']);
        $info['time'] = time();
        $commentsObj = spClass('libComments');
        $verifier = $commentsObj->spVerifier($info);
        if( false == $verifier){
            if($commentsObj->create($info)){
                $this->success('发布成功');
            } else {
                $this->error('发布失败');
            }
        } else {
            $msg = array_pop($verifier);
            $this->error(array_pop($msg));
        }

    }
}