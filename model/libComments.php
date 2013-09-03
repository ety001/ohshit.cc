<?php
class libComments extends spModel
{
    var $pk = "id"; // 数据表的主键
    var $table = "comments"; // 数据表的名称
    var $verifier = array(
                        "rules" => array( // 规则
                            'content' => array(
                                'notnull' => TRUE
                            )
                        ),
                        "messages" => array( // 规则
                            'content' => array(
                                'notnull' => '评论内容不能为空'
                            )
                        )
                    );
}