<?php
class main extends spController
{
	function index(){
		$postsObj = spClass('libPosts');
        $allPosts = $postsObj->findAll(false,'time desc');

        $this->allPosts = $allPosts;
        $this->display('index.html');
	}

    function l(){
        $id = (int)$this->spArgs('id');
        if($id){
            $conditions['id'] = $id;
        } else {
            $this->error('错误的文章id',spUrl('main','index'));
            return;
        }
        $postsObj = spClass('libPosts');
        $this->post = $postsObj->find($conditions);
        $commentsObj = spClass('libComments');
        $this->allComments = $commentsObj->findAll(array('post_id'=>$id),'time desc');
        $this->display('l.html');
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
        $info['title'] = strip_illegal_tags($info['title']);
        $info['content'] = strip_illegal_tags($info['content']);
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
        $info['uid'] = 0;
        $info['time'] = time();
        $info['content'] = strip_illegal_tags($info['content']);
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