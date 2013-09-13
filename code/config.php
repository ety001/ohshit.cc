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
    ),
    'launch' => array( // 加入挂靠点，以便开始使用Url_ReWrite的功能
        'router_prefilter' => array( 
            array('spUrlRewrite', 'setReWrite'),  // 对路由进行挂靠，处理转向地址
        ),
         'function_url' => array(
            array("spUrlRewrite", "getReWrite"),  // 对spUrl进行挂靠，让spUrl可以进行Url_ReWrite地址的生成
        ),
    ),
    'ext' => array(
        'spUrlRewrite' => array(
            'suffix' => '', 
            'sep' => '/', 
            'map' => array( 
                'l' => 'main@l',
                'savePost' => 'main@savePost',
                'addPost' => 'main@addPost',
                'saveComment' => 'main@saveComment',
                'os' => 'main@index',
                '@' => 'main@index'         
            ),
            'args' => array(
                'l' => array('id','p'),
                'os' => array('p')
            )
        ),
        //微信扩展设置 
        'spWeiXin' => array( 
            'TOKEN' => $_SERVER['OHSHIT_WX'] //微信通信密钥，后台设置 
        ) 
    )
);