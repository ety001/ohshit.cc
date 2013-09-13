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
//获取设备类型
function get_device_type(){
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $pattern = '/ipad|iphone|android/';
    preg_match($pattern, $agent, $matches);
    if($matches[0]){
        return false;
    } else {
        return true;
    }
}

/* 
Utf-8、gb2312都支持的汉字截取函数 
cut_str(字符串, 截取长度, 开始长度, 编码); 
编码默认为 utf-8 
开始长度默认为 0 
*/
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8') 
{ 
    if($code == 'UTF-8'){ 
        $pa ="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string); if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
        return join('', array_slice($t_string[0], $start, $sublen)); 
    } else {
        $start = $start*2; 
        $sublen = $sublen*2; 
        $strlen = strlen($string); 
        $tmpstr = '';
        for($i=0; $i<$strlen; $i++) { 
            if($i>=$start && $i<($start+$sublen)) { 
                if(ord(substr($string, $i, 1))>129) { 
                    $tmpstr.= substr($string, $i, 2); 
                } else { 
                    $tmpstr.= substr($string, $i, 1); 
                } 
            } 
            if(ord(substr($string, $i, 1))>129) $i++; 
        } 
        if(strlen($tmpstr)<$strlen ) $tmpstr.= "..."; 
        return $tmpstr; 
    } 
}