<?php
require('./config.php');
$spConfig['mode'] = 'debug';
require(SP_PATH."/SpeedPHP.php");
import(APP_PATH.'/include/functions.php');//载入自定义函数库
spRun();