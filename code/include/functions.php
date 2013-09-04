<?php
//去除编辑器中输入的特殊标记
function strip_illegal_tags($t){
    return strip_tags($t,'<p><a><div><ul><li><ol><img><span><h1><h2><h3><h4><h5><h6><embed>');
}

/*
    * Name: time_ago 
    * Purpose: 将时间戳专为距当前时间的表现形式 
    * 1分钟内按秒 
    * 1小时内按分钟显示 
    * 1天内按时分显示 
    * 3天内以昨天，前天显示 
    * 超过3天显示具体日期 
    * 
    * @author Peter Pan 
    * @param int $time input int 
*/ 
function time_ago($time) { 
    $time_deff = time() - $time; 
    $retrun = ''; 
    if ($time_deff >= 259200) { 
        $retrun = date('Y-m-d H:i', $time); 
    } else if ($time_deff >= 172800) { 
        $retrun = "前天 " . date('H:i', $time); 
    } else if ($time_deff >= 86400) { 
        $retrun = "昨天" . date('H:i', $time); 
    } else if ($time_deff >= 3600) { 
        $hour = intval($time_deff / 3600); 
        $minute = intval(($time_deff % 3600) / 60); 
        $retrun = $hour . '小时'; 
        if ($minute > 0) { 
            $retrun .= $minute . '分钟'; 
        } 
        $retrun .= '前'; 
    } else if ($time_deff >= 60) { 
        $minute = intval($time_deff / 60); 
        $second = $time_deff % 60; 
        $retrun = $minute . '分'; 
        if ($second > 0) { 
            $retrun .= $second . '秒'; 
        } 
        $retrun .= '前'; 
    }else{ 
        $retrun = $time_deff.'秒前'; 
    } 
    return $retrun; 
} 