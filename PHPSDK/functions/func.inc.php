<?php


/**
 * 用于在控制器中进行位置重定向
 * @param	string	$path	用于设置重定向的位置
 * @param	string	$args 	用于重定向到新位置后传递参数
 *
 * $this->redirect("index")  /当前模块/index
 * $this->redirect("user/index") /user/index
 * $this->redirect("user/index", 'page/5') /user/index/page/5
 */
function redirect($path, $args=""){
	$path=trim($path, "/");
	if($args!="")
		$args="/".trim($args, "/");
	if(strstr($path, "/")){
		$url=$path.$args;
	}else{
		$url=$_GET["m"]."/".$path.$args;
	}

	$uri=B_APP.'/'.$url;
	//使用js跳转前面可以有输出
	echo '<script>';
	echo 'location="'.$uri.'"';
	echo '</script>';
}


/**
 * 写错误日志
 */
function log($log){
	$log ="[".__FILE__."][".__LINE__."]:".$log;
	$fileName = date("y_m_d");
	$logPath = $GLOBALS['root']."/log/".$fileName.".txt";
	file_put_contents($logPath,$log, FILE_APPEND);
}
