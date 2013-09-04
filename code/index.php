<?php
define("APP_PATH",dirname(__FILE__));
define("SP_PATH",dirname(__FILE__).'/SpeedPHP');
$spConfig = array(
    'db' => array( // 数据库设置
        'driver' => 'mysqli',
        'host' => $_SERVER['OHSHIT_HOST'],  // 数据库地址
        'login' => $_SERVER['OHSHIT_USER'], // 数据库用户名
        'password' => $_SERVER['OHSHIT_PASS'], // 数据库密码
        'database' => $_SERVER['OHSHIT_DB'], // 数据库的库名称
        'prefix' => $_SERVER['OHSHIT_PREFIX'] // 表前缀
    ),
    'view' => array( // 视图配置
        'enabled' => TRUE, // 开启视图
        'config' =>array(
            'template_dir' => APP_PATH.'/tpl', // 模板目录
        ),
        'engine_name' => 'speedy', // 模板引擎的类名称
        'engine_path' => SP_PATH.'/Drivers/speedy.php', // 模板引擎主类路径
    )
);
require(SP_PATH."/SpeedPHP.php");
import(APP_PATH.'/include/functions.php');//载入自定义函数库
spRun();