<?php
class libPosts extends spModel
{
    var $pk = "id"; // 数据表的主键
    var $table = "posts"; // 数据表的名称
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
}