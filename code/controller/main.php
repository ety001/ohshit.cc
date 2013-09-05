<?php
class main extends spController
{
	function index(){
        $p = (int)$this->spArgs('p',1);
		$postsObj = spClass('libPosts');
        $allPosts = $postsObj->spLinker()->spPager($p, 10)->findAll(false,'`update_time` desc, `time` desc');
        $pageInfo = $postsObj->spPager()->getPager();
        $this->allPosts = $allPosts;
        $this->pageInfo = $pageInfo;
        $this->display('index.html');
	}

    function l(){
        $id = (int)$this->spArgs('id');
        $p = (int)$this->spArgs('p',1);
        if($id){
            $conditions['id'] = $id;
        } else {
            $this->error('错误的文章id',spUrl('main','index'));
            return;
        }
        $postsObj = spClass('libPosts');
        $post = $postsObj->find($conditions);
        if(!$post){
            $this->error('错误的文章id',spUrl('main','index'));
            return;
        }
        $postsObj->updateField($conditions,'hits',$post['hits']+1);
        $this->post = $post;
        $commentsObj = spClass('libComments');
        $this->allComments = $commentsObj->spPager($p, 50)->findAll(array('post_id'=>$id),'time asc');
        $this->cpager = $commentsObj->spPager()->getPager();
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
        $info['update_time'] = time();
        $info['uid'] = 0;
        $info['hits'] = 0;
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
        $postsObj = spClass('libPosts');
        $verifier = $commentsObj->spVerifier($info);
        if( false == $verifier){
            if($commentsObj->create($info)){
                $postsObj->updateTime($info['post_id']);
                $this->success('发布成功',spUrl('main','l',array('id'=>$info['post_id'])));
            } else {
                $this->error('发布失败');
            }
        } else {
            $msg = array_pop($verifier);
            $this->error(array_pop($msg));
        }

    }

    function TTT(){
        $postsObj = spClass('libPosts');
        $allPosts = $postsObj->spLinker()->findAll();
        foreach ($allPosts as $key => $value) {
            $t = array_pop($value['comments']);
            if($t){
                $postsObj->updateField(array('id'=>$t['post_id']),'update_time',$t['time']);
            }
        }
    }
}