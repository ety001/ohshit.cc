<?php
//去除编辑器中输入的特殊标记
function strip_illegal_tags($t){
    return strip_tags($t,'<p><a><div><ul><li><ol><img><span><h1><h2><h3><h4><h5><h6><embed>');
}