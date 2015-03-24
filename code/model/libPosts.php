<?php
class libPosts extends spModel
{
    var $pk = "id"; // 数据表的主键
    var $table = "posts"; // 数据表的名称
    var $linker = array(
                    array(
                        'type' => 'hasmany',   // 一对多关联
                        'map' => 'comments',    // 关联的标识
                        'mapkey' => 'id', 
                        'fclass' => 'libComments',
                        'fkey' => 'post_id',
                        'enabled' => true
                    )
                );
    var $verifier = array(
                        "rules" => array( // 规则
                            'title' => array(
                                'notnull' => TRUE
                            ),
                            'content' => array(
                                'notnull' => TRUE
                            )
                        ),
                        "messages" => array( // 规则
                            'title' => array(
                                'notnull' => '标题不能空'
                            ),
                            'content' => array(
                                'notnull' => '内容不能为空'
                            )
                        )
                    );
    public function updateTime($postID=''){
        if(!$postID)return;
        $conditions['id'] = $postID;
        $now = time();
        if($this->updateField($conditions,'update_time',$now)){
            return true;
        } else {
            return false;
        }
    }
    
    public function rmdata($postID=''){
        if(!$postID)return;
        $conditions['id'] = $postID;
        $conditions_comment['post_id'] = $postID;
        $this->delete($conditions);
        spClass('libComments')->delete($conditions_comment);
    } 
}
