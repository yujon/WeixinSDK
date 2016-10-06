<?php
/*
 * 单一入口
*
*/
	namespace WeixinSDK\PHPSDK;
	//设置输出文本的编码
	header("Content-Type:text/html;Charset=utf-8");
	
	//设置自动加载函数，以便自动加载工具类
	require_once "classes/Autoloader.class.php";
	spl_autoload_register("Classes\\Autoloader::autoload");
	//引入工具函数文件,存放一些基础的常用函数;工具类需要时才加载
	require_once 'functions/func.inc.php';
	
	//定义目录
	$GLOBALS['root'] = dirname(__FILE__);
	//定义应用入口
	$start = strpos($_SERVER['PHP_SELF'], "index.php");
	$length = $start+9;
	$GLOBALS['app'] = substr($_SERVER['PHP_SELF'], 0,$length);
	
	//菜单管理 
// 	$menu = \Controls\Menu::getInstance();
// 	$menu->create();
// 	$menu->get();
//  $menu->delete();
    
	//素材管理
// 	$material =\Controls\Material::getInstance();
// 	$file_info = array('filename' => 'icon.jpg', //国片相对于网站根目录的路径
// 			'content-type' => 'image/jpg', //文件类型
// 			'filelength' => '71' //图文大小
// 	);
// 	$media_id = $material->addTemMaterial(0, $file_info);
// 	$material->getTemMaterial(0, $media_id);
//  $media_id = $material->addForMaterial(0,$file_info);
//     $article = array(
//     		array(
//     			"图文1",$media_id,"yujon","上传图文测试",1,"这是图文1的内容","www.baidu.com"
//     		)
//     );
// 	$material->addforNews(4,$article);	
// 	$material->getForMaterial(0,$media_id);
// 	$material->delForMaterial($media_id);
//  $num = $material->getMaterialNum();
//  print_r($num);
//  $list = $material->getMaterialList(0, 0, 5);
//  print_r($list);

	//账户管理
	//  $account = \Controls\Account::getInstance();
	//  $account->getForQrCode(3000);
	//  $account->getTemQrCode(2000);
	
    //用户管理
//  $user = \Controls\User::getInstance();
//  $user->createGroup("家人");
// 	print_r($user->getUserList());

	//群发管理
	$response = \Controls\Response::getInstance();
//  $response->sendToAll(true,"text","这是一个群发文本",true,false,$openids);
    
	
	//消息管理
	if(isset($_SERVER['PATH_INFO'])){  //微信服务器传过来的没有此值
		\Classes\RewriteURL::parse();   //解析URL,也可以通用设置apache服务器进行重定向
	}else{
		$_GET["m"] = "Index";
		$_GET["a"] = 'index';
	}
	$className = "Controls\\".ucfirst($_GET['m']);
	$control = $className::getInstance();
	$control->run();  //运行某操作
